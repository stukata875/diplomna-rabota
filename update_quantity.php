<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Невалидна заявка']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($id <= 0 || $quantity < 1 || $quantity > 10) {
    echo json_encode(['success' => false, 'message' => 'Невалидни данни']);
    exit;
}

// Нормализирай кошницата
normalizeCart();

if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] = $quantity;
    echo json_encode(['success' => true, 'message' => 'Количеството е обновено']);
} else {
    echo json_encode(['success' => false, 'message' => 'Продуктът не е в кошницата']);
}
?>