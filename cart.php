<?php
require_once 'config.php';
$page_title = 'Кошница';
include 'header.php';

$cart_items = [];
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = str_repeat('?,', count($ids) - 1) . '?';
    
    // Вземане на всички книги от кошницата
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
            'image' => $book['image'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
    $stmt->close();
}

// Изчисляване на доставката
$delivery_price = 0;
if ($total_price > 0 && $total_price < 20) {
    $delivery_price = 4.90;
} elseif ($total_price >= 100) {
    $delivery_price = 0;
} else {
    $delivery_price = 2.90;
}

$final_total = $total_price + $delivery_price;
?>

<style>
.cart-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    min-height: 70vh;
}

.cart-header {
    text-align: center;
    margin-bottom: 30px;
}

.cart-header h1 {
    color: #333;
    font-size: 28px;
    margin-bottom: 10px;
}

.cart-header .item-count {
    color: #666;
    font-size: 16px;
}

.cart-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
}

@media (max-width: 992px) {
    .cart-content {
        grid-template-columns: 1fr;
    }
}

.cart-items {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #eee;
}

.cart-item {
    display: flex;
    gap: 20px;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
    position: relative;
}

.cart-item:last-child {
    border-bottom: none;
}

.remove-product {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 24px;
    height: 24px;
    background: #ff4444;
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.remove-product:hover {
    background: #cc0000;
    transform: scale(1.1);
}

.item-image {
    width: 100px;
    height: 140px;
    flex-shrink: 0;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
}

.item-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding-right: 40px;
}

.item-title {
    font-size: 16px;
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
    line-height: 1.3;
}

.item-title a {
    color: inherit;
    text-decoration: none;
}

.item-title a:hover {
    color: #e60000;
}

.item-author {
    color: #666;
    font-size: 14px;
    margin-bottom: auto;
}

.item-controls {
    display: flex;
    align-items: center;
    gap: 30px;
    margin-top: 15px;
}

.quantity-control {
    display: flex;
    align-items: center;
    gap: 10px;
}

