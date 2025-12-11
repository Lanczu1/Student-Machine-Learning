<?php
header('Content-Type: application/json');

$historyFile = 'prediction_history.json';

if (file_exists($historyFile)) {
    unlink($historyFile);
}

echo json_encode(['success' => true, 'message' => 'All prediction history cleared']);
?>