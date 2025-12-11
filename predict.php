<?php
require 'vendor/autoload.php';

use Phpml\ModelManager;

// Validate form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['attendance'], $_POST['mathematics'], $_POST['english'], $_POST['science'], $_POST['history'], $_POST['pe'])) {
    die("Invalid form submission.");
}

// Validate input values
$attendance = floatval($_POST['attendance']);
$mathematics = floatval($_POST['mathematics']);
$english = floatval($_POST['english']);
$science = floatval($_POST['science']);
$history = floatval($_POST['history']);
$pe = floatval($_POST['pe']);

if ($attendance < 0 || $mathematics < 0 || $english < 0 || $science < 0 || $history < 0 || $pe < 0) {
    die("All values must be non-negative.");
}

$modelManager = new ModelManager();

if (!file_exists('student_model.phpml')) {
    die("Model file not found! Train first by running train_model.php");
}

$model = $modelManager->restoreFromFile('student_model.phpml');

// Calculate average of subject grades
$subjectAverage = ($mathematics + $english + $science + $history + $pe) / 5;

// Predict using attendance and average subject grade (model expects 3 inputs: attendance, score, score)
$input = [$attendance, $subjectAverage, $subjectAverage];
$result = $model->predict([$input]);

// Calculate overall average score
$avgScore = ($attendance + $subjectAverage) / 2;

// Determine performance level
$performanceLevel = 'Excellent';
$performanceColor = 'green';
if ($avgScore < 60) {
    $performanceLevel = 'Needs Improvement';
    $performanceColor = 'red';
} elseif ($avgScore < 70) {
    $performanceLevel = 'Good';
    $performanceColor = 'yellow';
} elseif ($avgScore < 85) {
    $performanceLevel = 'Very Good';
    $performanceColor = 'blue';
}

// Save prediction to history
$historyFile = 'prediction_history.json';
$predictions = [];

if (file_exists($historyFile)) {
    $predictions = json_decode(file_get_contents($historyFile), true) ?? [];
}

$prediction = [
    'timestamp' => date('Y-m-d H:i:s'),
    'attendance' => $attendance,
    'mathematics' => $mathematics,
    'english' => $english,
    'science' => $science,
    'history' => $history,
    'pe' => $pe,
    'average' => $avgScore,
    'prediction' => $result[0],
    'performance_level' => $performanceLevel
];

// Add to history (keep last 50 predictions)
array_unshift($predictions, $prediction);
$predictions = array_slice($predictions, 0, 50);

