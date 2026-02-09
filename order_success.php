<?php
require_once 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = 'Успешна поръчка';
include 'header.php';

// Проверка дали има поръчка в сесията
if (!isset($_SESSION['last_order_id'])) {
    header('Location: index.php');
    exit();
}

$order_id = $_SESSION['last_order_id'];

// Вземи информация за поръчката от базата данни
$stmt = $conn->prepare("
    SELECT o.*, co.name as office_name, co.address as office_address, 
           co.city as office_city, co.type as office_type
    FROM orders o
    LEFT JOIN courier_offices co ON o.office_code = co.office_code
    WHERE o.id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Опитай да вземеш само от orders таблицата
    $stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        header('Location: index.php');
        exit();
    }
    
    $order = $result->fetch_assoc();
    $order['office_name'] = '';
    $order['office_address'] = '';
    $order['office_city'] = '';
    $order['office_type'] = '';
} else {
    $order = $result->fetch_assoc();
}
$stmt->close();

// Вземи продуктите от поръчката
$order_items = [];
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // Ако все още има кошница в сесията, използвай я
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $conn->prepare("
        SELECT id, title, author, price, image 
        FROM books 
        WHERE id IN ($placeholders)
    ");
    $stmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($book = $result->fetch_assoc()) {
        $quantity = $_SESSION['cart'][$book['id']];
        $order_items[] = [
            'id' => $book['id'],
            'title' => $book['title'],
            'author' => $book['author'],
            'price' => $book['price'],
            'quantity' => $quantity,
            'image' => $book['image']
        ];
    }
    $stmt->close();
} else {
    // Вземи продуктите от order_products таблицата
    $stmt = $conn->prepare("
        SELECT op.*, b.title, b.author, b.price, b.image 
        FROM order_products op
        JOIN books b ON op.book_id = b.id
        WHERE op.order_id = ?
    ");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $products_result = $stmt->get_result();
    
    while ($item = $products_result->fetch_assoc()) {
        $order_items[] = $item;
    }
    $stmt->close();
}

// Определи очакван срок за доставка
$delivery_days = 3; // По подразбиране 3 работни дни
if ($order['delivery_method'] === 'box_now') {
    $delivery_days = 1; // Box Now е по-бърз
}

$delivery_date = date('d.m.Y', strtotime("+$delivery_days weekday"));
?>


<style>
.success-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
    font-family: Arial, sans-serif;
}

.success-header {
    text-align: center;
    margin-bottom: 40px;
    padding: 30px;
    background: #f0fff0;
    border-radius: 10px;
    border-left: 5px solid #4CAF50;
}

.success-icon {
    font-size: 60px;
    color: #4CAF50;
    margin-bottom: 20px;
}

.success-header h1 {
    color: #2E7D32;
    font-size: 36px;
    margin-bottom: 10px;
}

.success-header p {
    color: #555;
    font-size: 18px;
    margin-bottom: 5px;
}

.order-summary-section {
    background: #fff;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.section-title {
    font-size: 24px;
    color: #333;
    margin-bottom: 25px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eee;
}

.order-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.info-card {
    padding: 20px;
    background: #f9f9f9;
    border-radius: 8px;
    border-left: 4px solid #007bff;
}

.info-card h3 {
    color: #333;
    margin-bottom: 15px;
    font-size: 18px;
}

.info-item {
    margin-bottom: 10px;
    font-size: 15px;
}

.info-label {
    font-weight: bold;
    color: #555;
    display: inline-block;
    min-width: 120px;
}

.info-value {
    color: #333;
}

/* Продукти таблица */
.products-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.products-table th {
    background: #f5f5f5;
    padding: 15px;
    text-align: left;
    color: #333;
    font-weight: bold;
    border-bottom: 2px solid #ddd;
}

.products-table td {
    padding: 15px;
    border-bottom: 1px solid #eee;
    vertical-align: top;
}

.products-table tr:hover {
    background: #f9f9f9;
}

.product-image {
    width: 60px;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
}

.product-title {
    font-weight: bold;
    color: #333;
    margin-bottom: 5px;
}

.product-author {
    color: #666;
    font-size: 14px;
}

