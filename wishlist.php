<?php
include 'config.php';


// Проверка дали потребителят е влязъл
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=wishlist.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$page_title = 'Любими книги';
include 'header.php';

// Функция за вземане на любими книги
function getWishlistBooks($conn, $user_id) {
    $sql = "SELECT b.* FROM books b
            JOIN wishlist w ON b.id = w.book_id
            WHERE w.user_id = ?
            ORDER BY w.added_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $books = [];
    
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    
    $stmt->close();
    return $books;
}

// Вземане на любими книги
$wishlist_books = getWishlistBooks($conn, $user_id);
$total_books = count($wishlist_books);
?>

<style>
/* СТИЛОВЕ ЗА ЛЮБИМИ КНИГИ */
.wishlist-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.wishlist-header {
    text-align: center;
    margin-bottom: 40px;
}

.wishlist-header h1 {
    color: #333;
    font-size: 36px;
    margin-bottom: 15px;
    position: relative;
    display: inline-block;
}

.wishlist-header h1:after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: #e60000;
    border-radius: 2px;
}

.wishlist-stats {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.stat-card {
    background: white;
    padding: 20px 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    text-align: center;
    min-width: 150px;
    border: 1px solid #f0f0f0;
}

.stat-value {
    font-size: 32px;
    font-weight: bold;
    color: #e60000;
    margin-bottom: 5px;
}

.stat-label {
    color: #666;
    font-size: 14px;
}

/* Съобщения за празна списък */
.empty-wishlist {
    text-align: center;
    padding: 80px 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    border-radius: 15px;
    margin: 30px 0;
    box-shadow: 0 8px 25px rgba(0,0,0,0.05);
}

.empty-wishlist-icon {
    font-size: 80px;
    color: #e60000;
    margin-bottom: 20px;
    opacity: 0.7;
}

.empty-wishlist h2 {
    color: #333;
    margin-bottom: 15px;
    font-size: 28px;
}

.empty-wishlist p {
    color: #666;
    font-size: 18px;
    max-width: 600px;
    margin: 0 auto 30px;
    line-height: 1.6;
}

.browse-books-btn {
    background: #e60000;
    color: white;
    padding: 14px 35px;
    text-decoration: none;
    border-radius: 10px;
    font-weight: bold;
    font-size: 16px;
    display: inline-block;
    transition: all 0.3s ease;
    border: 2px solid #e60000;
    box-shadow: 0 4px 12px rgba(230, 0, 0, 0.2);
}

.browse-books-btn:hover {
    background: white;
    color: #e60000;
    text-decoration: none;
    transform: translateY(-3px);
    box-shadow: 0 6px 18px rgba(230, 0, 0, 0.3);
}

/* Грид за книгите в любими */
.wishlist-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 25px;
    margin-bottom: 50px;
}

@media (max-width: 1200px) {
    .wishlist-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
}

@media (max-width: 992px) {
    .wishlist-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .wishlist-grid {
        grid-template-columns: 1fr;
        max-width: 350px;
        margin: 0 auto 30px;
    }
}

/* Картичка за книга в любими */
.wishlist-book-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    border: 1px solid #e0e0e0;
    position: relative;
    height: 450px;
    display: flex;
    flex-direction: column;
}

.wishlist-book-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    border-color: #e60000;
}

/* Значка за добавена дата */
.added-date {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(230, 0, 0, 0.9);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    z-index: 1;
}

/* Бутон за премахване от любими */
.remove-from-wishlist-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(0,0,0,0.7);
    color: white;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    transition: all 0.3s ease;
    z-index: 1;
}

.remove-from-wishlist-btn:hover {
    background: #e60000;
    transform: scale(1.1);
}

