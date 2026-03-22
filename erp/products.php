<?php

require_once __DIR__ . '/helpers.php';

$productsPath = __DIR__ . '/../data/products.json';
$products = readJsonFile($productsPath);

sendJson([
    'success' => true,
    'products' => $products
]);