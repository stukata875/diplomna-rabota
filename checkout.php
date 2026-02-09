<?php
require_once 'config.php';
$page_title = 'Плащане и доставка';
include 'header.php';

// Проверка дали кошницата е празна
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit();
}

// Задай $selected_delivery да е празно ако няма избор
$selected_delivery = $_SESSION['delivery_method'] ?? null;

// Вземане на данни за кошницата
$cart_items = [];
$total_price = 0;

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
    $subtotal = $book['price'] * $quantity;
    $total_price += $subtotal;
    
    $cart_items[] = [
        'id' => $book['id'],
        'title' => $book['title'],
        'author' => $book['author'],
        'price' => $book['price'],
        'quantity' => $quantity,
        'subtotal' => $subtotal
    ];
}
$stmt->close();

// Цени на доставките
$delivery_options = [
    'box_now' => [
        'name' => 'BOX NOW',
        'description' => 'Автомат на Box Now',
        'price' => 3.36,
        'currency' => 'лв.',
        'needs_office' => true,
        'company' => 'box_now',
        'office_type' => 'automat',
        'color' => '#FF6B35'
    ],
    'econt' => [
        'name' => 'ЕКОНТ',
        'description' => 'Еконт Експрес - до офис или Еконтомат 24/7',
        'price' => 5.98,
        'currency' => 'лв.',
        'needs_office' => true,
        'company' => 'econt',
        'office_type' => 'all',
        'color' => '#00A9E0'
    ],
    'speedy' => [
        'name' => 'SPEEDY',
        'description' => 'Спиди - до офис или автомат',
        'price' => 5.98,
        'currency' => 'лв.',
        'needs_office' => true,
        'company' => 'speedy',
        'office_type' => 'all',
        'color' => '#D50032'
    ]
];

// Изчисляване на цените
if ($selected_delivery && isset($delivery_options[$selected_delivery])) {
    $delivery_price = $delivery_options[$selected_delivery]['price'];
    $final_total = $total_price + $delivery_price;
} else {
    $delivery_price = 0;
    $final_total = $total_price;
}
?>

<style>
body {
    margin: 0;
    padding: 0;
    background-color: #f5f5f5;
}

.checkout-container {
    width: 100%;
    max-width: 1600px;
    margin: 0 auto;
    padding: 30px;
    min-height: 80vh;
    font-family: Arial, sans-serif;
    box-sizing: border-box;
}

.checkout-header {
    margin-bottom: 40px;
    padding-bottom: 10px;
}

.checkout-header h1 {
    color: #333;
    font-size: 36px;
    font-weight: bold;
    margin: 0;
    padding-bottom: 10px;
}

.checkout-content {
    display: flex;
    gap: 50px;
    align-items: flex-start;
}

/* Лява част - формата */
.form-section-wrapper {
    flex: 1;
    min-width: 0;
}

.checkout-form-section {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 40px;
    width: 100%;
}

/* Дясна част - обобщение */
.order-summary-wrapper {
    width: 400px;
    flex-shrink: 0;
}

.order-summary {
    background: #fff;
    border: 2px solid #000;
    border-radius: 8px;
    padding: 30px;
    position: sticky;
    top: 30px;
}

/* Общи стилове за форма */
.checkout-form-section h2 {
    font-size: 20px;
    color: #333;
    margin-bottom: 25px;
    font-weight: bold;
    padding-bottom: 10px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.form-group label.required:after {
    content: " *";
    color: #ff4444;
    font-weight: bold;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box;
    transition: all 0.3s;
    background: #fff;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #666;
}

/* Delivery options - хоризонтално разположение */
.delivery-options {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
    margin-bottom: 25px;
}

.delivery-option {
    display: flex;
    flex-direction: column;
    padding: 15px;
    border: 2px solid #ddd;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s;
    background: #f9f9f9;
    text-align: center;
    height: 150px;
    justify-content: space-between;
}

.delivery-option:hover {
    border-color: #666;
    background: #fff;
}

.delivery-option.selected {
    border-color: #000;
    background: #fff;
    box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
}

.delivery-radio {
    align-self: flex-start;
    width: 18px;
    height: 18px;
    cursor: pointer;
    margin-bottom: 10px;
}

.delivery-details {
    flex: 1;
}

.delivery-name {
    font-weight: bold;
    color: #333;
    margin-bottom: 8px;
    font-size: 16px;
    text-transform: uppercase;
}

.delivery-description {
    font-size: 12px;
    color: #666;
    margin-bottom: 8px;
    line-height: 1.3;
}

.delivery-euro-price {
    font-size: 12px;
    color: #666;
    font-weight: 600;
    margin-bottom: 8px;
}

.delivery-price {
    font-weight: bold;
    font-size: 18px;
    color: #333;
    margin-top: 8px;
}

/* Офис локатор секция */

.office-locator-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;  /* Промени от 15px на 10px */
    margin-bottom: 15px;
}

