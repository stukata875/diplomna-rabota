<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['method'])) {
    $_SESSION['delivery_method'] = $_POST['method'];
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['success' => false]);