.quantity-btn {
    width: 30px;
    height: 30px;
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.quantity-btn:hover {
    background: #e0e0e0;
}

.quantity-btn:active {
    background: #d0d0d0;
}

.quantity-display {
    width: 50px;
    height: 30px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    background: white;
}

.item-subtotal {
    font-size: 18px;
    font-weight: bold;
    color: #333;
    margin-left: auto;
}

.cart-actions {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: space-between;
}

.clear-cart-btn {
    background: #ff4444;
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.clear-cart-btn:hover {
    background: #cc0000;
    transform: translateY(-2px);
}

.cart-summary {
    background: #fff;
    border-radius: 8px;
    padding: 20px;
    border: 1px solid #eee;
    height: fit-content;
}

.cart-summary h2 {
    font-size: 20px;
    color: #333;
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid #e60000;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.summary-row.total {
    border-bottom: none;
    border-top: 2px solid #e60000;
    margin-top: 10px;
    padding-top: 15px;
    font-size: 18px;
    font-weight: bold;
}

.summary-label {
    color: #666;
}

.summary-value {
    font-weight: 600;
    color: #333;
}

.summary-row.total .summary-value {
    color: #e60000;
    font-size: 20px;
}

.free-delivery {
    color: #2E7D32 !important;
    font-weight: bold !important;
    text-transform: uppercase;
}

.checkout-btn {
    width: 100%;
    background: #e60000;
    color: #fff;
    padding: 15px;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    margin-top: 20px;
    transition: background 0.3s;
}

.checkout-btn:hover {
    background: #c40000;
}

/* Празна кошница - ЦЕНТРИРАНА */
.empty-cart-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 60vh;
    width: 100%;
}

.empty-cart {
    text-align: center;
    padding: 50px 40px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    max-width: 500px;
    width: 100%;
    border: 1px solid #eee;
}

.empty-cart-icon {
    font-size: 80px;
    color: #ddd;
    margin-bottom: 25px;
}

.empty-cart h2 {
    font-size: 24px;
    color: #333;
    margin-bottom: 15px;
    font-weight: 600;
}

.empty-cart p {
    font-size: 16px;
    color: #666;
    margin-bottom: 30px;
    line-height: 1.5;
}

.empty-cart-btn {
    background: #e60000;
    color: white;
    border: none;
    padding: 14px 40px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.empty-cart-btn:hover {
    background: #c40000;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(230, 0, 0, 0.3);
    color: white;
    text-decoration: none;
}

/* Добави този CSS стил */
.continue-shopping-link {
    color: #e60000;
    font-weight: bold;
    text-decoration: none;
    transition: text-decoration 0.2s;
}

.continue-shopping-link:hover {
    text-decoration: underline;
}
</style>

<div class="cart-container">
    
    <?php if (empty($cart_items)): ?>
    <!-- Празна кошница - ЦЕНТРИРАНА -->
    <div class="empty-cart-container">
        <div class="empty-cart">
            <div class="empty-cart-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h2>Вашата кошница е празна</h2>
            <p>За да добавите продукти, разгледайте нашата колекция от книги</p>
            <a href="index.php" class="empty-cart-btn">
                <i class="fas fa-arrow-left"></i> Върнете се към магазина
            </a>
        </div>
    </div>
    
    <?php else: ?>
    
    <div class="cart-header">
        <h1>Кошница</h1>

    </div>
    
    <div class="cart-content">
        <!-- Продукти -->
        <div class="cart-items">
            <?php foreach ($cart_items as $item): ?>
            <div class="cart-item" data-id="<?= $item['id'] ?>" data-price="<?= $item['price'] ?>">
                <button class="remove-product" onclick="removeProduct(<?= $item['id'] ?>, this)">
                    ✕
                </button>
                
                <div class="item-image">
                    <img src="<?= htmlspecialchars($item['image']) ?>" 
                         alt="<?= htmlspecialchars($item['title']) ?>"
                         onerror="this.src='https://via.placeholder.com/100x140/DDD/333?text=Книга'">
                </div>
                
                <div class="item-info">
                    <div class="item-title">
                        <a href="book.php?id=<?= $item['id'] ?>">
                            <?= htmlspecialchars(mb_substr($item['title'], 0, 60, 'UTF-8')) . (mb_strlen($item['title'], 'UTF-8') > 60 ? '...' : '') ?>
                        </a>
                    </div>
                    <div class="item-author">
                        <?= htmlspecialchars($item['author']) ?>
                    </div>
                    
                    <div class="item-controls">
                        <div class="quantity-control">
                            <button class="quantity-btn minus" onclick="updateQuantity(<?= $item['id'] ?>, -1)">-</button>
                            <div class="quantity-display" id="quantity-<?= $item['id'] ?>">
                                <?= $item['quantity'] ?>
                            </div>
                            <button class="quantity-btn plus" onclick="updateQuantity(<?= $item['id'] ?>, 1)">+</button>
                        </div>
                        
                        <div class="item-subtotal" id="subtotal-<?= $item['id'] ?>">
                            <?= number_format($item['subtotal'], 2) ?> лв.
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <div class="cart-actions">
                <button class="clear-cart-btn" onclick="clearCart()">
                    <i class="fas fa-trash-alt"></i> Изчисти цялата кошница
                </button>
                <a href="index.php" class="continue-shopping-link">
    <i class="fas fa-arrow-left"></i> Продължи пазаруването
</a>
            </div>
        </div>
        
        <!-- Обобщение -->
        <div class="cart-summary">
            <h2>Обобщение на поръчката</h2>
            
            <div class="summary-row">
                <span class="summary-label">Продукти</span>
                <span class="summary-value" id="summary-products">
                    <?= number_format($total_price, 2) ?> лв.
                </span>
            </div>
            
            <div class="summary-row">
                <span class="summary-label">Доставка</span>
                <span class="summary-value <?= $delivery_price == 0 ? 'free-delivery' : '' ?>" id="summary-delivery">
                    <?php if ($delivery_price == 0): ?>
                        БЕЗПЛАТНА
                    <?php else: ?>
                        <?= number_format($delivery_price, 2) ?> лв.
                    <?php endif; ?>
                </span>
            </div>
            
            <div class="summary-row total">
                <span class="summary-label">Общо</span>
                <span class="summary-value" id="summary-total">
                    <?= number_format($final_total, 2) ?> лв.
                </span>
            </div>
            
           <button class="checkout-btn" onclick="goToCheckout()">
    Продължи към плащане
</button>
        </div>
    </div>
    
    <?php endif; ?>
</div>

<script>

    
// Функция за обновяване на количеството
function updateQuantity(productId, change) {
    // Намери текущото количество
    const quantityElement = document.getElementById(`quantity-${productId}`);
    let currentQuantity = parseInt(quantityElement.textContent);
    
    // Пресметни новото количество
    let newQuantity = currentQuantity + change;
    
    // Ограничи между 1 и 10
    if (newQuantity < 1) newQuantity = 1;
    if (newQuantity > 10) newQuantity = 10;
    
    // Ако количеството не се е променило, не прави нищо
    if (newQuantity === currentQuantity) return;
    
    // Покажи новото количество
    quantityElement.textContent = newQuantity;
    
    // Изпрати AJAX заявка към сървъра
    fetch('update_quantity.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `id=${productId}&quantity=${newQuantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Обнови цената на продукта
            updateProductPrice(productId, newQuantity);
            
            // Обнови цялата кошница
            updateCartSummary();
            
            // Обнови брояча в header-а
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
        } else {
            // Върни старото количество при грешка
            quantityElement.textContent = currentQuantity;
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        // Върни старото количество при грешка
        quantityElement.textContent = currentQuantity;
    });
}

// Функция за обновяване на цената на конкретен продукт
function updateProductPrice(productId, quantity) {
    // Намери цената от data attribute
    const cartItem = document.querySelector(`.cart-item[data-id="${productId}"]`);
    const price = parseFloat(cartItem.dataset.price);
    
    // Пресметни новата обща цена за този продукт
    const newSubtotal = price * quantity;
    
    // Обнови показваната цена
    const subtotalElement = document.getElementById(`subtotal-${productId}`);
    if (subtotalElement) {
        subtotalElement.textContent = newSubtotal.toFixed(2).replace('.', ',') + ' лв.';
    }
}

// Функция за обновяване на обобщението
function updateCartSummary() {
    // Събери цените на всички продукти
    let productsTotal = 0;
    document.querySelectorAll('.cart-item').forEach(item => {
        const productId = item.dataset.id;
        const quantity = parseInt(document.getElementById(`quantity-${productId}`).textContent);
        const price = parseFloat(item.dataset.price);
        productsTotal += price * quantity;
    });
    
    // Обнови общата цена на продуктите
    const productsElement = document.getElementById('summary-products');
    if (productsElement) {
        productsElement.textContent = productsTotal.toFixed(2).replace('.', ',') + ' лв.';
    }
    
    // Изчисли новата цена на доставката
    let deliveryPrice = 0;
    if (productsTotal > 0 && productsTotal < 20) {
        deliveryPrice = 4.90;
    } else if (productsTotal >= 100) {
        deliveryPrice = 0;
    } else {
        deliveryPrice = 2.90;
    }
    
    // Обнови доставката
    const deliveryElement = document.getElementById('summary-delivery');
    if (deliveryElement) {
        if (deliveryPrice == 0) {
            deliveryElement.textContent = 'БЕЗПЛАТНА';
            deliveryElement.className = 'summary-value free-delivery';
        } else {
            deliveryElement.textContent = deliveryPrice.toFixed(2).replace('.', ',') + ' лв.';
            deliveryElement.className = 'summary-value';
        }
    }
    
    // Изчисли и обнови общата сума
    const finalTotal = productsTotal + deliveryPrice;
    const totalElement = document.getElementById('summary-total');
    if (totalElement) {
        totalElement.textContent = finalTotal.toFixed(2).replace('.', ',') + ' лв.';
    }
}

// Функция за премахване на продукт (БЕЗ СЪОБЩЕНИЕ)
function removeProduct(productId, button) {
    if (!confirm('Сигурни ли сте, че искате да премахнете този продукт от кошницата?')) {
        return;
    }
    
    // Анимация за изтриване
    const cartItem = button.closest('.cart-item');
    cartItem.style.transform = 'translateX(-100%)';
    cartItem.style.opacity = '0';
    cartItem.style.transition = 'all 0.3s ease';
    
    // Изпрати заявка към сървъра
    fetch('remove_from_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `id=${productId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Изтрий елемента от DOM след анимацията
            setTimeout(() => {
                cartItem.remove();
                
                // Обнови обобщението
                updateCartSummary();
                
                // Обнови брояча в header-а
                if (typeof updateCartCount === 'function') {
                    updateCartCount();
                }
                
                // Обнови броя на продуктите в заглавието
                updateItemCount();
                
                // Проверка дали кошницата е празна
                checkIfCartIsEmpty();
            }, 300);
        } else {
            // Върни анимацията при грешка
            cartItem.style.transform = '';
            cartItem.style.opacity = '1';
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        // Върни анимацията при грешка
        cartItem.style.transform = '';
        cartItem.style.opacity = '1';
    });
}

