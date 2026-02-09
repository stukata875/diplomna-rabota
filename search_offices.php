<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['office_code'])) {
    $_SESSION['selected_office_code'] = $_POST['office_code'];
    
    // Вземане на детайли за офиса от базата данни
    $office_code = $_POST['office_code'];
    $stmt = $conn->prepare("SELECT * FROM offices WHERE office_code = ?");
    $stmt->bind_param("s", $office_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($office = $result->fetch_assoc()) {
        $_SESSION['selected_office_name'] = $office['name'];
        $_SESSION['selected_office_address'] = $office['address'];
        $_SESSION['selected_office_type'] = $office['type'];
        $_SESSION['selected_office_city'] = $office['city'];
    }
    
    $stmt->close();
    
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Невалидни данни']);
}
?>