<?php
require 'vendor/autoload.php';

use Phpml\Classification\DecisionTree;
use Phpml\ModelManager;

$trainingSamples = [
    [85, 78, 90],
    [60, 55, 50],
    [92, 88, 96],
    [45, 40, 35],
    [70, 65, 60],
    [30, 25, 20],
];

$trainingLabels = [
    "Pass",
    "Fail",
    "Pass",
    "Fail",
    "Pass",
    "Fail"
];

$model = new DecisionTree();
$model->train($trainingSamples, $trainingLabels);

// Save model
$modelManager = new ModelManager();
$modelManager->saveToFile($model, 'student_model.phpml');

echo "Model trained and saved successfully!";