/* Намали отстоянията на лейбълите */
.office-locator-title {
    font-size: 16px;
    font-weight: bold;
    color: #333;
    margin-bottom: 10px;  /* Промени от 15px на 10px */
    padding-bottom: 5px;
    border-bottom: 1px solid #ddd;
}

/* Намали padding на картата за офис */
.office-card {
    padding: 12px;  /* Промени от 15px на 12px */
    border: 1px solid #e0e0e0;  /* Промени от 2px на 1px */
    border-radius: 6px;
    margin-bottom: 10px;  /* Промени от 15px на 10px */
    background: #fff;
    cursor: pointer;
    transition: all 0.3s;
}

/* Намали padding на избрания офис */
.selected-office-display {
    padding: 15px;  /* Промени от 20px на 15px */
    border: 1px solid #007bff;  /* Промени от 2px на 1px */
    border-radius: 6px;
    background: #f0f7ff;
    margin-top: 10px;
}

/* По-малки шрифтове */
.office-card-title {
    font-weight: bold;
    font-size: 14px;  /* Промени от 15px на 14px */
    color: #333;
    flex: 1;
}

.office-card-details {
    font-size: 12px;  /* Промени от 13px на 12px */
    color: #555;
}

/* По-малки баджове */
.courier-badge {
    display: inline-block;
    padding: 2px 6px;  /* Промени от 3px 8px на 2px 6px */
    border-radius: 10px;
    font-size: 9px;    /* Промени от 10px на 9px */
    font-weight: bold;
    color: white;
    margin-right: 6px; /* Промени от 8px на 6px */
    text-transform: uppercase;
}

.office-type-indicator {
    display: inline-block;
    padding: 3px 8px;  /* Промени от 4px 10px на 3px 8px */
    background: #4CAF50;
    color: white;
    border-radius: 10px;
    font-size: 10px;   /* Промени от 11px на 10px */
    font-weight: bold;
    text-transform: uppercase;
}

.loading-spinner {
    text-align: center;
    padding: 20px;
    color: #666;
}

.loading-spinner:before {
    content: "⏳";
    margin-right: 8px;
}

/* Плащане - хоризонтално разположение */
.payment-section {
    margin-bottom: 25px;
}

