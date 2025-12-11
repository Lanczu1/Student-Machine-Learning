<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Performance Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-chart-line text-3xl"></i>
                    <h1 class="text-3xl font-bold">Student Performance Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="history.php" class="bg-white text-indigo-600 hover:bg-indigo-50 px-4 py-2 rounded-lg font-semibold transition">
                        <i class="fas fa-history mr-2"></i>View All Predictions
                    </a>
                    <button onclick="clearAllData()" class="bg-red-500 text-white hover:bg-red-600 px-4 py-2 rounded-lg font-semibold transition">
                        <i class="fas fa-trash mr-2"></i>Clear/Reset
                    </button>
                    <div class="text-sm opacity-90">ML Prediction System</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Total Students</p>
                        <p class="stat-total-students text-3xl font-bold text-blue-600">0</p>
                    </div>
                    <i class="fas fa-users text-4xl text-blue-100"></i>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Pass Rate</p>
                        <p class="stat-pass-rate text-3xl font-bold text-green-600">0%</p>
                    </div>
                    <i class="fas fa-check-circle text-4xl text-green-100"></i>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Avg Score</p>
                        <p class="stat-avg-score text-3xl font-bold text-yellow-600">0</p>
                    </div>
                    <i class="fas fa-star text-4xl text-yellow-100"></i>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-semibold">Attendance</p>
                        <p class="stat-attendance text-3xl font-bold text-purple-600">0%</p>
                    </div>
                    <i class="fas fa-calendar-check text-4xl text-purple-100"></i>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Charts Section -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Performance by Grade -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Students by Course Subject</h2>
                        <i class="fas fa-chart-pie text-2xl text-indigo-500"></i>
                    </div>
                    <canvas id="gradeChart" style="max-height: 300px;"></canvas>
                </div>

                <!-- Participation Rate -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Participation Rate by Branch</h2>
                        <i class="fas fa-chart-bar text-2xl text-indigo-500"></i>
                    </div>
                    <canvas id="participationChart" style="max-height: 250px;"></canvas>
                </div>

                <!-- Examination Results -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Examination Results by Branch</h2>
                        <i class="fas fa-book text-2xl text-indigo-500"></i>
                    </div>
                    <canvas id="examChart" style="max-height: 300px;"></canvas>
                </div>

                <!-- Prediction History -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-800">Recent Predictions</h2>
                        <i class="fas fa-history text-2xl text-indigo-500"></i>
                    </div>
                    <div id="predictionHistoryContainer" class="space-y-3">
                        <p class="text-gray-500 text-center py-8">No predictions yet. Use the predictor to get started!</p>
                    </div>
                </div>
            </div>

            <!-- Predictor Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-8 sticky top-8">
                    <div class="text-center mb-6">
                        <i class="fas fa-brain text-4xl text-indigo-600 mb-2"></i>
                        <h2 class="text-2xl font-bold text-gray-800">Performance Predictor</h2>
                        <p class="text-gray-600 text-sm mt-2">AI-Powered Prediction</p>
                    </div>

                    <form action="predict.php" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-percent text-indigo-500 mr-2"></i>Attendance %
                            </label>
                            <input type="number" name="attendance" min="0" max="100" step="0.1" 
                                   placeholder="Enter attendance %" required
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calculator text-indigo-500 mr-2"></i>Mathematics Grade
                            </label>
                            <input type="number" name="mathematics" min="0" max="100" step="0.1" 
                                   placeholder="Enter Mathematics grade" required
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-book text-indigo-500 mr-2"></i>English Grade
                            </label>
                            <input type="number" name="english" min="0" max="100" step="0.1" 
                                   placeholder="Enter English grade" required
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-flask text-indigo-500 mr-2"></i>Science Grade
                            </label>
                            <input type="number" name="science" min="0" max="100" step="0.1" 
                                   placeholder="Enter Science grade" required
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-scroll text-indigo-500 mr-2"></i>History Grade
                            </label>
                            <input type="number" name="history" min="0" max="100" step="0.1" 
                                   placeholder="Enter History grade" required
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-running text-indigo-500 mr-2"></i>Physical Education Grade
                            </label>
                            <input type="number" name="pe" min="0" max="100" step="0.1" 
                                   placeholder="Enter PE grade" required
                                   class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 transition">
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white font-bold py-3 px-4 rounded-lg transition transform hover:scale-105 shadow-md mt-6">
                            <i class="fas fa-magic mr-2"></i>Predict Performance
                        </button>
                    </form>

                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                        <p class="text-xs text-gray-600 text-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            Model trained on student performance data
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Clear all data and reset stats
        function clearAllData() {
            if (confirm('Are you sure you want to clear all predictions and reset the dashboard? This action cannot be undone.')) {
                fetch('clear_history.php', { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reset stat cards
                            document.querySelectorAll('.stat-total-students').forEach(el => el.textContent = '0');
                            document.querySelectorAll('.stat-pass-rate').forEach(el => el.textContent = '0%');
                            document.querySelectorAll('.stat-avg-score').forEach(el => el.textContent = '0');
                            document.querySelectorAll('.stat-attendance').forEach(el => el.textContent = '0%');
                            
                            // Reset prediction history container
                            document.getElementById('predictionHistoryContainer').innerHTML = '<p class="text-gray-500 text-center py-8">No predictions yet. Use the predictor to get started!</p>';
                            
                            // Reset charts
                            if (window.gradeChartInstance) {
                                window.gradeChartInstance.data.datasets[0].data = [22.67, 21, 14.67, 20.33, 21.33];
                                window.gradeChartInstance.update();
                            }
                            if (window.participationChartInstance) {
                                window.participationChartInstance.data.datasets[0].data = [89, 87.7, 87.33, 85.33, 82.33];
                                window.participationChartInstance.update();
                            }
                            if (window.examChartInstance) {
                                window.examChartInstance.data.datasets[0].data = [240, 220, 200, 190, 250];
                                window.examChartInstance.data.datasets[1].data = [20, 45, 30, 50, 40];
                                window.examChartInstance.update();
                            }
                            
                            alert('All data cleared successfully!');
                        }
                    })
                    .catch(error => {
                        console.error('Error clearing data:', error);
                        alert('Error clearing data');
                    });
            }
        }

        // Load prediction history and update dashboard stats
        async function loadPredictionHistory() {
            try {
                const response = await fetch('get_history.php');
                const data = await response.json();
                const container = document.getElementById('predictionHistoryContainer');
                
                // Update dashboard statistics
                if (data.length > 0) {
                    const totalPredictions = data.length;
                    const passPredictions = data.filter(p => p.prediction === 'Pass').length;
                    const failPredictions = data.filter(p => p.prediction === 'Fail').length;
                    const passRate = ((passPredictions / totalPredictions) * 100).toFixed(1);
                    const avgScore = (data.reduce((sum, p) => sum + p.average, 0) / totalPredictions).toFixed(1);
                    const avgAttendance = (data.reduce((sum, p) => sum + p.attendance, 0) / totalPredictions).toFixed(1);
                    
                    // Update stat cards
                    document.querySelectorAll('.stat-total-students').forEach(el => {
                        el.textContent = totalPredictions;
                    });
                    document.querySelectorAll('.stat-pass-rate').forEach(el => {
                        el.textContent = passRate + '%';
                    });
                    document.querySelectorAll('.stat-avg-score').forEach(el => {
                        el.textContent = avgScore;
                    });
                    document.querySelectorAll('.stat-attendance').forEach(el => {
                        el.textContent = avgAttendance + '%';
                    });
                    
                    // Update course subject distribution
                    updateCourseDistribution(data);
                    
                    // Update participation and exam charts
                    updateChartsWithData(data);
                }
                
                if (data.length === 0) {
                    container.innerHTML = '<p class="text-gray-500 text-center py-8">No predictions yet. Use the predictor to get started!</p>';
                    return;
                }
                
                let html = '';
                data.slice(0, 5).forEach((pred, index) => {
                    const resultClass = pred.prediction === 'Pass' ? 'bg-green-100 border-green-500' : 'bg-red-100 border-red-500';
                    const resultIcon = pred.prediction === 'Pass' ? 'fas fa-check-circle text-green-600' : 'fas fa-times-circle text-red-600';
                    const levelColor = {
                        'Excellent': 'text-purple-600',
                        'Very Good': 'text-blue-600',
                        'Good': 'text-yellow-600',
                        'Needs Improvement': 'text-red-600'
                    }[pred.performance_level] || 'text-gray-600';
                    
                    html += `
                        <div class="border-l-4 ${resultClass} p-4 rounded bg-opacity-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <i class="${resultIcon}"></i>
                                        <span class="font-semibold text-gray-800">${pred.prediction}</span>
                                        <span class="text-xs text-gray-600">Avg: ${parseFloat(pred.average).toFixed(2)}</span>
                                    </div>
                                    <div class="flex gap-2 text-xs text-gray-700 mb-2 flex-wrap">
                                        <span class="bg-red-100 px-2 py-1 rounded"><i class="fas fa-calculator text-red-600 mr-1"></i>M: ${pred.mathematics}</span>
                                        <span class="bg-green-100 px-2 py-1 rounded"><i class="fas fa-book text-green-600 mr-1"></i>E: ${pred.english}</span>
                                        <span class="bg-yellow-100 px-2 py-1 rounded"><i class="fas fa-flask text-yellow-600 mr-1"></i>S: ${pred.science}</span>
                                        <span class="bg-purple-100 px-2 py-1 rounded"><i class="fas fa-scroll text-purple-600 mr-1"></i>H: ${pred.history}</span>
                                        <span class="bg-indigo-100 px-2 py-1 rounded"><i class="fas fa-running text-indigo-600 mr-1"></i>PE: ${pred.pe}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs text-gray-700 mb-2">
                                        <i class="fas fa-percent text-indigo-500"></i> ${pred.attendance}%
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-500">${new Date(pred.timestamp).toLocaleString()}</span>
                                        <span class="text-xs font-semibold ${levelColor}">${pred.performance_level}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                container.innerHTML = html;
            } catch (error) {
                console.error('Error loading predictions:', error);
            }
        }

        // Update course distribution based on performance levels
        function updateCourseDistribution(data) {
            if (!data || data.length === 0) return;
            
            // Count students in each subject based on their grades
            const subjectCounts = {
                'Mathematics': 0,
                'English': 0,
                'Science': 0,
                'History': 0,
                'PE': 0
            };
            
            data.forEach(pred => {
                if (pred.mathematics >= 70) subjectCounts['Mathematics']++;
                if (pred.english >= 70) subjectCounts['English']++;
                if (pred.science >= 70) subjectCounts['Science']++;
                if (pred.history >= 70) subjectCounts['History']++;
                if (pred.pe >= 70) subjectCounts['PE']++;
            });
            
            const courseDistribution = [
                subjectCounts['Mathematics'],
                subjectCounts['English'],
                subjectCounts['Science'],
                subjectCounts['History'],
                subjectCounts['PE']
            ];
            
            if (window.gradeChartInstance) {
                window.gradeChartInstance.data.datasets[0].data = courseDistribution;
                window.gradeChartInstance.update();
            }
        }

        // Update participation and exam charts with prediction data
        function updateChartsWithData(data) {
            if (!data || data.length === 0) return;
            
            // Calculate participation rates based on average subject grades
            const courseParticipation = [
                (data.reduce((sum, p) => sum + (p.mathematics || 0), 0) / data.length).toFixed(2),
                (data.reduce((sum, p) => sum + (p.english || 0), 0) / data.length).toFixed(2),
                (data.reduce((sum, p) => sum + (p.science || 0), 0) / data.length).toFixed(2),
                (data.reduce((sum, p) => sum + (p.history || 0), 0) / data.length).toFixed(2),
                (data.reduce((sum, p) => sum + (p.pe || 0), 0) / data.length).toFixed(2)
            ].map(v => parseFloat(v));
            
            if (window.participationChartInstance) {
                window.participationChartInstance.data.datasets[0].data = courseParticipation;
                window.participationChartInstance.update();
            }
            
            // Update exam results based on actual predictions (Pass/Fail)
            const courseExamPass = [
                data.filter(p => p.prediction === 'Pass' && p.mathematics >= 70).length,
                data.filter(p => p.prediction === 'Pass' && p.english >= 70).length,
                data.filter(p => p.prediction === 'Pass' && p.science >= 70).length,
                data.filter(p => p.prediction === 'Pass' && p.history >= 70).length,
                data.filter(p => p.prediction === 'Pass' && p.pe >= 70).length
            ];
            
            const courseExamFail = [
                data.filter(p => p.prediction === 'Fail' || (p.mathematics < 70)).length,
                data.filter(p => p.prediction === 'Fail' || (p.english < 70)).length,
                data.filter(p => p.prediction === 'Fail' || (p.science < 70)).length,
                data.filter(p => p.prediction === 'Fail' || (p.history < 70)).length,
                data.filter(p => p.prediction === 'Fail' || (p.pe < 70)).length
            ];
            
            if (window.examChartInstance) {
                window.examChartInstance.data.datasets[0].data = courseExamPass;
                window.examChartInstance.data.datasets[1].data = courseExamFail;
                window.examChartInstance.update();
            }
        }

        // Load history on page load
        loadPredictionHistory();
        
        // Reload history every 2 seconds to reflect new predictions
        setInterval(loadPredictionHistory, 2000);

        // Grade Distribution Chart
        const gradeCtx = document.getElementById('gradeChart').getContext('2d');
        window.gradeChartInstance = new Chart(gradeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Mathematics', 'English', 'Science', 'History', 'Physical Education'],
                datasets: [{
                    data: [22.67, 21, 14.67, 20.33, 21.33],
                    backgroundColor: ['#FF6B6B', '#FFA500', '#FFD93D', '#95E1D3', '#C77DFF'],
                    borderColor: '#fff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 12 }, padding: 15 }
                    }
                }
            }
        });

        // Participation Rate Chart
        const participationCtx = document.getElementById('participationChart').getContext('2d');
        window.participationChartInstance = new Chart(participationCtx, {
            type: 'bar',
            data: {
                labels: ['Mathematics', 'English', 'Science', 'History', 'Physical Education'],
                datasets: [{
                    label: 'Participation Rate %',
                    data: [89, 87.7, 87.33, 85.33, 82.33],
                    backgroundColor: '#FFA500',
                    borderRadius: 5,
                    borderSkipped: false
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: { beginAtZero: true, max: 100 }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Examination Results Chart
        const examCtx = document.getElementById('examChart').getContext('2d');
        window.examChartInstance = new Chart(examCtx, {
            type: 'bar',
            data: {
                labels: ['Mathematics', 'English', 'Science', 'History', 'Physical Education'],
                datasets: [
                    {
                        label: 'Pass',
                        data: [240, 220, 200, 190, 250],
                        backgroundColor: '#FFD93D'
                    },
                    {
                        label: 'Fail',
                        data: [20, 45, 30, 50, 40],
                        backgroundColor: '#FF6B6B'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    x: { stacked: false },
                    y: { stacked: false }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { size: 11 }, padding: 10 }
                    }
                }
            }
        });
    </script>
</body>
</html>
