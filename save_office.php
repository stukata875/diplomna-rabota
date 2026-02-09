<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['office_code'])) {
    $_SESSION['selected_office_code'] = $_POST['office_code'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>