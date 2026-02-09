<?php
require_once 'config.php';

header('Content-Type: application/json');

// Нормализирай кошницата преди да я броиш
normalizeCart();

$cart_count = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;

echo json_encode(['cart_count' => $cart_count]);
?>