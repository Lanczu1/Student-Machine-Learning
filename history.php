<?php
$historyFile = 'prediction_history.json';
$predictions = [];

if (file_exists($historyFile)) {
    $predictions = json_decode(file_get_contents($historyFile), true) ?? [];
}

// Get statistics
$totalPredictions = count($predictions);
$passCount = 0;
$failCount = 0;
$avgAverage = 0;

foreach ($predictions as $pred) {
    if ($pred['prediction'] === 'Pass') {
        $passCount++;
    } else {
        $failCount++;
    }
    $avgAverage += $pred['average'];
}

if ($totalPredictions > 0) {
    $avgAverage /= $totalPredictions;
}

$passRate = $totalPredictions > 0 ? ($passCount / $totalPredictions) * 100 : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prediction History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-history text-3xl"></i>
                    <h1 class="text-3xl font-bold">Prediction History</h1>
                </div>
                <a href="index.php" class="bg-white text-indigo-600 hover:bg-indigo-50 px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Total Predictions</p>
                        <p class="text-3xl font-bold text-blue-600"><?php echo $totalPredictions; ?></p>
                    </div>
                    <i class="fas fa-chart-bar text-4xl text-blue-100"></i>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Pass Count</p>
                        <p class="text-3xl font-bold text-green-600"><?php echo $passCount; ?></p>
                    </div>
                    <i class="fas fa-check-circle text-4xl text-green-100"></i>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Fail Count</p>
                        <p class="text-3xl font-bold text-red-600"><?php echo $failCount; ?></p>
                    </div>
                    <i class="fas fa-times-circle text-4xl text-red-100"></i>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Pass Rate</p>
                        <p class="text-3xl font-bold text-purple-600"><?php echo number_format($passRate, 1); ?>%</p>
                    </div>
                    <i class="fas fa-percent text-4xl text-purple-100"></i>
                </div>
            </div>
        </div>

        <!-- Predictions Table -->
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 border-b-2 border-gray-200">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-table mr-3 text-indigo-600"></i>All Predictions
                </h2>
            </div>

            <?php if ($totalPredictions === 0): ?>
                <div class="p-12 text-center">
                    <i class="fas fa-inbox text-6xl text-gray-300 mb-4 block"></i>
                    <p class="text-gray-500 text-xl">No predictions recorded yet.</p>
                    <a href="index.php" class="text-indigo-600 hover:text-indigo-700 font-semibold mt-4 inline-block">
                        Go make your first prediction
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-r from-indigo-500 to-blue-500 text-white">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold">#</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Date/Time</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold">Attendance %</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold">Mathematics</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold">English</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold">Science</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold">History</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold">PE</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold">Average</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold">Prediction</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold">Performance Level</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($predictions as $index => $pred): 
                                $resultColor = $pred['prediction'] === 'Pass' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50';
                                $levelColors = [
                                    'Excellent' => 'bg-purple-100 text-purple-600',
                                    'Very Good' => 'bg-blue-100 text-blue-600',
                                    'Good' => 'bg-yellow-100 text-yellow-600',
                                    'Needs Improvement' => 'bg-red-100 text-red-600'
                                ];
                                $levelColor = $levelColors[$pred['performance_level']] ?? 'bg-gray-100 text-gray-600';
                            ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-600"><?php echo $index + 1; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-700"><?php echo date('M d, Y h:i A', strtotime($pred['timestamp'])); ?></td>
                                    <td class="px-6 py-4 text-sm text-center font-semibold text-indigo-600"><?php echo $pred['attendance']; ?>%</td>
                                    <td class="px-6 py-4 text-sm text-center font-semibold text-red-600"><?php echo $pred['mathematics']; ?></td>
                                    <td class="px-6 py-4 text-sm text-center font-semibold text-green-600"><?php echo $pred['english']; ?></td>
                                    <td class="px-6 py-4 text-sm text-center font-semibold text-yellow-600"><?php echo $pred['science']; ?></td>
                                    <td class="px-6 py-4 text-sm text-center font-semibold text-purple-600"><?php echo $pred['history']; ?></td>
                                    <td class="px-6 py-4 text-sm text-center font-semibold text-indigo-600"><?php echo $pred['pe']; ?></td>
                                    <td class="px-6 py-4 text-sm text-center font-bold text-gray-800"><?php echo number_format($pred['average'], 2); ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-sm font-semibold <?php echo $resultColor; ?>">
                                            <?php if ($pred['prediction'] === 'Pass'): ?>
                                                <i class="fas fa-check-circle mr-1"></i><?php echo $pred['prediction']; ?>
                                            <?php else: ?>
                                                <i class="fas fa-times-circle mr-1"></i><?php echo $pred['prediction']; ?>
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $levelColor; ?>">
                                            <?php echo $pred['performance_level']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Summary Stats -->
        <?php if ($totalPredictions > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-line mr-2 text-indigo-600"></i>Average Metrics
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-gray-700">Avg. Overall Score:</span>
                        <span class="text-2xl font-bold text-indigo-600"><?php echo number_format($avgAverage, 2); ?></span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-gray-700">Avg. Attendance:</span>
                        <span class="text-xl font-bold text-green-600"><?php echo number_format(array_sum(array_column($predictions, 'attendance')) / $totalPredictions, 2); ?>%</span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-gray-700">Avg. Mathematics:</span>
                        <span class="text-xl font-bold text-red-600"><?php echo number_format(array_sum(array_column($predictions, 'mathematics')) / $totalPredictions, 2); ?></span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-gray-700">Avg. English:</span>
                        <span class="text-xl font-bold text-green-600"><?php echo number_format(array_sum(array_column($predictions, 'english')) / $totalPredictions, 2); ?></span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-gray-700">Avg. Science:</span>
                        <span class="text-xl font-bold text-yellow-600"><?php echo number_format(array_sum(array_column($predictions, 'science')) / $totalPredictions, 2); ?></span>
                    </div>
                    <div class="flex justify-between items-center pb-3 border-b">
                        <span class="text-gray-700">Avg. History:</span>
                        <span class="text-xl font-bold text-purple-600"><?php echo number_format(array_sum(array_column($predictions, 'history')) / $totalPredictions, 2); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-700">Avg. PE:</span>
                        <span class="text-xl font-bold text-indigo-600"><?php echo number_format(array_sum(array_column($predictions, 'pe')) / $totalPredictions, 2); ?></span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-pie-chart mr-2 text-indigo-600"></i>Performance Distribution
                </h3>
                <div class="space-y-4">
                    <?php 
                    $performanceLevels = [
                        'Excellent' => 0,
                        'Very Good' => 0,
                        'Good' => 0,
                        'Needs Improvement' => 0
                    ];
                    foreach ($predictions as $pred) {
                        $performanceLevels[$pred['performance_level']]++;
                    }
                    foreach ($performanceLevels as $level => $count):
                        $percentage = $totalPredictions > 0 ? ($count / $totalPredictions) * 100 : 0;
                        $colors = [
                            'Excellent' => 'bg-purple-500',
                            'Very Good' => 'bg-blue-500',
                            'Good' => 'bg-yellow-500',
                            'Needs Improvement' => 'bg-red-500'
                        ];
                    ?>
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-gray-700 text-sm font-semibold"><?php echo $level; ?></span>
                                <span class="text-sm font-bold text-gray-600"><?php echo $count; ?> (<?php echo number_format($percentage, 1); ?>%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="h-3 rounded-full <?php echo $colors[$level]; ?>" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div class="text-center py-8 text-gray-600 mt-12">
        <p><i class="fas fa-info-circle mr-2"></i>Last 50 predictions are stored in the system</p>
    </div>
</body>
</html>