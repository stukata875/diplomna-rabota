<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Невалидна заявка']);
    exit;
}

// Изчисти кошницата
$_SESSION['cart'] = [];

echo json_encode([
    'success' => true,
    'message' => 'Кошницата е изпразнена'
]);
?>