<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Моля, влезте в профила си']);
    exit();
}

$user_id = $_SESSION['user_id'];
$book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : 'add';

if ($book_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Невалидна книга']);
    exit();
}

// Проверка дали книгата съществува
$check_book = $conn->prepare("SELECT id FROM books WHERE id = ?");
$check_book->bind_param("i", $book_id);
$check_book->execute();
$book_exists = $check_book->get_result()->num_rows > 0;
$check_book->close();

if (!$book_exists) {
    echo json_encode(['success' => false, 'message' => 'Книгата не съществува']);
    exit();
}

if ($action === 'add') {
    // Добавяне в любими
    $stmt = $conn->prepare("INSERT IGNORE INTO wishlist (user_id, book_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $book_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Книгата е добавена в любими']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Грешка при добавяне: ' . $conn->error]);
    }
    $stmt->close();
    
} elseif ($action === 'remove') {
    // Премахване от любими
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND book_id = ?");
    $stmt->bind_param("ii", $user_id, $book_id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Книгата е премахната от любими']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Грешка при премахване: ' . $conn->error]);
    }
    $stmt->close();
}

$conn->close();
?>