<?php

require_once __DIR__ . '/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson([
        'success' => false,
        'message' => 'Method not allowed'
    ], 405);
}

$input = json_decode(file_get_contents('php://input'), true);

if (
    !isset($input['customer_name']) ||
    !isset($input['item_code']) ||
    !isset($input['qty']) ||
    trim($input['customer_name']) === '' ||
    trim($iput['item_code']) === '' ||
    (int)$input['qty'] <= 0
) {
    logMessage('ERP validation failed');

    sendJson([
        'success' => false,
        'message' => 'Invalid ERP order data'
    ], 400);
}

$customerName = trim($input['customer_name']);
$itemCode = trim($input['item_code']);
$qty = (int)$input['qty'];

$productsPath = __DIR__ . '/../data/products.json';
$ordersPath = __DIR__ . '/../data/orders.json';

$products = readJsonFile($productsPath);
$orders = readJsonFile($ordersPath);

$productIndex = null;
$productData = null;

foreach ($products as $index => $product) {
    if ($product['sku'] === $itemCode) {
        $productIndex = $index;
        $productData = $product;
        break;
    }
}

if ($productData === null) {
    logMessage("ERP product not found: $itemCode");

    sendJson([
        'success' => false,
        'message' => 'Product not found in ERP'
    ], 404);
}

if ($productData['stock'] < $qty) {
    logMessage("ERP insufficient stock for SKU: $itemCode");

    sendJson([
        'success' => false,
        'message' => 'Insufficient stock'
    ], 409);
}

$total = $productData['price'] * $qty;

$order = [
    'id' => count($orders) + 1,
    'customer_name' => $customerName,
    'item_code' => $itemCode,
    'qty' => $qty,
    'total' => $total,
    'status' => 'synced',
    'created_at' => date('Y-m-d H:i:s')
];

$orders[] = $order;
$products[$productIndex]['stock'] -= $qty;

writeJsonFile($ordersPath, $orders);
writeJsonFile($productsPath, $products);

logMessage("ERP order stored successfully for SKU: $itemcode");

sendJson([
    'success' => true,
    'message' => 'Order stored in ERP successfully',
    'order' => $order
], 201);