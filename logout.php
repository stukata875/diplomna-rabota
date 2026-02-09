<?php
// logout.php - финална версия

// Стартиране на сесията
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Премахване на всички сесийни променливи
$_SESSION = array();

// Ако искаме да унищожим cookie-то на сесията
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Унищожаване на сесията
session_destroy();

// Пренасочване към началната страница
header('Location: index.php?logout=success');
exit();
?>