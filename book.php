<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Връзка с базата
$conn = new mysqli('localhost', 'root', '', 'bookstore');
if ($conn->connect_error) {
    die('Грешка при връзка с базата данни: ' . $conn->connect_error);
}
$conn->set_charset("utf8");

// Вземане на ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $page_title = 'Грешка';
    include 'header.php';
    echo '<div style="text-align: center; padding: 50px;">Невалиден ID на книга.</div>';
    include 'footer.php';
    exit;
}

// Вземаме само реалните колони
$stmt = $conn->prepare("
    SELECT id, title, author, price, image, sales, description, is_new
    FROM books WHERE id = ?
");
if (!$stmt) {
    die('Грешка в SQL заявката: ' . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();

// Проверка дали книгата е вече в любими (ако потребителят е влязъл)
$is_in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $check_stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id = ? AND book_id = ?");
    $check_stmt->bind_param("ii", $_SESSION['user_id'], $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $is_in_wishlist = $check_result->num_rows > 0;
    $check_stmt->close();
}

$stmt->close();
$conn->close();

if (!$book) {
    $page_title = 'Книгата не е намерена';
    include 'header.php';
    echo '<div style="text-align: center; padding: 50px;">Книгата не е намерена.</div>';
    include 'footer.php';
    exit;
}

$page_title = htmlspecialchars($book['title']);
include 'header.php';

// Всички книги се считат за налични (няма stock колона)
$is_available = true;
?>
<style>
/* Стилове за линка на автора в upcoming_books.php */
.book-author a {
    color: #666;
    text-decoration: none;
    font-style: italic;
    transition: all 0.3s ease;
    display: inline-block;
    margin-left: 5px;
}

.book-author a:hover {
    color: #e60000;
    text-decoration: underline;
}

/* Стилове за бутоните в book.php */
.add-to-cart-btn-new {
    background: #e60000;
    color: white;
    border: none;
    padding: 15px 30px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    width: 100%;
    margin-top: 15px;
    display: block;
    text-align: center;
}

.add-to-cart-btn-new:hover {
    background: #c40000;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(230, 0, 0, 0.3);
}

.add-to-cart-btn-new:active {
    transform: translateY(0);
}

/* Стилове за бутона за любими */
.add-to-wishlist-btn-book {
    background: transparent;
    border: 2px solid #e60000;
    color: #e60000;
    padding: 14px 30px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    width: 100%;
    margin-top: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.add-to-wishlist-btn-book:hover {
    background: #e60000;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(230, 0, 0, 0.3);
}

.add-to-wishlist-btn-book.added {
    background: #e60000;
    color: white;
    border-color: #e60000;
}

.add-to-wishlist-btn-book i {
    font-size: 18px;
}

/* Стилове за група от бутони */
.book-actions-group {
    margin-top: 20px;
}

.book-actions-row {
    display: flex;
    gap: 10px;
    margin-bottom: 10px;
}

.book-actions-row .add-to-cart-btn-new {
    flex: 1;
    margin-top: 0;
}

.book-actions-row .add-to-wishlist-btn-book {
    flex: 1;
    margin-top: 0;
}

/* Адаптивност */
@media (max-width: 768px) {
    .book-actions-row {
        flex-direction: column;
    }
    
    .add-to-cart-btn-new,
    .add-to-wishlist-btn-book {
        width: 100%;
    }
}
</style>

<link rel="stylesheet" href="assets/css/book.css">

<div class="book-details-page">
    <div class="book-details-container">
        <div class="book-image-column">
            <?php if (!empty($book['image'])): ?>
                <img src="<?= htmlspecialchars($book['image']) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>">
            <?php else: ?>
                <div class="no-image">Няма снимка</div>
            <?php endif; ?>
        </div>
        
        <div class="book-info-column">
            <h1><?= htmlspecialchars($book['title']) ?></h1>
            <p class="book-author">
                <strong>Автор:</strong> 
                <a href="author_books.php?author=<?= urlencode($book['author']) ?>" 
                   class="author-link" >
                    <?= htmlspecialchars($book['author']) ?>
                </a>
            </p>
            
            <div class="book-price-section">
                <div class="price-container">
                    <span class="book-price"><?= number_format($book['price'], 2) ?> лв.</span>
                </div>
                
                <?php if (!empty($book['sales'])): ?>
                <div class="sales-info"><strong>Продадени:</strong> <?= $book['sales'] ?> броя</div>
                <?php endif; ?>
                
                <div class="availability"><strong>Наличност:</strong> <?= $is_available ? 'В наличност' : 'Изчерпана' ?></div>
                
                <?php if ($is_available): ?>
                <div class="book-actions-group">
                    <div class="book-actions-row">
                        <button class="add-to-cart-btn-new" 
                                onclick="addToCartNew(<?= $book['id'] ?>, this)">
                            Добави в кошницата
                        </button>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <button class="add-to-wishlist-btn-book <?= $is_in_wishlist ? 'added' : '' ?>" 
                                id="wishlist-btn-<?= $book['id'] ?>"
                                onclick="addToWishlistBook(<?= $book['id'] ?>, this)">
                            <i class="<?= $is_in_wishlist ? 'fas fa-heart' : 'far fa-heart' ?>"></i>
                            <?= $is_in_wishlist ? 'В любими' : 'Добави в любими' ?>
                        </button>
                        <?php else: ?>
                        <button class="add-to-wishlist-btn-book" 
                                onclick="showLoginMessage()">
                            <i class="far fa-heart"></i>
                            Добави в любими
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php else: ?>
                <button class="out-of-stock-btn" disabled>Няма наличност</button>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($book['is_new'])): ?>
            <div class="new-book-badge">НОВА КНИГА</div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (!empty($book['description'])): ?>
    <div class="book-description">
        <h2>Описание</h2>
        <div class="description-content"><?= nl2br(htmlspecialchars($book['description'])) ?></div>
    </div>
    <?php endif; ?>
</div>
<script>
// Функция за добавяне в кошницата (за book.php)
function addToCartNew(productId, button) {
    if (!button) return;
    
    const originalText = button.innerHTML;
    const originalColor = button.style.backgroundColor;
    
    button.innerHTML = 'Добавя се...';
    button.disabled = true;
    button.style.backgroundColor = '#999';
    
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        button.innerHTML = originalText;
        button.disabled = false;
        button.style.backgroundColor = originalColor;
        
        if (data.success) {
            // Обнови брояча в header-а
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
            
            // Покажи известие
            showCartNotification('✓ Книгата е добавена в кошницата!', 'success');
        } else {
            showCartNotification('✗ ' + (data.message || 'Грешка при добавяне'), 'error');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        button.innerHTML = originalText;
        button.disabled = false;
        button.style.backgroundColor = originalColor;
        showCartNotification('✗ Възникна грешка', 'error');
    });
}

// Функция за добавяне/премахване от любими в book.php
function addToWishlistBook(bookId, button) {
    if (!button) return;
    
    const isAdded = button.classList.contains('added');
    const originalHTML = button.innerHTML;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Обработка...';
    button.disabled = true;
    
    const action = isAdded ? 'remove' : 'add';
    
    fetch('add_to_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'book_id=' + bookId + '&action=' + action
    })
    .then(response => response.json())
    .then(data => {
        button.disabled = false;
        
        if (data.success) {
            if (action === 'add') {
                button.classList.add('added');
                button.innerHTML = '<i class="fas fa-heart"></i> В любими';
                showCartNotification('✓ Книгата е добавена в любими!', 'success');
                
                // Обновяване на брояча за любими (ако има такъв в header-а)
                const wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    let current = parseInt(wishlistCount.textContent) || 0;
                    wishlistCount.textContent = current + 1;
                }
            } else {
                button.classList.remove('added');
                button.innerHTML = '<i class="far fa-heart"></i> Добави в любими';
                showCartNotification('✓ Книгата е премахната от любими', 'success');
                
                // Обновяване на брояча за любими
                const wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    let current = parseInt(wishlistCount.textContent) || 0;
                    if (current > 0) {
                        wishlistCount.textContent = current - 1;
                    }
                }
            }
        } else {
            button.innerHTML = originalHTML;
            showCartNotification('✗ ' + (data.message || 'Грешка'), 'error');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        button.disabled = false;
        button.innerHTML = originalHTML;
        showCartNotification('✗ Възникна грешка при обработката', 'error');
    });
}

// Функция за показване на съобщение за вход
function showLoginMessage() {
    showCartNotification('Моля, влезте в профила си за да добавите в любими!', 'info');
}

// Функция за показване на известия
function showCartNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = 'cart-notification';
    notification.innerHTML = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3'};
        color: white;
        border-radius: 6px;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideInRight 0.3s ease;
        font-family: Arial, sans-serif;
        max-width: 300px;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Добавяне на CSS анимации
if (!document.querySelector('#notification-animations')) {
    const style = document.createElement('style');
    style.id = 'notification-animations';
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
}
</script>
<?php include 'footer.php'; ?>