.payment-options {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.payment-option {
    display: flex;
    align-items: center;
    padding: 15px;
    border: 2px solid #ddd;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s;
    background: #f9f9f9;
}

.payment-option:hover {
    border-color: #666;
    background: #fff;
}

.payment-option.selected {
    border-color: #000;
    background: #fff;
}

.payment-radio {
    margin-right: 12px;
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.payment-label {
    font-weight: 600;
    cursor: pointer;
    font-size: 14px;
    flex: 1;
}

/* Лична информация - 2 колони */
.personal-info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
    margin-bottom: 25px;
}

/* Бутон за поръчка */
.submit-order-btn {
    width: 100%;
    background: #000;
    color: #fff;
    padding: 18px;
    border: none;
    border-radius: 6px;
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    text-transform: uppercase;
    margin-top: 25px;
}

.submit-order-btn:hover {
    background: #333;
}

/* Обобщение */
.order-summary h2 {
    font-size: 20px;
    color: #333;
    margin-bottom: 20px;
    font-weight: bold;
    padding-bottom: 10px;
    border-bottom: 2px solid #000;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}

.summary-item.total {
    border-bottom: none;
    border-top: 2px solid #000;
    margin-top: 20px;
    padding-top: 18px;
    font-weight: bold;
    font-size: 18px;
}

.summary-label {
    color: #333;
    flex: 1;
}

.summary-value {
    font-weight: 600;
    color: #333;
    text-align: right;
    min-width: 100px;
}

.summary-item.total .summary-value {
    font-size: 20px;
    color: #000;
}

/* Заглавия на секции */
.form-section-title {
    font-size: 18px;
    font-weight: bold;
    margin: 25px 0 15px 0;
    color: #333;
    padding-bottom: 8px;
}

.form-section-title:first-child {
    margin-top: 0;
}

/* Обратно към кошницата */
.back-to-cart {
    text-align: center;
    margin-top: 20px;
}

.back-to-cart a {
    color: #333;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    padding: 10px 20px;
    border: 1px solid #333;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s;
}

.back-to-cart a:hover {
    background: #333;
    color: #fff;
}



/* Пратка информация */
.package-info {
    margin-top: 15px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: #f9f9f9;
    font-size: 12px;
    color: #666;
    line-height: 1.4;
}


/* Стилове за badge на куриерска компания */
.courier-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 10px;
    font-weight: bold;
    color: white;
    margin-right: 8px;
    text-transform: uppercase;
}

.courier-badge.box_now {
    background-color: #FF6B35;
}

.courier-badge.econt {
    background-color: #00A9E0;
}

.courier-badge.speedy {
    background-color: #D50032;
}

/* Стилове за карта на офис */
.office-card {
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    margin-bottom: 15px;
    background: #fff;
    cursor: pointer;
    transition: all 0.3s;
}

.office-card:hover {
    border-color: #007bff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.office-card.selected {
    border-color: #007bff;
    background: #f0f7ff;
}

.office-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}

.office-card-title {
    font-weight: bold;
    font-size: 15px;
    color: #333;
    flex: 1;
}

.office-type-indicator {
    display: inline-block;
    padding: 4px 10px;
    background: #4CAF50;
    color: white;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
}

.office-type-indicator.automat {
    background: #FF9800;
}

.office-card-details {
    font-size: 13px;
    color: #555;
}

.office-code {
    font-family: monospace;
    background: #f5f5f5;
    padding: 2px 6px;
    border-radius: 4px;
    margin-right: 10px;
}

.office-address {
    margin-top: 5px;
    line-height: 1.4;
}

/* Избран офис */
.selected-office-display {
    padding: 20px;
    border: 2px solid #007bff;
    border-radius: 8px;
    background: #f0f7ff;
    margin-top: 15px;
}

.selected-office-header {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.selected-office-header h4 {
    margin: 0;
    font-size: 16px;
    color: #333;
    flex: 1;
}

.selected-office-details {
    font-size: 14px;
    color: #555;
}

.selected-office-details div {
    margin-bottom: 6px;
}

.selected-office-details strong {
    color: #333;
    margin-right: 5px;
}

/* Индикатор за зареждане */
.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
}

.loading-spinner {
    font-size: 14px;
    color: #007bff;
    display: flex;
    align-items: center;
    gap: 8px;
}