// Функция за изчистване на цялата кошница (БЕЗ СЪОБЩЕНИЕ)
function clearCart() {
    if (!confirm('Сигурни ли сте, че искате да изпразните цялата кошница?')) {
        return;
    }
    
    // Анимация за изтриване на всички продукти
    const cartItems = document.querySelectorAll('.cart-item');
    cartItems.forEach((item, index) => {
        setTimeout(() => {
            item.style.transform = 'translateX(-100%)';
            item.style.opacity = '0';
            item.style.transition = 'all 0.3s ease';
        }, index * 100);
    });
    
    // Изпрати заявка към сървъра
    fetch('clear_cart.php', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            setTimeout(() => {
                // Премахни всички продукти от DOM
                cartItems.forEach(item => item.remove());
                
                // Обнови брояча в header-а
                if (typeof updateCartCount === 'function') {
                    updateCartCount();
                }
                
                // Обнови броя на продуктите в заглавието
                updateItemCount();
                
                // Покажи екран за празна кошница
                showEmptyCartScreen();
            }, 500);
        } else {
            // Върни анимацията при грешка
            cartItems.forEach(item => {
                item.style.transform = '';
                item.style.opacity = '1';
            });
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        // Върни анимацията при грешка
        cartItems.forEach(item => {
            item.style.transform = '';
            item.style.opacity = '1';
        });
    });
}

