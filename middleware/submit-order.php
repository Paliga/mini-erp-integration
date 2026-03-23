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
    'item_code' => $data['productSku'],
    'qty' => (int)$data['quantity'],
    'source' => 'shop-ui'
];

$erpUrl = 'http://localhost:8082/erp/receive-order.php';

$ch = curl_init($erpUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($erpPayload));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);

curl_close($ch);

if ($curlError) {
    logMiddleware("curl error: $curlError");

    sendJson([
        'success' => false,
        'message' => 'Failed to connect to ERP system'
    ], 500);
}

$erpResponse = json_decode($response, true);

if(!$erpResponse) {
    logMiddleware("Invalid ERP response: $response");

    sendJson([
        'success' => false,
        'message' => 'Invalid ERP response'
    ], 500);
}

sendJson($erpResponse, $httpCode);