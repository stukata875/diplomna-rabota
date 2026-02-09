<?php
// subscribe.php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Невалиден метод']);
    exit();
}

$email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Невалиден имейл адрес']);
    exit();
}

// Тук може да запишете имейла в база данни или файл
// Пример: записване във файл
$file = 'subscribers.txt';
$data = date('Y-m-d H:i:s') . ' | ' . $email . PHP_EOL;
file_put_contents($file, $data, FILE_APPEND | LOCK_EX);

echo json_encode(['success' => true, 'message' => 'Успешно се абонирахте!']);
?>