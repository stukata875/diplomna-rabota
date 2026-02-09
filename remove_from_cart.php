<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Невалидна заявка']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Невалидно ID']);
    exit;
}

// Нормализирай кошницата
normalizeCart();

if (isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
    echo json_encode(['success' => true, 'message' => 'Продуктът е премахнат']);
} else {
    echo json_encode(['success' => false, 'message' => 'Продуктът не е в кошницата']);
}
?>