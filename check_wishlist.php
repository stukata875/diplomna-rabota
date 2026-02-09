<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Не сте влезли в профила']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$book_ids = isset($data['book_ids']) ? $data['book_ids'] : [];

if (empty($book_ids)) {
    echo json_encode(['success' => true, 'wishlist_books' => []]);
    exit();
}

// Подготвяне на IN условия
$placeholders = str_repeat('?,', count($book_ids) - 1) . '?';
$sql = "SELECT book_id FROM wishlist WHERE user_id = ? AND book_id IN ($placeholders)";
$stmt = $conn->prepare($sql);

// Свързване на параметри
$types = str_repeat('i', count($book_ids) + 1);
$params = array_merge([$user_id], $book_ids);
$stmt->bind_param($types, ...$params);

$stmt->execute();
$result = $stmt->get_result();
$wishlist_books = [];

while ($row = $result->fetch_assoc()) {
    $wishlist_books[] = $row['book_id'];
}

$stmt->close();
$conn->close();

echo json_encode(['success' => true, 'wishlist_books' => $wishlist_books]);
?>