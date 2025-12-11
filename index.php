<?php

/**
 * College Performance Prediction System
 * 
 * Evaluates 3rd-year student performance across multiple subjects
 * with attendance consideration and graduation probability prediction.
 */

// ==================== CONFIGURATION ====================

/**
 * Subject configuration for the system
 * Subjects that 3rd-year students take
 */
$SUBJECTS = [
    'client_tech' => 'Client Technologies',
    'networking' => 'Networking 2',
    'web_design' => 'Principles of Web Design',
    'system_integration' => 'System Integration',
    'programming' => 'Integrative Programming Technologies 2'
];

/**
 * Grade Point Equivalence Table
 * Converts percentage scores to grade points with descriptive labels
 */
$GRADE_EQUIVALENCE = [
    'Excellent' => ['min' => 99, 'max' => 100, 'grade' => 1.00],
    'Very Good' => ['min' => 90, 'max' => 98, 'grade' => 1.50],  // 95-98% = 1.25, 90-94% = 1.50
    'Good' => ['min' => 80, 'max' => 89, 'grade' => 2.00],       // 85-89% = 1.75, 80-84% = 2.00
    'Satisfactory' => ['min' => 70, 'max' => 79, 'grade' => 2.25], // 75-79% = 2.25, 70-74% = 2.50
    'Passing' => ['min' => 60, 'max' => 69, 'grade' => 2.75],     // 65-69% = 2.75, 60-64% = 3.00
    'Failing' => ['min' => 0, 'max' => 59, 'grade' => 5.00],
];

/**
 * Detailed Grade Conversion Table
 * Maps specific percentage ranges to exact grade points
 */
$DETAILED_GRADE_MAP = [
    [99, 100, 1.00, 'Excellent'],
    [95, 98, 1.25, 'Very Good'],
    [90, 94, 1.50, 'Very Good'],
    [85, 89, 1.75, 'Good'],
    [80, 84, 2.00, 'Good'],
    [75, 79, 2.25, 'Satisfactory'],
    [70, 74, 2.50, 'Satisfactory'],
    [65, 69, 2.75, 'Passing'],
    [60, 64, 3.00, 'Passing'],
    [0, 59, 5.00, 'Failing'],
];

// ==================== UTILITY FUNCTIONS ====================

/**
 * Get grade description from grade point
 * 
 * @param float $gradePoint Grade point (1.00-5.00)
 * @return string Description of the grade
 */
function getGradeDescription($gradePoint)
{
    $gradePoint = round($gradePoint, 2);

    switch ($gradePoint) {
        case 1.00:
            return 'Excellent (99-100%)';
        case 1.25:
            return 'Very Good (95-98%)';
        case 1.50:
            return 'Very Good (90-94%)';
        case 1.75:
            return 'Good (85-89%)';
        case 2.00:
            return 'Good (80-84%)';
        case 2.25:
            return 'Satisfactory (75-79%)';
        case 2.50:
            return 'Satisfactory (70-74%)';
        case 2.75:
            return 'Passing (65-69%)';
        case 3.00:
            return 'Passing (60-64%)';
        case 5.00:
            return 'Failing (<60%)';
        default:
            return 'Invalid Grade';
    }
}

/**
 * Get color class for grade point
 * 
 * @param float $gradePoint Grade point (1.00-5.00)
 * @return string Tailwind color classes
 */
function getGradeColor($gradePoint)
{
    $gradePoint = round($gradePoint, 2);

    switch ($gradePoint) {
        case 1.00:
        case 1.25:
        case 1.50:
            return 'text-green-600 bg-green-50';  // Excellent, Very Good
        case 1.75:
        case 2.00:
        case 2.25:
        case 2.50:
            return 'text-blue-600 bg-blue-50';    // Good, Satisfactory
        case 2.75:
        case 3.00:
            return 'text-yellow-600 bg-yellow-50'; // Passing
        case 5.00:
            return 'text-red-600 bg-red-50';      // Failing
        default:
            return 'text-gray-600 bg-gray-50';
    }
}

/**
 * Check if student needs retake (has 5.00 on any subject)
 * 
 * @param array $grades Array of subject grades
 * @return bool True if any grade is 5.00
 */
function needsRetake($grades)
{
    foreach ($grades as $grade) {
        if (round($grade, 2) === 5.00) {
            return true;
        }
    }
    return false;
}

/**
 * Calculate final grade from all subject grades
 * 
 * @param array $grades Array of subject grades
 * @param float $attendanceGrade Converted attendance grade
 * @return float Average grade
 */
function calculateFinalGrade($grades, $attendanceGrade)
{
    $allGrades = array_merge($grades, [$attendanceGrade]);
    return array_sum($allGrades) / count($allGrades);
}

/**
 * Predict student status based on final grade
 * 
 * RULES:
 * - Above 3.00 → FAILED
 * - 2.75 to 2.00 → PASSED
 * - 1.95 to 1.00 → EXCELLENT
 * 
 * @param float $finalGrade Computed final grade
 * @return string Student status
 */
function predictStatus($finalGrade)
{
    if ($finalGrade > 3.00) {
        return "FAILED";
    } elseif ($finalGrade >= 2.00 && $finalGrade <= 2.75) {
        return "PASSED";
    } elseif ($finalGrade >= 1.00 && $finalGrade < 2.00) {
        return "EXCELLENT";
    }
    return "INVALID";
}