/* Снимка на книгата */
.wishlist-book-image {
    text-align: center;
    margin: 30px 0 15px;
    flex-shrink: 0;
    height: 180px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.wishlist-book-image img {
    max-height: 180px;
    max-width: 140px;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* Информация за книгата */
.wishlist-book-info {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.wishlist-book-info h3 {
    margin: 0 0 10px 0;
    font-size: 16px;
    font-weight: 600;
    line-height: 1.3;
    min-height: 45px;
}

.wishlist-book-info h3 a {
    color: #333;
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.wishlist-book-info h3 a:hover {
    color: #e60000;
    text-decoration: underline;
}

.wishlist-book-author {
    color: #666;
    margin: 0 0 15px 0;
    font-size: 14px;
    font-style: italic;
    min-height: 20px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.wishlist-price-sales {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.wishlist-price {
    color: #e60000;
    font-weight: bold;
    font-size: 18px;
}

.wishlist-sales {
    color: #888;
    font-size: 13px;
    background: #f5f5f5;
    padding: 4px 10px;
    border-radius: 12px;
}

/* Действия с книгата */
.wishlist-book-actions {
    display: flex;
    gap: 10px;
    margin-top: auto;
}

.wishlist-add-to-cart-btn {
    flex: 1;
    background: #e60000;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.wishlist-add-to-cart-btn:hover {
    background: #c40000;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(230, 0, 0, 0.2);
}

/* Опции за споделяне */
.wishlist-options {
    margin-top: 40px;
    background: #f8f9fa;
    padding: 25px;
    border-radius: 12px;
    text-align: center;
}

.wishlist-options h3 {
    color: #333;
    margin-bottom: 20px;
    font-size: 20px;
}

.wishlist-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}

.wishlist-action-btn {
    padding: 10px 20px;
    background: white;
    color: #333;
    border: 2px solid #ddd;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.wishlist-action-btn:hover {
    background: #e60000;
    color: white;
    border-color: #e60000;
    transform: translateY(-2px);
}

/* Групиране на действията */
.wishlist-book-actions {
    display: flex;
    gap: 10px;
}

.wishlist-add-to-cart-btn {
    flex: 1;
    background: #e60000;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
}

.wishlist-add-to-cart-btn:hover {
    background: #c40000;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(230, 0, 0, 0.2);
}
</style>

<div class="wishlist-page">
    <div class="wishlist-header">
        <h1><i class="fas fa-heart"></i> Любими книги</h1>
        <p>Това са книгите, които сте запазили за по-късно</p>
    </div>
    
    <div class="wishlist-stats">
        <div class="stat-card">
            <div class="stat-value"><?= $total_books ?></div>
            <div class="stat-label">Общо книги</div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?php 
                // Изчисляване на общата цена
                $total_price = 0;
                foreach ($wishlist_books as $book) {
                    $total_price += $book['price'];
                }
                echo number_format($total_price, 2);
            ?></div>
            <div class="stat-label">Обща стойност</div>
        </div>
    </div>
    
    <?php if ($total_books > 0): ?>
    
    <div class="wishlist-grid">
        <?php 
        // Вземане на дати на добавяне за всяка книга
        $added_dates_sql = "SELECT book_id, added_at FROM wishlist WHERE user_id = ? ORDER BY added_at DESC";
        $dates_stmt = $conn->prepare($added_dates_sql);
        $dates_stmt->bind_param("i", $user_id);
        $dates_stmt->execute();
        $dates_result = $dates_stmt->get_result();
        $added_dates = [];
        while ($date_row = $dates_result->fetch_assoc()) {
            $added_dates[$date_row['book_id']] = $date_row['added_at'];
        }
        $dates_stmt->close();
        
        foreach ($wishlist_books as $book): 
            $added_date = isset($added_dates[$book['id']]) ? $added_dates[$book['id']] : '';
            $formatted_date = $added_date ? date('d.m.Y', strtotime($added_date)) : '';
        ?>
        <div class="wishlist-book-card" id="wishlist-book-<?= $book['id'] ?>">
            <?php if ($formatted_date): ?>
            <div class="added-date" <?= $formatted_date ?>>
                <i class="far fa-calendar-alt"></i> <?= $formatted_date ?>
            </div>
            <?php endif; ?>
            
            <button class="remove-from-wishlist-btn" 
                    onclick="removeFromWishlist(<?= $book['id'] ?>, this)"
                >
                <i class="fas fa-times"></i>
            </button>
            
            <div class="wishlist-book-image">
                <img src="<?= htmlspecialchars($book['image']) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>"
                     onerror="this.src='https://via.placeholder.com/140x180/DDD/333?text=Няма+снимка'">
            </div>
            
            <div class="wishlist-book-info">
                <h3>
                    <a href="book.php?id=<?= $book['id'] ?>">
                        <?= htmlspecialchars(mb_substr($book['title'], 0, 60, 'UTF-8')) . 
                           (mb_strlen($book['title'], 'UTF-8') > 60 ? '...' : '') ?>
                    </a>
                </h3>
                
                <p class="wishlist-book-author">
                    <a href="author_books.php?author=<?= urlencode($book['author']) ?>">
                        <?= htmlspecialchars($book['author']) ?>
                    </a>
                </p>
                
                <div class="wishlist-price-sales">
                    <span class="wishlist-price"><?= number_format($book['price'], 2) ?> лв.</span>
                    <span class="wishlist-sales"><?= $book['sales'] ?> продажби</span>
                </div>
                
                <div class="wishlist-book-actions">
                    <button class="wishlist-add-to-cart-btn" 
                            onclick="addToCartNew(<?= $book['id'] ?>, this)">
                        <i class="fas fa-shopping-cart"></i> В кошницата
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="wishlist-options">
        <h3>Допълнителни опции</h3>
        <div class="wishlist-actions">
            <button class="wishlist-action-btn" onclick="printWishlist()">
                <i class="fas fa-print"></i> Разпечатай списък
            </button>
            <button class="wishlist-action-btn" onclick="shareWishlist()">
                <i class="fas fa-share-alt"></i> Сподели
            </button>
            <button class="wishlist-action-btn" onclick="clearWishlist()">
                <i class="fas fa-trash-alt"></i> Изчисти всички
            </button>
        </div>
    </div>
    
    <?php else: ?>
    
    <div class="empty-wishlist">
        <div class="empty-wishlist-icon">
            <i class="far fa-heart"></i>
        </div>
        <h2>Вашият списък с любими книги е празен</h2>
        <p>Добавете книги, които харесвате, за да ги запазите за по-късно. Кликнете върху сърцето на всяка книга, която ви харесва!</p>
        <a href="books.php" class="browse-books-btn">
            <i class="fas fa-book"></i> Разгледайте книгите
        </a>
    </div>
    
    <?php endif; ?>
</div>

<script>
// Функция за премахване от любими
function removeFromWishlist(bookId, button) {
    if (!button) return;
    
    const originalIcon = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    fetch('add_to_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'book_id=' + bookId + '&action=remove'
    })
    .then(response => response.json().catch(() => ({})))
    .then(data => {
        button.disabled = false;
        
        if (data.success) {
            // Премахване на картата от екрана
            const card = document.getElementById('wishlist-book-' + bookId);
            if (card) {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.8)';
                
                setTimeout(() => {
                    card.remove();
                    updateWishlistStats();
                    
                    // Ако няма повече книги, показваме празен списък
                    if (document.querySelectorAll('.wishlist-book-card').length === 0) {
                        showEmptyWishlist();
                    }
                }, 300);
            }
            
            showSimpleMessage('✓ Книгата е премахната от любими', 'success');
            
            // Обновяване на брояча в хедъра
            const wishlistCount = document.getElementById('wishlist-count');
            if (wishlistCount) {
                let current = parseInt(wishlistCount.textContent) || 0;
                if (current > 0) {
                    wishlistCount.textContent = current - 1;
                }
            }
        } else {
            button.innerHTML = originalIcon;
            showSimpleMessage('✗ ' + (data.message || 'Грешка при премахване'), 'error');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        button.disabled = false;
        button.innerHTML = originalIcon;
        showSimpleMessage('✗ Възникна грешка', 'error');
    });
}

// Обновяване на статистиката
function updateWishlistStats() {
    const books = document.querySelectorAll('.wishlist-book-card');
    const totalBooks = books.length;
    
    // Обновяване на броя книги
    const totalBooksElement = document.querySelector('.stat-value:first-child');
    if (totalBooksElement) {
        totalBooksElement.textContent = totalBooks;
    }
    
    // Преизчисляване на общата цена
    let totalPrice = 0;
    books.forEach(card => {
        const priceElement = card.querySelector('.wishlist-price');
        if (priceElement) {
            const priceText = priceElement.textContent.replace(' лв.', '').trim();
            const price = parseFloat(priceText.replace(',', '.'));
            if (!isNaN(price)) {
                totalPrice += price;
            }
        }
    });
    
    const totalPriceElement = document.querySelector('.stat-value:last-child');
    if (totalPriceElement) {
        totalPriceElement.textContent = totalPrice.toFixed(2);
    }
}

// Показване на празен списък
function showEmptyWishlist() {
    const wishlistGrid = document.querySelector('.wishlist-grid');
    const wishlistOptions = document.querySelector('.wishlist-options');
    
    if (wishlistGrid) wishlistGrid.remove();
    if (wishlistOptions) wishlistOptions.remove();
    
    const emptyHTML = `
        <div class="empty-wishlist">
            <div class="empty-wishlist-icon">
                <i class="far fa-heart"></i>
            </div>
            <h2>Вашият списък с любими книги е празен</h2>
            <p>Добавете книги, които харесвате, за да ги запазите за по-късно. Кликнете върху сърцето на всяка книга, която ви харесва!</p>
            <a href="books.php" class="browse-books-btn">
                <i class="fas fa-book"></i> Разгледайте книгите
            </a>
        </div>
    `;
    
    const stats = document.querySelector('.wishlist-stats');
    if (stats) {
        stats.insertAdjacentHTML('afterend', emptyHTML);
    }
}

// Допълнителни функции
function printWishlist() {
    window.print();
}

function shareWishlist() {
    const bookTitles = Array.from(document.querySelectorAll('.wishlist-book-info h3 a'))
        .map(title => title.textContent.trim())
        .join('\n');
    
    const shareText = `Моите любими книги:\n\n${bookTitles}\n\nВиж ги в BookStore!`;
    
    if (navigator.share) {
        navigator.share({
            title: 'Моите любими книги',
            text: shareText,
            url: window.location.href
        });
    } else {
        // Копиране в клипборда
        navigator.clipboard.writeText(shareText).then(() => {
            showSimpleMessage('Списъкът е копиран в клипборда!', 'success');
        });
    }
}

function clearWishlist() {
    if (confirm('Наистина ли искате да изтриете всички книги от любими?')) {
        fetch('clear_wishlist.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showEmptyWishlist();
                showSimpleMessage('✓ Всички книги са премахнати от любими', 'success');
                
                // Нулиране на брояча
                const wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    wishlistCount.textContent = '0';
                }
            }
        })
        .catch(error => {
            console.error('Грешка:', error);
            showSimpleMessage('✗ Възникна грешка', 'error');
        });
    }
}

// Използваме същата функция за кошницата от index.php
function addToCartNew(productId, button) {
    if (!button) return;
    
    const originalText = button.innerHTML;
    const originalColor = button.style.backgroundColor;
    
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    button.style.backgroundColor = '#999';
    
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'id=' + productId
    })
    .then(response => response.json().catch(() => ({})))
    .then(data => {
        button.innerHTML = originalText;
        button.disabled = false;
        button.style.backgroundColor = originalColor;
        
        if (data.success) {
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                let current = parseInt(cartCount.textContent) || 0;
                cartCount.textContent = current + 1;
                
                cartCount.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    cartCount.style.transform = 'scale(1)';
                }, 300);
            }
            
            showSimpleMessage('✓ Книгата е добавена в кошницата!', 'success');
        } else {
            showSimpleMessage('✗ ' + (data.message || 'Грешка при добавяне'), 'error');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        button.innerHTML = originalText;
        button.disabled = false;
        button.style.backgroundColor = originalColor;
        showSimpleMessage('✗ Възникна грешка', 'error');
    });
}
</script>

<?php 
include 'footer.php';
?>