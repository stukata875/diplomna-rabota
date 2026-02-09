<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Невалидна заявка']);
    exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Невалидно ID на продукт']);
    exit;
}

// Проверка дали книгата съществува
$stmt = $conn->prepare("SELECT id FROM books WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Проверка в предстоящи книги
    $stmt = $conn->prepare("SELECT id FROM upcoming_books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Продуктът не е намерен']);
        exit;
    }
}

// Нормализиране на кошницата (ако все още не е направено)
normalizeCart();

// Добавяне/увеличаване на количеството
if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] += 1;
    if ($_SESSION['cart'][$id] > 10) {
        $_SESSION['cart'][$id] = 10;
    }
} else {
    $_SESSION['cart'][$id] = 1;
}

echo json_encode([
    'success' => true,
    'message' => 'Продуктът е добавен в кошницата',
    'cart_count' => array_sum($_SESSION['cart'])
]);
$stmt->close();
$conn->close();
?>