/**
 * Get color code for display status
 * 
 * @param string $status Student status
 * @return string Color class
 */
function getStatusColor($status)
{
    switch ($status) {
        case "EXCELLENT":
            return "text-green-600";
        case "PASSED":
            return "text-blue-600";
        case "FAILED":
            return "text-red-600";
        default:
            return "text-gray-600";
    }
}

/**
 * Get background color for status
 * 
 * @param string $status Student status
 * @return string Background color class
 */
function getStatusBgColor($status)
{
    switch ($status) {
        case "EXCELLENT":
            return "bg-green-100";
        case "PASSED":
            return "bg-blue-100";
        case "FAILED":
            return "bg-red-100";
        default:
            return "bg-gray-100";
    }
}

/**
 * Get neon class for final grade display
 * 
 * @param string $status Student status
 * @return string Neon CSS class
 */
function getNeonClass($status)
{
    switch ($status) {
        case "EXCELLENT":
            return "neon-green";
        case "PASSED":
            return "neon-blue";
        case "FAILED":
            return "neon-red";
        default:
            return "neon-gray";
    }
}

/**
 * Calculate graduation probability based on average
 * 
 * @param float $averageGrade Student's average grade
 * @param int $semestersCompleted Semesters completed (for 3rd year: 5)
 * @return float Graduation probability percentage (0-100)
 */
function calculateGraduationProbability($averageGrade, $semestersCompleted = 5)
{
    // Base probability calculation
    $baseProbability = 100; // Start at 100%

    // Deduct based on grade
    if ($averageGrade > 3.0) {
        $baseProbability -= 50; // High failure risk
    } elseif ($averageGrade > 2.75) {
        $baseProbability -= 20; // Moderate risk
    } elseif ($averageGrade > 2.0) {
        $baseProbability -= 5;  // Low risk
    }

    // Bonus for excellent performance
    if ($averageGrade < 2.0) {
        $baseProbability += 15;
    }

    // Adjust based on semesters completed (more semesters = better prediction)
    $semesterBonus = min($semestersCompleted / 8 * 10, 10);
    $baseProbability += $semesterBonus;

    return max(0, min(100, $baseProbability)); // Ensure 0-100 range
}

/**
 * Validate grade input (valid grade points)
 * 
 * @param float $grade Grade point to validate
 * @return bool True if valid
 */
function isValidGrade($grade)
{
    $validGrades = [1.00, 1.25, 1.50, 1.75, 2.00, 2.25, 2.50, 2.75, 3.00, 5.00];
    return in_array(round($grade, 2), $validGrades);
}

/**
 * Validate attendance input (0 to 100)
 * 
 * @param float $attendance Attendance to validate
 * @return bool True if valid
 */
function isValidAttendance($attendance)
{
    return is_numeric($attendance) && $attendance >= 0 && $attendance <= 100;
}

/**
 * Get approximate percentage for a grade point (used for progress UI)
 * @param float $gradePoint
 * @return float approximate percentage
 */
function getApproxPercent($gradePoint)
{
    $g = round(floatval($gradePoint), 2);
    switch ($g) {
        case 1.00:
            return 99.5;
        case 1.25:
            return 96.5;
        case 1.50:
            return 92.0;
        case 1.75:
            return 87.0;
        case 2.00:
            return 82.0;
        case 2.25:
            return 77.0;
        case 2.50:
            return 72.0;
        case 2.75:
            return 67.0;
        case 3.00:
            return 62.0;
        case 5.00:
            return 40.0; // failing approximate
        default:
            return 0;
    }
}

// ==================== MAIN PROCESSING ====================

