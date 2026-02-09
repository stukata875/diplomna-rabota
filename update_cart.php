<?php
require_once 'config.php';
header('Content-Type: application/json; charset=utf-8');

$id = (int)($_POST['id'] ?? 0);
$quantity = (int)($_POST['quantity'] ?? 0);

if ($id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Невалиден продукт'
    ]);
    exit;
}

if ($quantity <= 0) {
    unset($_SESSION['cart'][$id]);
} elseif ($quantity <= 10) {
    $_SESSION['cart'][$id] = $quantity;
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Максимум 10 броя',
        'cart_count' => array_sum($_SESSION['cart'])
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'cart_count' => array_sum($_SESSION['cart']),
    'message' => 'Количеството е обновено'
]);
?>