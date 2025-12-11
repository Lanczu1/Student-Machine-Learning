<?php
header('Content-Type: application/json');

$historyFile = 'college_history.json';

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (empty($data['timestamp'])) {
    echo json_encode(['success' => false, 'message' => 'Missing timestamp']);
    exit;
}

$timestamp = $data['timestamp'];

if (!file_exists($historyFile)) {
    echo json_encode(['success' => false, 'message' => 'History file not found']);
    exit;
}

$history = json_decode(file_get_contents($historyFile), true) ?? [];
$originalCount = count($history);

// Filter out entries that match the given timestamp exactly
$newHistory = array_values(array_filter($history, function($item) use ($timestamp) {
    return (($item['timestamp'] ?? '') !== $timestamp);
}));

$removed = $originalCount - count($newHistory);

if ($removed > 0) {
    file_put_contents($historyFile, json_encode($newHistory, JSON_PRETTY_PRINT));
    echo json_encode(['success' => true, 'removed' => $removed]);
} else {
    echo json_encode(['success' => false, 'message' => 'No matching entry found']);
}

exit;