.loading-spinner::before {
    content: "";
    width: 16px;
    height: 16px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Заглавие на резултати */
.office-results-title {
    font-size: 15px;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 2px solid #eee;
}

/* Няма резултати */
.no-results {
    text-align: center;
    padding: 30px;
    color: #666;
    font-style: italic;
    background: #f9f9f9;
    border-radius: 8px;
    border: 1px dashed #ddd;
}

/* Скриване на скролбара в office-results */
.office-results {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 5px;
}

.office-results::-webkit-scrollbar {
    width: 6px;
}

.office-results::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.office-results::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.office-results::-webkit-scrollbar-thumb:hover {
    background: #555;
}


.loading-spinner {
    font-size: 14px;
    color: #007bff;
    display: flex;
    align-items: center;
    gap: 8px;
}

.loading-spinner::before {
    content: "";
    width: 16px;
    height: 16px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #007bff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* Направи полето за град по-компактно */
#city {
    max-width: 400px; /* Ограничи ширината */
    padding: 10px 12px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

/* Адаптивност за мобилни */
@media (max-width: 768px) {
    #city {
        max-width: 100%;
    }
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Адаптивност */
@media (max-width: 1200px) {
    .checkout-container {
        padding: 20px;
    }
    
    .checkout-content {
        gap: 30px;
    }
    
    .order-summary-wrapper {
        width: 350px;
    }
    
    .delivery-options {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 992px) {
    .checkout-content {
        flex-direction: column;
    }
    
    .order-summary-wrapper {
        width: 100%;
    }
    
    .delivery-options {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .personal-info-grid {
        grid-template-columns: 1fr;
    }
    
    .office-locator-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .checkout-container {
        padding: 15px;
    }
    
    .checkout-form-section {
        padding: 25px;
    }
    
    .delivery-options {
        grid-template-columns: 1fr;
    }
    
    .payment-options {
        grid-template-columns: 1fr;
    }
    
    .checkout-header h1 {
        font-size: 28px;
    }
}
</style>


<div class="checkout-container">
    <div class="checkout-header">
        <h1>Адрес за доставка</h1>
    </div>
    
    <div class="checkout-content">
        <!-- Лява част - Форма -->
        <div class="form-section-wrapper">
            <div class="checkout-form-section">
               <form id="checkoutForm" method="POST">
                    <!-- Имейл секция -->
                    <div class="form-section-title">Имейл адрес</div>
                    <div class="form-group">
                        <label for="email" class="required">Имейл адрес</label>
                        <input type="email" id="email" name="email" required 
                               value="<?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>">
                    </div>
                    
                    <!-- Лични данни -->
                    <div class="form-section-title">Лични данни</div>
                    <div class="personal-info-grid">
                        <div class="form-group">
                            <label for="name" class="required">Име</label>
                            <input type="text" id="name" name="name" required 
                                   value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="lastname" class="required">Фамилия</label>
                            <input type="text" id="lastname" name="lastname" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone" class="required">Телефон</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="country" class="required">Държава</label>
                            <select id="country" name="country" required>
                                <option value="България" selected>България</option>
                            </select>
                        </div>
                    </div>

                    <!-- Поле за град -->
                    <div class="form-group">
                        <label for="city" class="required">Град</label>
                        <input type="text" id="city" name="city" required
                               value="<?= htmlspecialchars($_SESSION['selected_city'] ?? '') ?>"
                               placeholder="Въведете град"
                               onblur="loadOffices()">
                    </div>

                    <!-- Метод на доставка -->
                    <div class="form-section-title">Методи на доставка</div>
                    <div class="form-group">
                        <div class="delivery-options">
                            <?php foreach ($delivery_options as $key => $option): ?>
                            <div class="delivery-option <?= $selected_delivery == $key ? 'selected' : '' ?>" 
                                 onclick="selectDelivery('<?= $key ?>')">
                                <input type="radio" name="delivery_method" value="<?= $key ?>" 
                                       id="delivery_<?= $key ?>" class="delivery-radio"
                                       <?= $selected_delivery == $key ? 'checked' : '' ?>>
                                <div class="delivery-details">
                                    <div class="delivery-name"><?= $option['name'] ?></div>
                                    <div class="delivery-description"><?= $option['description'] ?></div>
                                </div>
                                <div class="delivery-price">
                                    <?= number_format($option['price'], 2) ?> <?= $option['currency'] ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Информация за избран офис -->
                    <input type="hidden" id="office_code" name="office_code" value="<?= htmlspecialchars($_SESSION['selected_office_code'] ?? '') ?>">

                    <div class="form-group">
                        <label class="required">Избран офис</label>
                        <div id="selected_office_display" class="selected-office-display" style="<?= empty($_SESSION['selected_office_code']) ? 'display: none;' : '' ?>">
                            <?php if (isset($_SESSION['selected_office_code']) && !empty($_SESSION['selected_office_code'])): ?>
                                <?php
                                $selected_office_code = $_SESSION['selected_office_code'];
                                $stmt = $conn->prepare("SELECT * FROM courier_offices WHERE office_code = ?");
                                $stmt->bind_param("s", $selected_office_code);
                                $stmt->execute();
                                $office_result = $stmt->get_result();
                                
                                if ($office = $office_result->fetch_assoc()):
                                    $courier_name = '';
                                    $courier_class = '';
                                    switch($office['courier_company']) {
                                        case 'box_now': $courier_name = 'BOX NOW'; $courier_class = 'box_now'; break;
                                        case 'econt': $courier_name = 'ЕКОНТ'; $courier_class = 'econt'; break;
                                        case 'speedy': $courier_name = 'SPEEDY'; $courier_class = 'speedy'; break;
                                    }
                                ?>
                                <div class="selected-office-header">
                                    <span class="courier-badge <?= $courier_class ?>"><?= $courier_name ?></span>
                                    <h4 style="font-size: 14px; margin: 0;"><?= htmlspecialchars($office['name']) ?></h4>
                                    <span class="office-type-indicator <?= $office['type'] == 'automat' ? 'automat' : '' ?>">
                                        <?= $office['type'] == 'automat' ? 'АВТОМАТ' : 'ОФИС' ?>
                                    </span>
                                </div>
                                <div class="selected-office-details" style="font-size: 12px;">
                                    <div><strong>Код:</strong> <?= htmlspecialchars($office['office_code']) ?></div>
                                    <div><strong>Адрес:</strong> <?= htmlspecialchars($office['address']) ?></div>
                                    <div><strong>Град:</strong> <?= htmlspecialchars($office['city']) ?></div>
                                </div>
                                <?php 
                                $stmt->close();
                                endif; 
                                ?>
                            <?php endif; ?>
                        </div>
                        <div id="no_office_selected" style="<?= empty($_SESSION['selected_office_code']) ? '' : 'display: none; margin-top: 10px;' ?>">
                            <div class="no-results" style="margin: 0; padding: 15px; font-size: 13px;">
                                Все още няма избран офис. Въведете град, изберете куриер и след това офис от списъка по-долу.
                            </div>
                        </div>
                    </div>

                    <!-- Резултати от офис търсене -->
                    <div id="officeResultsContainer" style="position: relative; min-height: 150px; margin-top: 10px;">
                        <div id="officeResults" class="office-results" style="max-height: 300px;">
                            <!-- Тук ще се показват офисите -->
                        </div>
                        <div id="officeLoading" class="loading-overlay" style="display: none;">
                            <div class="loading-spinner" style="font-size: 13px;">Зареждане на офиси...</div>
                        </div>
                    </div>
                    
                    <!-- Метод на плащане -->
                    <div class="form-section-title">Начин на плащане</div>
                    <div class="payment-section">
                        <div class="payment-options">
                            <div class="payment-option selected" onclick="selectPayment('cash')">
                                <input type="radio" name="payment_method" value="cash" id="payment_cash" checked>
                                <label for="payment_cash" class="payment-label">Наложен платеж</label>
                            </div>
                            <div class="payment-option" onclick="selectPayment('card')">
                                <input type="radio" name="payment_method" value="card" id="payment_card">
                                <label for="payment_card" class="payment-label">Кредитна/дебитна карта</label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-order-btn" id="submitOrderBtn">
                        Потвърди поръчката
                    </button>
                </form>
                
                <div class="back-to-cart">
                    <a href="cart.php">← Назад към кошницата</a>
                </div>
            </div>
        </div>
        
        <!-- Дясна част - Обобщение -->
        <div class="order-summary-wrapper">
            <div class="order-summary">
                <h2>Вашата поръчка</h2>
                
                <?php foreach ($cart_items as $item): ?>
                <div class="summary-item">
                    <span class="summary-label">
                        <?= htmlspecialchars($item['title']) ?> × <?= $item['quantity'] ?>
                    </span>
                    <span class="summary-value"><?= number_format($item['subtotal'], 2) ?> лв.</span>
                </div>
                <?php endforeach; ?>
                
                <div class="summary-item">
                    <span class="summary-label">Доставка</span>
                    <span class="summary-value" id="delivery-summary">
                        <?= $selected_delivery ? number_format($delivery_price, 2) . ' лв.' : '---' ?>
                    </span>
                </div>
                
                <div class="summary-item total">
                    <span class="summary-label">Общо за плащане</span>
                    <span class="summary-value" id="total-summary">
                        <?= number_format($final_total, 2) ?> лв.
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let selectedDeliveryCompany = <?= $selected_delivery ? "'$selected_delivery'" : 'null' ?>;
let allOffices = [];

const deliveryPrices = {
    box_now: <?= $delivery_options['box_now']['price'] ?>,
    econt: <?= $delivery_options['econt']['price'] ?>,
    speedy: <?= $delivery_options['speedy']['price'] ?>
};

// ===== ДОСТАВКА =====
function selectDelivery(method) {
    selectedDeliveryCompany = method;

    // Обновяване на визуалния избор
    document.querySelectorAll('.delivery-option').forEach(o => {
        o.classList.remove('selected');
        o.querySelector('input').checked = false;
    });

    const radio = document.getElementById('delivery_' + method);
    radio.checked = true;
    radio.closest('.delivery-option').classList.add('selected');

    // Обновяване на цената
    updateDeliveryPrice(method);
    
    // Изчистване на избрания офис
    clearSelectedOffice();

    // Запазване в сесията
    fetch('save_delivery.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `method=${method}`
    });

    // Презареждане на офиси, ако има град
    const city = document.getElementById('city').value.trim();
    if (city) loadOffices();
}

function updateDeliveryPrice(method) {
    if (!method) {
        document.getElementById('delivery-summary').textContent = '---';
        document.getElementById('total-summary').textContent = 
            <?= $total_price ?>.toFixed(2).replace('.', ',') + ' лв.';
        return;
    }
    
    const delivery = deliveryPrices[method];
    document.getElementById('delivery-summary').textContent =
        delivery.toFixed(2).replace('.', ',') + ' лв.';

    const total = <?= $total_price ?> + delivery;
    document.getElementById('total-summary').textContent =
        total.toFixed(2).replace('.', ',') + ' лв.';
}

// ===== ПЛАЩАНЕ =====
function selectPayment(method) {
    document.querySelectorAll('.payment-option').forEach(o => {
        o.classList.remove('selected');
    });
    
    const selectedOption = document.querySelector(`.payment-option input[value="${method}"]`).closest('.payment-option');
    selectedOption.classList.add('selected');
    selectedOption.querySelector('input').checked = true;
}

// ===== ОФИСИ =====
function loadOffices() {
    const city = document.getElementById('city').value.trim();
    if (!city || !selectedDeliveryCompany) {
        alert('Моля, въведете град.');
        return;
    }

    // Показване на индикатор за зареждане
    const loadingEl = document.getElementById('officeLoading');
    const resultsEl = document.getElementById('officeResults');
    loadingEl.style.display = 'flex';
    resultsEl.innerHTML = '';

    // Изчистване на избрания офис
    clearSelectedOffice();

    fetch('get_offices.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `city=${encodeURIComponent(city)}&company=${selectedDeliveryCompany}`
    })
    .then(response => {
        if (!response.ok) throw new Error('Грешка в мрежата');
        return response.json();
    })
    .then(data => {
        loadingEl.style.display = 'none';
        
        if (data.success && data.offices && data.offices.length > 0) {
            allOffices = data.offices;
            displayOffices(data.offices);
        } else {
            resultsEl.innerHTML = `
                <div class="no-results">
                    ${data.message || 'Няма намерени офиси за този град и куриер.'}
                </div>
            `;
        }
    })
    .catch(error => {
        loadingEl.style.display = 'none';
        resultsEl.innerHTML = `
            <div class="no-results">
                Грешка при зареждане на офиси: ${error.message}
            </div>
        `;
        console.error('Грешка:', error);
    });
}

function displayOffices(offices) {
    const resultsEl = document.getElementById('officeResults');
    
    if (offices.length === 0) {
        resultsEl.innerHTML = '<div class="no-results">Няма намерени офиси</div>';
        return;
    }

    // Заглавие с брой
    resultsEl.innerHTML = `
        <div class="office-results-title">
            Намерени офиси: ${offices.length} - изберете един от тях
        </div>
    `;

    // Добавяне на всеки офис като карта
    offices.forEach(office => {
        const courierClass = selectedDeliveryCompany;
        const courierName = courierClass === 'box_now' ? 'BOX NOW' : 
                          courierClass === 'econt' ? 'ЕКОНТ' : 'SPEEDY';
        
        const officeEl = document.createElement('div');
        officeEl.className = 'office-card';
        
        officeEl.innerHTML = `
            <div class="office-card-header">
                <span class="courier-badge ${courierClass}">${courierName}</span>
                <span class="office-card-title">${office.name}</span>
                <span class="office-type-indicator ${office.type === 'automat' ? 'automat' : ''}">
                    ${office.type === 'automat' ? 'АВТОМАТ' : 'ОФИС'}
                </span>
            </div>
            <div class="office-card-details">
                <span class="office-code">Код: ${office.office_code}</span>
                <div class="office-address">${office.address}</div>
            </div>
        `;
        
        officeEl.onclick = () => selectOffice(office);
        resultsEl.appendChild(officeEl);
    });
}

function selectOffice(office, event) {
    // Обновяване на скритото поле
    document.getElementById('office_code').value = office.office_code;
    
    // Обновяване на визуалното представяне
    const courierClass = selectedDeliveryCompany;
    const courierName = courierClass === 'box_now' ? 'BOX NOW' : 
                      courierClass === 'econt' ? 'ЕКОНТ' : 'SPEEDY';
    
    const displayHtml = `
        <div class="selected-office-header">
            <span class="courier-badge ${courierClass}">${courierName}</span>
            <h4>${office.name}</h4>
            <span class="office-type-indicator ${office.type === 'automat' ? 'automat' : ''}">
                ${office.type === 'automat' ? 'АВТОМАТ' : 'ОФИС'}
            </span>
        </div>
        <div class="selected-office-details">
            <div><strong>Код:</strong> ${office.office_code}</div>
            <div><strong>Адрес:</strong> ${office.address}</div>
            <div><strong>Град:</strong> ${office.city}</div>
        </div>
    `;
    
    document.getElementById('selected_office_display').innerHTML = displayHtml;
    document.getElementById('selected_office_display').style.display = 'block';
    document.getElementById('no_office_selected').style.display = 'none';
    
    // Маркиране на избраната карта
    document.querySelectorAll('.office-card').forEach(card => {
        card.classList.remove('selected');
    });
    event.currentTarget.classList.add('selected');
    
    // Запазване в сесията
    fetch('save_office.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `office_code=${office.office_code}`
    });
}

function clearSelectedOffice() {
    document.getElementById('office_code').value = '';
    document.getElementById('selected_office_display').style.display = 'none';
    document.getElementById('no_office_selected').style.display = 'block';
    document.getElementById('selected_office_display').innerHTML = '';
    
    document.querySelectorAll('.office-card').forEach(card => {
        card.classList.remove('selected');
    });
}

// Инициализация при зареждане
document.addEventListener('DOMContentLoaded', function() {
    selectPayment('cash');
    
    // Зареждане на офиси, ако има град в сесията
    const cityInput = document.getElementById('city');
    if (cityInput.value.trim()) {
        loadOffices();
    }
});

// Валидация на формата
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Проверка за офис
    const city = document.getElementById('city').value.trim();
    const officeCode = document.getElementById('office_code').value;
    const deliveryMethod = document.querySelector('input[name="delivery_method"]:checked');
    
    if (!city) {
        alert('Моля, въведете град.');
        return;
    }
    
    if (!deliveryMethod) {
        alert('Моля, изберете куриер.');
        return;
    }
    
    if (!officeCode) {
        alert('Моля, изберете офис от списъка.');
        return;
    }
    
    const submitBtn = document.getElementById('submitOrderBtn');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Обработка...';
    
    const formData = new FormData(this);
    
    fetch('process_order.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Пренасочване към страницата за успешна поръчка
            window.location.href = 'order_success.php';
        } else {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
            alert(data.message || 'Грешка при обработка на поръчката.');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
        alert('Възникна грешка. Моля, опитайте отново.');
    });
});
</script>

<?php 
$conn->close();
include 'footer.php';
?>