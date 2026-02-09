<?php
// config.php - НОВ ФАЙЛ (без дублиране)

// Подавене на грешки за потребител (за development)
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Проверка дали сесията не е вече стартирана
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Конфигурация за база данни
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'bookstore');

// Създаване на връзка
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Проверка за грешки
if ($conn->connect_error) {
    die("В момента имаме технически проблеми. Моля, опитайте по-късно.");
}

// Задаване на кодировка за кирилица
$conn->set_charset("utf8mb4");

// ФУНКЦИЯ ЗА НОРМАЛИЗИРАНЕ НА КОШНИЦАТА - САМО ЕДНА ВЕДНЪЖ
function normalizeCart() {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
        return;
    }
    
    $normalized = [];
    foreach ($_SESSION['cart'] as $id => $item) {
        $id = (int)$id;
        
        if (is_numeric($item)) {
            // Вече е число
            $quantity = (int)$item;
        } elseif (is_array($item) && isset($item['quantity'])) {
            // Стара структура
            $quantity = (int)$item['quantity'];
        } elseif (is_array($item) && isset($item[0])) {
            // Друга структура
            $quantity = (int)$item[0];
        } else {
            // Непозната структура - прескачаме
            continue;
        }
        
        if ($quantity > 0 && $quantity <= 10) {
            $normalized[$id] = $quantity;
        }
    }
    
    $_SESSION['cart'] = $normalized;
}
// Функция за получаване на броя на артикулите
function getCartCount() {
    normalizeCart(); // Увери се, че кошницата е нормализирана
    return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
}

// Инициализация на кошницата
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Нормализиране на кошницата
normalizeCart();
?>