file_put_contents($historyFile, json_encode($predictions, JSON_PRETTY_PRINT));

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediction Result</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-chart-line text-3xl"></i>
                    <h1 class="text-3xl font-bold">Prediction Result</h1>
                </div>
                <div class="text-sm opacity-90">Student Performance Analysis</div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="max-w-4xl mx-auto px-4 py-12">
        <!-- Result Card -->
        <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
            <!-- Prediction Result -->
            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 p-8 text-white text-center">
                <h2 class="text-4xl font-bold mb-4">
                    <?php if ($result[0] === 'Pass'): ?>
                        <i class="fas fa-check-circle text-6xl text-green-400 mb-4 block"></i>
                        PASS
                    <?php else: ?>
                        <i class="fas fa-times-circle text-6xl text-red-400 mb-4 block"></i>
                        FAIL
                    <?php endif; ?>
                </h2>
                <p class="text-xl opacity-90">AI-Powered Prediction</p>
            </div>

            <!-- Content -->
            <div class="p-8">
                <!-- Input Summary -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                    <div class="bg-blue-50 rounded-lg p-6 border-l-4 border-blue-500">
                        <p class="text-gray-600 text-sm font-semibold mb-1">Attendance %</p>
                        <p class="text-3xl font-bold text-blue-600"><?php echo number_format($attendance, 1); ?>%</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-6 border-l-4 border-red-500">
                        <p class="text-gray-600 text-sm font-semibold mb-1">Mathematics Grade</p>
                        <p class="text-3xl font-bold text-red-600"><?php echo number_format($mathematics, 1); ?></p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-6 border-l-4 border-green-500">
                        <p class="text-gray-600 text-sm font-semibold mb-1">English Grade</p>
                        <p class="text-3xl font-bold text-green-600"><?php echo number_format($english, 1); ?></p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-6 border-l-4 border-yellow-500">
                        <p class="text-gray-600 text-sm font-semibold mb-1">Science Grade</p>
                        <p class="text-3xl font-bold text-yellow-600"><?php echo number_format($science, 1); ?></p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-6 border-l-4 border-purple-500">
                        <p class="text-gray-600 text-sm font-semibold mb-1">History Grade</p>
                        <p class="text-3xl font-bold text-purple-600"><?php echo number_format($history, 1); ?></p>
                    </div>
                    <div class="bg-indigo-50 rounded-lg p-6 border-l-4 border-indigo-500">
                        <p class="text-gray-600 text-sm font-semibold mb-1">Physical Education Grade</p>
                        <p class="text-3xl font-bold text-indigo-600"><?php echo number_format($pe, 1); ?></p>
                    </div>
                </div>

                <!-- Performance Analysis -->
                <div class="bg-gray-50 rounded-lg p-6 mb-8">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Performance Analysis</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between mb-2">
                                <span class="text-gray-700 font-semibold">Overall Average Score</span>
                                <span class="text-2xl font-bold text-indigo-600"><?php echo number_format($avgScore, 2); ?></span>
                            </div>
                            <div class="w-full bg-gray-300 rounded-full h-4">
                                <div class="bg-gradient-to-r from-indigo-500 to-blue-500 h-4 rounded-full" 
                                     style="width: <?php echo min($avgScore, 100); ?>%"></div>
                            </div>
                        </div>

                        <div class="pt-4 border-t-2 border-gray-300">
                            <p class="text-gray-700 mb-2">
                                <i class="fas fa-star text-yellow-500 mr-2"></i>
                                <span class="font-semibold">Performance Level:</span>
                            </p>
                            <p class="text-lg font-bold text-<?php echo $performanceColor; ?>-600">
                                <?php echo $performanceLevel; ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Prediction Info -->
                <div class="bg-indigo-50 border-l-4 border-indigo-500 p-6 rounded mb-8">
                    <h3 class="font-semibold text-indigo-900 mb-2">
                        <i class="fas fa-lightbulb mr-2"></i>ML Model Prediction
                    </h3>
                    <p class="text-indigo-800">
                        Based on the student's attendance, quiz performance, and assignment scores, 
                        the machine learning model predicts the student will <strong><?php echo $result[0]; ?></strong>.
                    </p>
                </div>

                <!-- Recommendations -->
                <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded mb-8">
                    <h3 class="font-semibold text-blue-900 mb-3">
                        <i class="fas fa-graduation-cap mr-2"></i>Recommendations
                    </h3>
                    <ul class="text-blue-800 space-y-2">
                        <?php if ($result[0] === 'Fail'): ?>
                            <li><i class="fas fa-arrow-right mr-2"></i>Improve quiz performance through regular study</li>
                            <li><i class="fas fa-arrow-right mr-2"></i>Increase attendance and classroom participation</li>
                            <li><i class="fas fa-arrow-right mr-2"></i>Complete assignments with more care and effort</li>
                            <li><i class="fas fa-arrow-right mr-2"></i>Seek tutoring or peer study groups for support</li>
                        <?php else: ?>
                            <li><i class="fas fa-arrow-right mr-2"></i>Maintain your excellent attendance record</li>
                            <li><i class="fas fa-arrow-right mr-2"></i>Continue strong performance in assignments</li>
                            <li><i class="fas fa-arrow-right mr-2"></i>Keep challenging yourself with advanced topics</li>
                            <li><i class="fas fa-arrow-right mr-2"></i>Help other students who may need support</li>
                        <?php endif; ?>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4 pt-4">
                    <a href="index.php" class="flex-1 bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-3 px-4 rounded-lg transition text-center">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                    <a href="index.php" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded-lg transition text-center">
                        <i class="fas fa-plus mr-2"></i>New Prediction
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-gray-600">
            <p><i class="fas fa-shield-alt mr-2"></i>This prediction is based on machine learning models and historical data</p>
        </div>
    </div>
</body>
</html>