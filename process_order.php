<?php
require_once 'config.php';
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Невалиден метод']);
    exit();
}

// Проверка за задължителни полета
$required_fields = ['name', 'email', 'phone', 'city', 'delivery_method', 'payment_method', 'office_code'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => 'Моля, попълнете всички полета']);
        exit();
    }
}

try {
    $conn->begin_transaction();
    
    // Генериране на номер на поръчка
    $order_number = 'ORD' . date('Ymd') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    
    // Изчисляване на суми
    $total_price = 0;
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $book_id => $quantity) {
            $stmt = $conn->prepare("SELECT price FROM books WHERE id = ?");
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($book = $result->fetch_assoc()) {
                $total_price += $book['price'] * $quantity;
            }
            $stmt->close();
        }
    }
    
    // Определяне на цена на доставка
    $delivery_price = 0;
    $delivery_method = $_POST['delivery_method'];
    if ($delivery_method === 'box_now') {
        $delivery_price = 3.36;
    } elseif (in_array($delivery_method, ['econt', 'speedy'])) {
        $delivery_price = 5.98;
    }
    
    $total_amount = $total_price + $delivery_price;
    
    // Запазване на поръчката
    $stmt = $conn->prepare("
        INSERT INTO orders (
            order_number, customer_name, email, phone, delivery_method, 
            office_code, payment_method, subtotal, delivery_price, total_amount,
            notes, status, user_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $user_id = $_SESSION['user_id'] ?? null;
    $notes = $_POST['notes'] ?? '';
    $status = 'pending';
    $customer_name = trim($_POST['name'] . ' ' . ($_POST['lastname'] ?? ''));
    
    $stmt->bind_param(
        "sssssssddsssi",
        $order_number,
        $customer_name,
        $_POST['email'],
        $_POST['phone'],
        $_POST['delivery_method'],
        $_POST['office_code'],
        $_POST['payment_method'],
        $total_price,
        $delivery_price,
        $total_amount,
        $notes,
        $status,
        $user_id
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Грешка при запазване на поръчката: ' . $stmt->error);
    }
    
    $order_id = $stmt->insert_id;
    $stmt->close();
    
    // Запазване на продуктите от поръчката
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $book_id => $quantity) {
            $stmt = $conn->prepare("SELECT price FROM books WHERE id = ?");
            $stmt->bind_param("i", $book_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($book = $result->fetch_assoc()) {
                $product_stmt = $conn->prepare("
                    INSERT INTO order_products (order_id, book_id, quantity, price)
                    VALUES (?, ?, ?, ?)
                ");
                $product_stmt->bind_param("iiid", $order_id, $book_id, $quantity, $book['price']);
                $product_stmt->execute();
                $product_stmt->close();
            }
            $stmt->close();
        }
    }
    
    $conn->commit();
    unset($_SESSION['cart']);
unset($_SESSION['selected_city']);
unset($_SESSION['selected_office_code']);
    // Запази ID на поръчката в сесията
    $_SESSION['last_order_id'] = $order_id;
    $_SESSION['order_info'] = [
        'order_number' => $order_number,
        'total_amount' => $total_amount,
        'customer_name' => $customer_name,
        'email' => $_POST['email'],
        'phone' => $_POST['phone'],
        'delivery_method' => $_POST['delivery_method'],
        'office_code' => $_POST['office_code'],
        'payment_method' => $_POST['payment_method']
    ];
    
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'order_number' => $order_number,
        'message' => 'Поръчката е успешно създадена'
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Грешка при обработка на поръчката: ' . $e->getMessage()
    ]);
}
?>