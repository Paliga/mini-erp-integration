<?php

function sendJson($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function logMiddleware($message) {
    // Path to the log.txt file
    $logPath = __DIR__ . '/../data/logs.txt';

    // Current datetime
    $date = date('Y-m-d H:i:s');

    // Create a log line (timestamp + message)
    $line = "[$date] MIDDLEWARE: $message" . PHP_EOL;

    // Write to file (append mode)
    file_put_contents($logPath, $line, FILE_APPEND);
}