// Функция за обновяване на броя на продуктите в заглавието
function updateItemCount() {
    const cartItems = document.querySelectorAll('.cart-item');
    const itemCountElement = document.querySelector('.item-count');
    const count = cartItems.length;
    
    if (itemCountElement) {
        itemCountElement.textContent = `${count} продукт${count !== 1 ? 'а' : ''}`;
    }
}

// Функция за проверка дали кошницата е празна
function checkIfCartIsEmpty() {
    const cartItems = document.querySelectorAll('.cart-item');
    if (cartItems.length === 0) {
        showEmptyCartScreen();
    }
}

// Функция за показване на екран за празна кошница (ЦЕНТРИРАН)
function showEmptyCartScreen() {
    const cartContainer = document.querySelector('.cart-container');
    if (cartContainer) {
        cartContainer.innerHTML = `
            <div class="empty-cart-container">
                <div class="empty-cart">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h2>Вашата кошница е празна</h2>
                    <p>За да добавите продукти, разгледайте нашата колекция от книги</p>
                    <a href="index.php" class="empty-cart-btn">
                        <i class="fas fa-arrow-left"></i> Върнете се към магазина
                    </a>
                </div>
            </div>
        `;
    }
}

// Когато страницата се зареди
document.addEventListener('DOMContentLoaded', function() {
    console.log('Кошницата е заредена успешно');
});

// Функция за преход към плащане
function goToCheckout() {
    window.location.href = 'checkout.php';
}
</script>

<?php 
$conn->close();
include 'footer.php';
?>