$processedData = null;
$errors = [];
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($submitted) {
    // Validate and collect subject grades
    $grades = [];
    $subjectNames = [];

    foreach ($SUBJECTS as $key => $label) {
        $fieldName = 'grade_' . $key;
        if (!isset($_POST[$fieldName])) {
            $errors[] = "$label grade is required";
            continue;
        }

        $grade = floatval($_POST[$fieldName]);
        if (!isValidGrade($grade)) {
            $errors[] = "$label must be between 1.0 and 5.0";
            continue;
        }

        $grades[$key] = $grade;
        $subjectNames[$key] = $label;
    }

    // Validate attendance
    if (!isset($_POST['attendance'])) {
        $errors[] = "Attendance is required";
    } else {
        $attendance = floatval($_POST['attendance']);
        if (!isValidAttendance($attendance)) {
            $errors[] = "Attendance must be between 0 and 100";
        }
    }

    // Process if no errors
    if (empty($errors) && count($grades) === count($SUBJECTS)) {
        // Convert all grades to floats and get descriptions
        $gradeObjects = [];
        $totalGradePoints = 0;

        foreach ($grades as $key => $gradePoint) {
            $gradePoint = floatval($gradePoint);
            $description = getGradeDescription($gradePoint);
            $gradeObjects[$key] = [
                'grade' => $gradePoint,
                'description' => $description
            ];
            $totalGradePoints += $gradePoint;
        }

        // Convert attendance grade point
        $attendanceGradePoint = floatval($attendance);
        $attendanceDescription = getGradeDescription($attendanceGradePoint);
        $totalGradePoints += $attendanceGradePoint;

        // Calculate final grade (average of all grade points)
        $finalGrade = $totalGradePoints / 6; // 5 subjects + attendance
        $status = predictStatus($finalGrade);
        $graduationProb = calculateGraduationProbability($finalGrade);
        $needsRetake = needsRetake($grades);

        $processedData = [
            'grades' => $grades,
            'gradeObjects' => $gradeObjects,
            'subjectNames' => $subjectNames,
            'attendance' => $attendance,
            'attendanceGrade' => $attendanceGradePoint,
            'attendanceDescription' => $attendanceDescription,
            'finalGrade' => $finalGrade,
            'status' => $status,
            'graduationProb' => $graduationProb,
            'needsRetake' => $needsRetake,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        // Save to history
        $historyFile = 'college_history.json';
        $history = [];
        if (file_exists($historyFile)) {
            $history = json_decode(file_get_contents($historyFile), true) ?? [];
        }
        array_unshift($history, $processedData);
        $history = array_slice($history, 0, 100); // Keep last 100 records
        file_put_contents($historyFile, json_encode($history, JSON_PRETTY_PRINT));

        // Redirect to prevent form resubmission on refresh
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Load history for dashboard
$allPredictions = [];
if (file_exists('college_history.json')) {
    $allPredictions = json_decode(file_get_contents('college_history.json'), true) ?? [];
}

// Ensure history is sorted by timestamp descending (newest first)
if (!empty($allPredictions)) {
    usort($allPredictions, function ($a, $b) {
        $ta = strtotime($a['timestamp'] ?? '1970-01-01 00:00:00');
        $tb = strtotime($b['timestamp'] ?? '1970-01-01 00:00:00');
        return $tb <=> $ta;
    });
}

// Get the most recent evaluation to display (from history)
$mostRecentEvaluation = null;
if (!empty($allPredictions)) {
    $mostRecentEvaluation = $allPredictions[0];
}

// Calculate statistics
$stats = [
    'total' => count($allPredictions),
    'excellent' => 0,
    'passed' => 0,
    'failed' => 0,
    'avgGrade' => 0,
    'avgAttendance' => 0
];

foreach ($allPredictions as $pred) {
    if ($pred['status'] === 'EXCELLENT') $stats['excellent']++;
    elseif ($pred['status'] === 'PASSED') $stats['passed']++;
    elseif ($pred['status'] === 'FAILED') $stats['failed']++;

    $stats['avgGrade'] += $pred['finalGrade'];
    $stats['avgAttendance'] += $pred['attendance'];
}

if ($stats['total'] > 0) {
    $stats['avgGrade'] /= $stats['total'];
    $stats['avgAttendance'] /= $stats['total'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Prediction System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="student.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Neon text effects for final grade */
        .neon-red {
            color: #fd5f5fff;
            text-shadow: 0 0 5px #ff0000, 0 0 10px #ff0000;
        }

        .neon-green {
            color: #038503ff;
            text-shadow: 0 0 10px #00ff00, 0 0 20px #00ff00, 0 0 30px #00ff00, 0 0 40px #00ff00;
        }

        .neon-blue {
            color: #55a8fcff;
            text-shadow: 0 0 5px #0080ff, 0 0 10px #0080ff;
        }

        .neon-gray {
            color: #505050ff;
            text-shadow: 0 0 5px #888888, 0 0 10px #888888;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen" id="appBody">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-graduation-cap text-3xl"></i>
                    <h1 class="text-3xl font-bold">Student Performance Prediction System</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm opacity-90">3rd Year Student Evaluation</div>
                    <button onclick="toggleDarkMode()" id="themeToggle" class="bg-yellow-400 hover:bg-yellow-500 text-gray-800 font-bold py-2 px-4 rounded-lg transition flex items-center space-x-2 shadow-md" title="Toggle Dark/Light Mode">
                        <i class="fas fa-moon"></i>
                        <span>Dark Mode</span>
                    </button>
                    <button onclick="clearHistory()" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg transition flex items-center space-x-2 shadow-md">
                        <i class="fas fa-trash-alt"></i>
                        <span>Clear History</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Total Evaluations</p>
                        <p class="text-3xl font-bold text-blue-600"><?php echo $stats['total']; ?></p>
                    </div>
                    <i class="fas fa-chart-bar text-4xl text-blue-100"></i>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Excellent</p>
                        <p class="text-3xl font-bold text-green-600"><?php echo $stats['excellent']; ?></p>
                    </div>
                    <i class="fas fa-star text-4xl text-green-100"></i>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Passed</p>
                        <p class="text-3xl font-bold text-blue-600"><?php echo $stats['passed']; ?></p>
                    </div>
                    <i class="fas fa-check-circle text-4xl text-blue-100"></i>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Failed</p>
                        <p class="text-3xl font-bold text-red-600"><?php echo $stats['failed']; ?></p>
                    </div>
                    <i class="fas fa-times-circle text-4xl text-red-100"></i>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Avg Grade</p>
                        <p class="text-3xl font-bold text-purple-600"><?php echo number_format($stats['avgGrade'], 2); ?></p>
                    </div>
                    <i class="fas fa-calculator text-4xl text-purple-100"></i>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Form Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fas fa-edit mr-3 text-indigo-600"></i>Student Evaluation Form
                    </h2>

                    <?php if (!empty($errors)): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                            <h3 class="font-bold mb-2">Please fix the following errors:</h3>
                            <ul class="list-disc list-inside">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-6">
                        <!-- Subject Grades -->
                        <div class="border-t-2 border-gray-200 pt-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Subject Grades</h3>
                            <p class="text-sm text-gray-600 mb-4">Select grade points for each subject</p>
                            <p class="text-xs text-gray-500 mb-4">
                                <strong>Grade Points:</strong> 1.00 (Excellent) | 1.25 (Very Good) | 1.50 (Very Good) | 1.75 (Good) | 2.00 (Good) | 2.25 (Satisfactory) | 2.50 (Satisfactory) | 2.75 (Passing) | 3.00 (Passing) | 5.00 (Failing)
                            </p>

                            <div class="space-y-4">
                                <?php foreach ($SUBJECTS as $key => $label): ?>
                                    <div class="flex items-center justify-between">
                                        <label class="text-gray-700 font-semibold flex-1">
                                            <i class="fas fa-book text-indigo-500 mr-2"></i><?php echo $label; ?>
                                        </label>
                                        <select name="grade_<?php echo $key; ?>"
                                            class="w-32 px-3 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 transition"
                                            required>
                                            <option value="">Select Grade</option>
                                            <option value="1.00" <?php echo (isset($_POST['grade_' . $key]) && $_POST['grade_' . $key] == '1.00') ? 'selected' : ''; ?>>1.00 - Excellent</option>
                                            <option value="1.25" <?php echo (isset($_POST['grade_' . $key]) && $_POST['grade_' . $key] == '1.25') ? 'selected' : ''; ?>>1.25 - Very Good</option>
                                            <option value="1.50" <?php echo (isset($_POST['grade_' . $key]) && $_POST['grade_' . $key] == '1.50') ? 'selected' : ''; ?>>1.50 - Very Good</option>
                                            <option value="1.75" <?php echo (isset($_POST['grade_' . $key]) && $_POST['grade_' . $key] == '1.75') ? 'selected' : ''; ?>>1.75 - Good</option>
                                            <option value="2.00" <?php echo (isset($_POST['grade_' . $key]) && $_POST['grade_' . $key] == '2.00') ? 'selected' : ''; ?>>2.00 - Good</option>
                                            <option value="2.25" <?php echo (isset($_POST['grade_' . $key]) && $_POST['grade_' . $key] == '2.25') ? 'selected' : ''; ?>>2.25 - Satisfactory</option>
                                            <option value="2.50" <?php echo (isset($_POST['grade_' . $key]) && $_POST['grade_' . $key] == '2.50') ? 'selected' : ''; ?>>2.50 - Satisfactory</option>
                                            <option value="2.75" <?php echo (isset($_POST['grade_' . $key]) && $_POST['grade_' . $key] == '2.75') ? 'selected' : ''; ?>>2.75 - Passing</option>
                                            <option value="3.00" <?php echo (isset($_POST['grade_' . $key]) && $_POST['grade_' . $key] == '3.00') ? 'selected' : ''; ?>>3.00 - Passing</option>
                                            <option value="5.00" <?php echo (isset($_POST['grade_' . $key]) && $_POST['grade_' . $key] == '5.00') ? 'selected' : ''; ?>>5.00 - Failing</option>
                                        </select>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Attendance -->
                        <div class="border-t-2 border-gray-200 pt-6">
                            <label class="block text-gray-800 font-bold mb-2">
                                <i class="fas fa-calendar-check text-indigo-500 mr-2"></i>Attendance Grade
                            </label>
                            <p class="text-sm text-gray-600 mb-3">Select attendance grade point</p>
                            <select name="attendance"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 transition"
                                required>
                                <option value="">Select Grade Point</option>
                                <option value="1.00" <?php echo (isset($_POST['attendance']) && $_POST['attendance'] == '1.00') ? 'selected' : ''; ?>>1.00 - Excellent</option>
                                <option value="1.25" <?php echo (isset($_POST['attendance']) && $_POST['attendance'] == '1.25') ? 'selected' : ''; ?>>1.25 - Very Good</option>
                                <option value="1.50" <?php echo (isset($_POST['attendance']) && $_POST['attendance'] == '1.50') ? 'selected' : ''; ?>>1.50 - Very Good</option>
                                <option value="1.75" <?php echo (isset($_POST['attendance']) && $_POST['attendance'] == '1.75') ? 'selected' : ''; ?>>1.75 - Good</option>
                                <option value="2.00" <?php echo (isset($_POST['attendance']) && $_POST['attendance'] == '2.00') ? 'selected' : ''; ?>>2.00 - Good</option>
                                <option value="2.25" <?php echo (isset($_POST['attendance']) && $_POST['attendance'] == '2.25') ? 'selected' : ''; ?>>2.25 - Satisfactory</option>
                                <option value="2.50" <?php echo (isset($_POST['attendance']) && $_POST['attendance'] == '2.50') ? 'selected' : ''; ?>>2.50 - Satisfactory</option>
                                <option value="2.75" <?php echo (isset($_POST['attendance']) && $_POST['attendance'] == '2.75') ? 'selected' : ''; ?>>2.75 - Passing</option>
                                <option value="3.00" <?php echo (isset($_POST['attendance']) && $_POST['attendance'] == '3.00') ? 'selected' : ''; ?>>3.00 - Passing</option>
                                <option value="5.00" <?php echo (isset($_POST['attendance']) && $_POST['attendance'] == '5.00') ? 'selected' : ''; ?>>5.00 - Failing</option>
                            </select>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-3 px-4 rounded-lg transition transform hover:scale-105 shadow-md">
                            <i class="fas fa-calculator mr-2"></i>Evaluate
                        </button>
                    </form>
                </div>
            </div>

            <!-- Results Section -->
            <div>
                <?php if ($mostRecentEvaluation): ?>
                    <div class="bg-white rounded-lg shadow-lg p-8 space-y-6">
                        <div class="text-center">
                            <?php if ($mostRecentEvaluation['needsRetake']): ?>
                                <div class="bg-red-100 border-2 border-red-500 text-red-700 p-6 rounded-lg mb-4 shadow-lg animate-pulse">
                                    <div class="flex items-center justify-center mb-3">
                                        <i class="fas fa-exclamation-triangle text-4xl mr-3"></i>
                                        <h2 class="text-3xl font-bold">POSSIBLE RETAKE</h2>
                                    </div>
                                    <p class="text-base font-semibold mb-3">⚠️ Student Action Required</p>
                                    <p class="text-sm mb-4">Student has a failing grade (5.00) in at least one subject and requires remedial action.</p>
                                    <div class="bg-red-200 border-l-4 border-red-600 p-3 text-left rounded">
                                        <p class="text-xs font-bold mb-2">Recommended Actions:</p>
                                        <ul class="text-xs space-y-1 list-disc list-inside">
                                            <li>Schedule meeting with instructor</li>
                                            <li>Complete remedial coursework</li>
                                            <li>Register for retake exam</li>
                                        </ul>
                                    </div>
                                </div>
                            <?php else: ?>
                                <h2 class="text-2xl font-bold text-gray-800 mb-4">Evaluation Result</h2>
                            <?php endif; ?>

                            <!-- Final Status -->
                            <?php
                            $statusLabel = $mostRecentEvaluation['status'];
                            $finalGradeVal = floatval($mostRecentEvaluation['finalGrade']);
                            // Advice message for PASSED and nearby grades (improves UX for values like 2.42)
                            $statusAdvice = '';
                            if ($statusLabel === 'PASSED') {
                                if ($finalGradeVal <= 2.25) {
                                    $statusAdvice = 'Good performance — consider aiming for Excellent with small improvements.';
                                } elseif ($finalGradeVal <= 2.50) {
                                    $statusAdvice = 'Solid performance — minor improvements recommended in weaker subjects.';
                                } else {
                                    $statusAdvice = 'Passing — review areas of weakness to further improve.';
                                }
                            } elseif ($statusLabel === 'EXCELLENT') {
                                $statusAdvice = 'Excellent work — keep up the outstanding performance!';
                            } elseif ($statusLabel === 'FAILED') {
                                $statusAdvice = 'Failed — please follow recommended remedial actions above.';
                            }

                            $statusContainerClasses = getStatusBgColor($statusLabel) . ' p-6 rounded-lg border-2 border-current mb-6';
                            if ($statusLabel === 'PASSED') $statusContainerClasses .= ' passed-highlight';
                            $neonClass = getNeonClass($statusLabel);
                            ?>

                            <div class="<?php echo $statusContainerClasses; ?>">
                                <div class="<?php echo getStatusColor($statusLabel); ?> text-5xl font-bold mb-2 status-label">
                                    <?php echo $statusLabel; ?>
                                </div>
                                <p class="final-grade text-sm"><span class="font-bold <?php echo $neonClass; ?>">Final Grade: <?php echo number_format($finalGradeVal, 2); ?></span></p>
                                <?php if (!empty($statusAdvice)): ?>
                                    <p class="mt-2 text-sm italic text-gray-700 status-advice"><?php echo $statusAdvice; ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Grade Summary -->
                        <div class="border-t-2 border-gray-200 pt-4">
                            <h3 class="font-bold text-gray-800 mb-3">Subject Grades</h3>
                            <div class="space-y-3">
                                <?php foreach ($mostRecentEvaluation['grades'] as $key => $gradePoint): ?>
                                    <?php
                                    $gradeObj = $mostRecentEvaluation['gradeObjects'][$key];
                                    $colorClass = getGradeColor($gradeObj['grade']);
                                    $approxPercent = getApproxPercent($gradeObj['grade']);
                                    ?>
                                    <div class="interactive-subject flex justify-between items-start p-3 <?php echo $colorClass; ?> rounded border-l-4 border-current" tabindex="0" role="button" aria-expanded="false" aria-controls="details-<?php echo $key; ?>">
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <span class="font-semibold"><?php echo $mostRecentEvaluation['subjectNames'][$key]; ?></span>
                                                    <div class="text-xs opacity-75 mt-1"><?php echo $gradeObj['description']; ?></div>
                                                </div>
                                                <div class="text-right ml-4">
                                                    <span class="font-bold text-lg"><?php echo number_format($gradeObj['grade'], 2); ?></span>
                                                    <div class="text-xs opacity-75">≈ <?php echo number_format($approxPercent, 1); ?>%</div>
                                                </div>
                                            </div>

                                            <!-- Hidden details (toggleable) -->
                                            <div id="details-<?php echo $key; ?>" class="subject-details mt-3 hidden text-xs text-gray-700 dark:text-gray-200">
                                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                                    <div class="progress-bar bg-indigo-500 h-2" style="width:0%;" data-target="<?php echo $approxPercent; ?>"></div>
                                                </div>
                                                <div class="mt-2 text-xs opacity-80">This bar shows approximate position relative to top score (1.00). Hover or press Enter/Space to toggle details.</div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Attendance Summary -->
                        <div class="border-t-2 border-gray-200 pt-4">
                            <h3 class="font-bold text-gray-800 mb-3">Attendance Grade</h3>
                            <?php
                            $attColorClass = getGradeColor($mostRecentEvaluation['attendanceGrade']);
                            ?>
                            <div class="flex justify-between items-start p-3 <?php echo $attColorClass; ?> rounded border-l-4 border-current">
                                <div>
                                    <span class="font-semibold">Attendance</span>
                                    <div class="text-xs opacity-75"><?php echo $mostRecentEvaluation['attendanceDescription']; ?></div>
                                </div>
                                <span class="font-bold text-lg"><?php echo number_format($mostRecentEvaluation['attendanceGrade'], 2); ?></span>
                            </div>
                        </div>

                        <!-- Timestamp -->
                        <div class="text-center text-xs text-gray-500 border-t-2 border-gray-200 pt-4">
                            <i class="fas fa-clock mr-1"></i><?php echo $mostRecentEvaluation['timestamp']; ?>
                        </div>

                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <div class="text-center mb-6">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4 block"></i>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">No Evaluation Yet</h3>
                            <p class="text-gray-600 mb-6">Fill out the form on the left and click "Evaluate" to see results.</p>
                        </div>

                        <!-- Grade Legend -->
                        <div class="border-t-2 border-gray-200 pt-6">
                            <h4 class="text-lg font-bold text-gray-800 mb-4 text-center">Grade Point System</h4>
                            <div class="space-y-3">
                                <!-- Green - Excellent -->
                                <div class="flex items-start p-3 text-green-600 bg-green-50 rounded border-l-4 border-green-600">
                                    <div class="mr-3 text-2xl">🟢</div>
                                    <div class="text-left flex-1">
                                        <p class="font-bold">Excellent / Very Good</p>
                                        <p class="text-xs text-green-700">1.00, 1.25, 1.50</p>
                                        <p class="text-xs mt-1">Outstanding performance (90-100%)</p>
                                    </div>
                                </div>

                                <!-- Blue - Good/Satisfactory -->
                                <div class="flex items-start p-3 text-blue-600 bg-blue-50 rounded border-l-4 border-blue-600">
                                    <div class="mr-3 text-2xl">🔵</div>
                                    <div class="text-left flex-1">
                                        <p class="font-bold">Good / Satisfactory</p>
                                        <p class="text-xs text-blue-700">1.75, 2.00, 2.25, 2.50</p>
                                        <p class="text-xs mt-1">Solid performance (70-89%)</p>
                                    </div>
                                </div>

                                <!-- Yellow - Passing -->
                                <div class="flex items-start p-3 text-yellow-700 bg-yellow-50 rounded border-l-4 border-yellow-600">
                                    <div class="mr-3 text-2xl">🟡</div>
                                    <div class="text-left flex-1">
                                        <p class="font-bold">Passing</p>
                                        <p class="text-xs text-yellow-800">2.75, 3.00</p>
                                        <p class="text-xs mt-1">Minimum requirement met (60-69%)</p>
                                    </div>
                                </div>

                                <!-- Red - Failing -->
                                <div class="flex items-start p-3 text-red-600 bg-red-50 rounded border-l-4 border-red-600">
                                    <div class="mr-3 text-2xl">🔴</div>
                                    <div class="text-left flex-1">
                                        <p class="font-bold">Failing</p>
                                        <p class="text-xs text-red-700">5.00</p>
                                        <p class="text-xs mt-1">Below passing standard (Below 60%) - Retake Required</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- History Section -->
        <?php if ($stats['total'] > 0): ?>
            <div id="history-section" class="bg-white rounded-lg shadow-lg p-8 mt-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-history mr-3 text-indigo-600"></i>Evaluation History
                </h2>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white">
                            <tr>

                                <th class="px-6 py-3 text-center text-sm font-semibold">Date/Time</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold">Attendance</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold">Final Grade</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold">Status</th>
                                <th class="px-6 py-3 text-center text-sm font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach (array_slice($allPredictions, 0, 10) as $index => $pred): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-center text-gray-700"><?php echo date('M d, Y H:i', strtotime($pred['timestamp'])); ?></td>
                                    <td class="px-6 py-4 text-sm text-center font-semibold text-blue-600"><?php echo number_format($pred['attendance'], 1); ?>%</td>
                                    <td class="px-6 py-4 text-sm text-center font-bold text-gray-800"><?php echo number_format($pred['finalGrade'], 2); ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-sm font-semibold <?php echo getStatusColor($pred['status']); ?> <?php echo getStatusBgColor($pred['status']); ?>">
                                            <?php echo $pred['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button onclick="deleteHistory('<?php echo htmlspecialchars($pred['timestamp'], ENT_QUOTES); ?>')" class="text-red-500 hover:text-red-700" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-center text-sm text-gray-600">
                    <i class="fas fa-info-circle mr-1"></i>Showing last 10 evaluations. Total: <?php echo $stats['total']; ?> evaluations stored.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="text-center py-8 text-gray-600 mt-8">
        <p><i class="fas fa-check-circle mr-2"></i>College Performance Prediction System - 3rd Year Student Evaluation</p>
    </div>

    <script>
        // Dark Mode Toggle Functionality
        function toggleDarkMode() {
            const appBody = document.getElementById('appBody');
            const themeToggle = document.getElementById('themeToggle');
            const isDarkMode = appBody.classList.contains('dark-mode');

            if (isDarkMode) {
                // Switch to Light Mode
                appBody.classList.remove('dark-mode');
                themeToggle.querySelector('i').className = 'fas fa-moon';
                themeToggle.querySelector('span').textContent = 'Dark Mode';
                themeToggle.classList.remove('bg-gray-700', 'hover:bg-gray-600', 'text-white');
                themeToggle.classList.add('bg-yellow-400', 'hover:bg-yellow-500', 'text-gray-800');
                localStorage.setItem('theme', 'light');
            } else {
                // Switch to Dark Mode
                appBody.classList.add('dark-mode');
                themeToggle.querySelector('i').className = 'fas fa-sun';
                themeToggle.querySelector('span').textContent = 'Light Mode';
                themeToggle.classList.remove('bg-yellow-400', 'hover:bg-yellow-500', 'text-gray-800');
                themeToggle.classList.add('bg-gray-700', 'hover:bg-gray-600', 'text-white');
                localStorage.setItem('theme', 'dark');
            }
        }

        // Load saved theme preference
        function loadTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            const appBody = document.getElementById('appBody');
            const themeToggle = document.getElementById('themeToggle');

            if (savedTheme === 'dark') {
                appBody.classList.add('dark-mode');
                themeToggle.querySelector('i').className = 'fas fa-sun';
                themeToggle.querySelector('span').textContent = 'Light Mode';
                themeToggle.classList.remove('bg-yellow-400', 'hover:bg-yellow-500', 'text-gray-800');
                themeToggle.classList.add('bg-gray-700', 'hover:bg-gray-600', 'text-white');
            }
        }

        // Clear History Function
        function clearHistory() {
            if (confirm('Are you sure you want to clear all evaluation history? This action cannot be undone.')) {
                fetch('clear_college_history.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('History cleared successfully!');
                            location.reload();
                        } else {
                            alert('Error clearing history: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error clearing history');
                    });
            }
        }

        // Delete a single history entry by timestamp
        function deleteHistory(timestamp) {
            if (!timestamp) return;
            if (!confirm('Delete this evaluation entry?')) return;

            fetch('delete_college_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        timestamp: timestamp
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Entry removed (' + (data.removed || 1) + ').');
                        location.reload();
                    } else {
                        alert('Error removing entry: ' + (data.message || 'unknown'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Error deleting entry');
                });
        }

        // Initialize theme on page load
        document.addEventListener('DOMContentLoaded', loadTheme);

        // Toggle subject details (expand/collapse) and animate progress bar
        function toggleSubjectDetails(container) {
            const details = container.querySelector('.subject-details');
            const isHidden = details.classList.contains('hidden');
            if (isHidden) {
                details.classList.remove('hidden');
                details.classList.add('visible');
                container.setAttribute('aria-expanded', 'true');
                // animate progress bar to target
                const bar = details.querySelector('.progress-bar');
                const target = parseFloat(bar.getAttribute('data-target')) || 0;
                // clamp to 100
                const w = Math.max(0, Math.min(100, target));
                setTimeout(() => {
                    bar.style.width = w + '%';
                }, 50);
            } else {
                // collapse
                const bar = details.querySelector('.progress-bar');
                if (bar) bar.style.width = '0%';
                details.classList.remove('visible');
                details.classList.add('hidden');
                container.setAttribute('aria-expanded', 'false');
            }
        }

        // Attach click and keyboard events to interactive subject boxes
        document.addEventListener('DOMContentLoaded', function() {
            const items = document.querySelectorAll('.interactive-subject');
            items.forEach(item => {
                item.addEventListener('click', () => toggleSubjectDetails(item));
                item.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        toggleSubjectDetails(item);
                    }
                });
                // On hover, pre-fill progress width quietly for quick feedback
                item.addEventListener('mouseenter', () => {
                    const details = item.querySelector('.subject-details');
                    const bar = details ? details.querySelector('.progress-bar') : null;
                    if (bar && details.classList.contains('hidden')) {
                        const target = parseFloat(bar.getAttribute('data-target')) || 0;
                        bar.style.width = Math.max(0, Math.min(100, target)) + '%';
                    }
                });
                item.addEventListener('mouseleave', () => {
                    const details = item.querySelector('.subject-details');
                    const bar = details ? details.querySelector('.progress-bar') : null;
                    if (bar && details.classList.contains('hidden')) {
                        bar.style.width = '0%';
                    }
                });
            });
        });
    </script>

    <style>
        /* Dark Mode Styles */
        body.dark-mode {
            background: linear-gradient(to bottom right, #1a1a2e, #16213e) !important;
        }

        body.dark-mode .bg-white {
            background-color: #2d3748 !important;
        }

        body.dark-mode .text-gray-800 {
            color: #e2e8f0 !important;
        }

        body.dark-mode .text-gray-600 {
            color: #cbd5e0 !important;
        }

        body.dark-mode .text-gray-700 {
            color: #cbd5e0 !important;
        }

        body.dark-mode .text-gray-500 {
            color: #a0aec0 !important;
        }

        body.dark-mode .border-gray-200 {
            border-color: #4a5568 !important;
        }

        body.dark-mode .border-gray-300 {
            border-color: #4a5568 !important;
        }

        body.dark-mode .hover\:bg-gray-50:hover {
            background-color: #4a5568 !important;
        }

        body.dark-mode .bg-red-100 {
            background-color: #742a2a !important;
        }

        body.dark-mode .bg-green-50 {
            background-color: #1e3e1e !important;
        }

        body.dark-mode .bg-blue-50 {
            background-color: #1e3a5f !important;
        }

        body.dark-mode .text-blue-600 {
            color: #3b82f6 !important;
        }

        body.dark-mode .bg-red-100 {
            background-color: #7c2d12 !important;
            color: #fecaca !important;
        }

        body.dark-mode .text-red-700 {
            color: #fca5a5 !important;
        }

        body.dark-mode .border-red-500 {
            border-color: #dc2626 !important;
        }

        body.dark-mode .border-red-600 {
            border-color: #ef4444 !important;
        }

        body.dark-mode .bg-red-200 {
            background-color: #991b1b !important;
        }

        body.dark-mode .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.8;
            }
        }

        body.dark-mode .bg-yellow-50 {
            background-color: #3e3e1e !important;
        }

        body.dark-mode .text-yellow-600 {
            color: #fbbf24 !important;
        }

        body.dark-mode .text-green-600 {
            color: #86efac !important;
        }

        /* Dark-mode override: make EXCELLENT (green) status label darker for contrast
           Applied only when the label already uses the green status class. */
        body.dark-mode .status-label.text-green-600 {
            color: #166534 !important;
            /* dark green (approx Tailwind green-800) */
            text-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
        }

        /* Status advice colors in dark mode: dark by default, but keep
           readable colors when the container is blue (PASSED) or red (FAILED) */
        body.dark-mode .status-advice {
            color: #000000 !important;
        }

        body.dark-mode .bg-blue-100 .status-advice {
            color: #bfdbfe !important;
            /* light-blue for PASSED advice */
        }

        body.dark-mode .bg-red-100 .status-advice {
            color: #fecaca !important;
            /* light-red for FAILED advice */
        }


        body.dark-mode .text-green-700 {
            color: #4ade80 !important;
        }

        body.dark-mode .text-blue-700 {
            color: #60a5fa !important;
        }

        body.dark-mode .text-yellow-700,
        body.dark-mode .text-yellow-800 {
            color: #fbbf24 !important;
        }

        body.dark-mode .text-red-700 {
            color: #fca5a5 !important;
        }

        body.dark-mode .divide-y>*+* {
            border-color: #4a5568 !important;
        }

        /* Improve PASSED display contrast in dark mode */
        .passed-highlight {
            box-shadow: 0 6px 18px rgba(59, 130, 246, 0.08);
        }

        .final-grade {
            color: #4a5568;
        }

        body.dark-mode .passed-highlight {
            background: linear-gradient(90deg, rgba(30, 58, 138, 0.14), rgba(59, 130, 246, 0.06)) !important;
            border-color: rgba(96, 165, 250, 0.6) !important;
        }

        body.dark-mode .passed-highlight .final-grade {
            color: #bfdbfe !important;
            /* light-blue for final grade text */
        }


        /* Interactive subject styles */
        .interactive-subject {
            cursor: pointer;
        }

        .interactive-subject:focus {
            outline: 3px solid rgba(99, 102, 241, 0.25);
        }

        .interactive-subject:hover {
            transform: translateY(-2px);
            transition: transform 150ms;
        }

        .subject-details {
            transition: max-height 250ms ease, opacity 200ms ease;
        }

        .subject-details.hidden {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
        }

        .subject-details.visible {
            max-height: 200px;
            opacity: 1;
        }

        .progress-bar {
            transition: width 700ms cubic-bezier(.2, .9, .2, 1);
        }

        body:not(.dark-mode) .text-gray-700,
        body:not(.dark-mode) .text-gray-600,
        body:not(.dark-mode) .text-gray-500,
        body:not(.dark-mode) .final-grade {
            color: #000000 !important;
        }

        body:not(.dark-mode) .bg-white {
            background-color: #ffffff !important;
        }
    </style>
</body>

</html>