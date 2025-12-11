<?php
header('Content-Type: application/json');

$historyFile = 'prediction_history.json';
$predictions = [];

if (file_exists($historyFile)) {
    $content = file_get_contents($historyFile);
    $predictions = json_decode($content, true) ?? [];
}

echo json_encode($predictions);
?>