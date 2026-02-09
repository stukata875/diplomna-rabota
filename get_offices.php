<?php
require_once 'config.php';

$city = trim($_POST['city'] ?? '');
$company = trim($_POST['company'] ?? '');
$type = $_POST['type'] ?? 'all';

if (!$city || !$company) {
    echo json_encode(['success' => false]);
    exit;
}

$sql = "SELECT * FROM courier_offices WHERE city = ? AND courier_company = ?";
$params = [$city, $company];
$types = "ss";

if ($type !== 'all') {
    $sql .= " AND type = ?";
    $params[] = $type;
    $types .= "s";
}

$sql .= " ORDER BY name";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

$res = $stmt->get_result();
$offices = [];

while ($row = $res->fetch_assoc()) {
    $offices[] = $row;
}

echo json_encode([
    'success' => true,
    'offices' => $offices
]);