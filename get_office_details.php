<?php
require_once 'config.php';

header('Content-Type: application/json');

$id = $_POST['id'] ?? 0;

if (empty($id)) {
    echo json_encode(['success' => false, 'message' => 'Невалиден ID']);
    exit();
}

$stmt = $conn->prepare("
    SELECT id, office_code, city, name, address, type 
    FROM courier_offices 
    WHERE id = ? AND is_active = 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($office = $result->fetch_assoc()) {
    echo json_encode(['success' => true, 'office' => $office]);
} else {
    echo json_encode(['success' => false, 'message' => 'Офисът не е намерен']);
}

$stmt->close();
$conn->close();
?>