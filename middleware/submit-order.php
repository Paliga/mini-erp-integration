<?php

require_once __DIR__ . '/helpers.php';

// Check if we get a POST method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Return an error
    sendJson([
        'success' => false,
        'message' => 'Method not allowed'
    ], 405);
}

$data = json_decode(file_get_contents('php://input'), true);

// Validate the data
if (
    !isset($data['customerName']) ||
    !isset($data['productSku']) ||
    !isset($data['quantity']) ||
    trim($data['customerName']) === '' ||
    (int)$data['quantity'] <= 0
) {
    logMiddleware("Validation failed");

    sendJson([
        'success' => false,
        'message' => 'Invalid input data'
    ], 400);
}

$erpPayload = [
    'customer_name' => $data['customerName'],
    'product_sku' => $data['productSku'],
    'qty' => (int)$data['quantity'],
    'source' => 'shop-ui'
];