/* Доставка секция */
.delivery-timeline {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
    position: relative;
}

.delivery-timeline::before {
    content: '';
    position: absolute;
    top: 25px;
    left: 0;
    right: 0;
    height: 3px;
    background: #e0e0e0;
    z-index: 1;
}

.timeline-step {
    position: relative;
    z-index: 2;
    text-align: center;
    flex: 1;
}

.step-circle {
    width: 50px;
    height: 50px;
    background: #fff;
    border: 3px solid #e0e0e0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-weight: bold;
    font-size: 18px;
    color: #999;
}

.step-circle.active {
    border-color: #4CAF50;
    background: #4CAF50;
    color: white;
}

.step-circle.completed {
    border-color: #4CAF50;
    background: #4CAF50;
    color: white;
}

.step-label {
    font-size: 14px;
    color: #666;
    margin-top: 5px;
}

.step-date {
    font-weight: bold;
    color: #333;
    margin-top: 5px;
}

/* Бутони */
.action-buttons {
    display: flex;
    gap: 15px;
    margin-top: 40px;
    justify-content: center;
}

.btn {
    padding: 12px 30px;
    border-radius: 6px;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-primary {
    background: #007bff;
    color: white;
    border: 2px solid #007bff;
}

.btn-primary:hover {
    background: #0056b3;
    border-color: #0056b3;
}

.btn-secondary {
    background: white;
    color: #333;
    border: 2px solid #ddd;
}

.btn-secondary:hover {
    background: #f5f5f5;
    border-color: #bbb;
}

.btn-icon {
    margin-right: 8px;
}

/* Адаптивност */
@media (max-width: 768px) {
    .success-container {
        padding: 20px 15px;
    }
    
    .success-header {
        padding: 20px;
    }
    
    .success-header h1 {
        font-size: 28px;
    }
    
    .order-info-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .delivery-timeline {
        flex-direction: column;
        gap: 20px;
    }
    
    .delivery-timeline::before {
        display: none;
    }
    
    .timeline-step {
        display: flex;
        align-items: center;
        text-align: left;
        gap: 15px;
    }
    
    .step-circle {
        margin: 0;
        flex-shrink: 0;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .btn {
        width: 100%;
    }
}
</style>

<div class="success-container">
    <div class="success-header">
        <div class="success-icon">✓</div>
        <h1>Поръчката ви е приета успешно!</h1>
        <p>Благодарим ви за доверието!</p>
        <p>Номер на поръчка: <strong>#<?= str_pad($order_id, 6, '0', STR_PAD_LEFT) ?></strong></p>
    </div>

    <div class="order-summary-section">
        <h2 class="section-title">Обобщение на поръчката</h2>
        
        <div class="order-info-grid">
            <div class="info-card">
                <h3>Информация за поръчката</h3>
                <div class="info-item">
                    <span class="info-label">Номер:</span>
                    <span class="info-value">#<?= str_pad($order_id, 6, '0', STR_PAD_LEFT) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Дата:</span>
                    <span class="info-value"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Статус:</span>
                    <span class="info-value" style="color: #4CAF50; font-weight: bold;">Обработва се</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Начин на плащане:</span>
                    <span class="info-value"><?= $order['payment_method'] == 'cash' ? 'Наложен платеж' : 'Кредитна карта' ?></span>
                </div>
            </div>
            
            <div class="info-card">
                <h3>Данни за доставка</h3>
                <div class="info-item">
                    <span class="info-label">Куриер:</span>
                    <span class="info-value">
                        <?php 
                        $courier_names = [
                            'box_now' => 'BOX NOW',
                            'econt' => 'ЕКОНТ',
                            'speedy' => 'SPEEDY'
                        ];
                        echo $courier_names[$order['delivery_method']] ?? $order['delivery_method'];
                        ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Офис/Автомат:</span>
                    <span class="info-value"><?= htmlspecialchars($order['office_name']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Адрес:</span>
                    <span class="info-value"><?= htmlspecialchars($order['office_address']) ?>, <?= htmlspecialchars($order['office_city']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Тип:</span>
                    <span class="info-value"><?= $order['office_type'] == 'automat' ? 'Автомат' : 'Офис' ?></span>
                </div>
            </div>
            
            <div class="info-card">
                <h3>Контактна информация</h3>
                <div class="info-item">
                    <span class="info-label">Име:</span>
                    <span class="info-value"><?= htmlspecialchars($order['customer_name']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Телефон:</span>
                    <span class="info-value"><?= htmlspecialchars($order['phone']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Имейл:</span>
                    <span class="info-value"><?= htmlspecialchars($order['email']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Забележка:</span>
                    <span class="info-value"><?= !empty($order['notes']) ? htmlspecialchars($order['notes']) : 'Няма' ?></span>
                </div>
            </div>
        </div>
        
        <h3 class="section-title">Продукти</h3>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Продукт</th>
                    <th>Количество</th>
                    <th>Единична цена</th>
                    <th>Общо</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $subtotal = 0;
                foreach ($order_items as $item): 
                    $item_total = $item['price'] * $item['quantity'];
                    $subtotal += $item_total;
                ?>
                <tr>
                    <td>
                        <div style="display: flex; gap: 15px; align-items: center;">
                            <img src="<?= htmlspecialchars($item['image'] ?? 'images/no-image.jpg') ?>" 
                                 alt="<?= htmlspecialchars($item['title']) ?>" 
                                 class="product-image">
                            <div>
                                <div class="product-title"><?= htmlspecialchars($item['title']) ?></div>
                                <div class="product-author"><?= htmlspecialchars($item['author']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'], 2) ?> лв.</td>
                    <td><?= number_format($item_total, 2) ?> лв.</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">Стойност на продуктите:</td>
                    <td style="font-weight: bold;"><?= number_format($subtotal, 2) ?> лв.</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">Цена на доставка:</td>
                    <td style="font-weight: bold;"><?= number_format($order['delivery_price'], 2) ?> лв.</td>
                </tr>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold; font-size: 18px;">Общо за плащане:</td>
                    <td style="font-weight: bold; font-size: 18px; color: #2E7D32;"><?= number_format($order['total_amount'], 2) ?> лв.</td>
                </tr>
            </tfoot>
        </table>
    </div>
    
    <div class="order-summary-section">
        <h2 class="section-title">Очакван срок за доставка</h2>
        
        <div class="delivery-timeline">
            <div class="timeline-step">
                <div class="step-circle completed">1</div>
                <div class="step-label">Поръчката е приета</div>
                <div class="step-date"><?= date('d.m.Y') ?></div>
            </div>
            
            <div class="timeline-step">
                <div class="step-circle active">2</div>
                <div class="step-label">Обработва се</div>
                <div class="step-date"><?= date('d.m.Y', strtotime('+1 weekday')) ?></div>
            </div>
            
            <div class="timeline-step">
                <div class="step-circle">3</div>
                <div class="step-label">Изпратена</div>
                <div class="step-date"><?= date('d.m.Y', strtotime('+2 weekday')) ?></div>
            </div>
            
            <div class="timeline-step">
                <div class="step-circle">4</div>
                <div class="step-label">Доставена</div>
                <div class="step-date"><?= $delivery_date ?></div>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 30px; padding: 20px; background: #f0f7ff; border-radius: 8px;">
            <h3 style="color: #007bff; margin-bottom: 10px;">📦 Очаквана дата на доставка</h3>
            <p style="font-size: 18px; color: #333; margin-bottom: 10px;">
                <strong><?= $delivery_date ?></strong>
            </p>
            <p style="color: #666;">
                Доставката се извършва в рамките на <strong><?= $delivery_days ?> работни дни</strong> след потвърждаване на поръчката.
            </p>
        </div>
    </div>
    
    <div class="action-buttons">
        <a href="index.php" class="btn btn-secondary">
            <span class="btn-icon">🏠</span>
            Към начална страница
        </a>
        <a href="my_orders.php" class="btn btn-primary">
            <span class="btn-icon">📋</span>
            Моите поръчки
        </a>
    </div>
</div>

<?php
// Изчисти сесията след успешна поръчка
unset($_SESSION['cart']);
unset($_SESSION['selected_city']);
unset($_SESSION['selected_office_code']);

$conn->close();
include 'footer.php';
?>