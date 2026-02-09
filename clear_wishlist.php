<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Моля, влезте в профила си']);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "DELETE FROM wishlist WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Всички книги са премахнати от любими']);
} else {
    echo json_encode(['success' => false, 'message' => 'Грешка при изтриване: ' . $conn->error]);
}

$stmt->close();
$conn->close();
?>