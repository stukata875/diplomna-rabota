<?php
include 'config.php';

$page_title = 'Детска литература';
include 'header.php';

// Проверка дали има колона is_children
$check_sql = "SHOW COLUMNS FROM books LIKE 'is_children'";
$check_result = $conn->query($check_sql);
$has_children = $check_result && $check_result->num_rows > 0;

// Ако няма колона is_children, създайте я
if (!$has_children) {
    $conn->query("ALTER TABLE books ADD COLUMN is_children TINYINT(1) DEFAULT 0");
    $conn->query("CREATE INDEX idx_children ON books(is_children)");
}

// Брой книги на страница
$books_per_page = 50;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $books_per_page;

// Вземане на общия брой книги
$total_sql = "SELECT COUNT(*) as total FROM books WHERE is_children = 1";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_books = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_books / $books_per_page);
$total_stmt->close();

if ($total_pages > 0 && $current_page > $total_pages) {
    $current_page = $total_pages;
} elseif ($total_pages == 0) {
    $current_page = 1;
}
?>

<style>
/* Стилове за Детска литература */
.page-content-children {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-content-children h1 {
    color: #333;
    padding-bottom: 15px;
    margin-bottom: 30px;
    font-size: 32px;
    font-weight: 600;
    border-bottom: 3px solid #FF6B6B;
    text-align: center;
}

/* Инфо бар */
.page-info-children {
    text-align: center;
    margin-bottom: 25px;
    color: #666;
    font-size: 15px;
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

/* Мрежа от книги - 5 колони */
.books-grid-children {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 40px;
    justify-items: center;
}

/* Картичка за книга */
.book-card-children {
    width: 100%;
    max-width: 240px;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    padding: 15px;
    background: white;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    height: 380px;
}

.book-card-children:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: #FF6B6B;
}

/* Бадж за детска литература */
.children-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #FF6B6B;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    z-index: 2;
}

/* Снимка */
.book-image-children {
    text-align: center;
    margin: 15px 0 15px 0;
    flex-shrink: 0;
    height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.book-image-children img {
    max-height: 140px;
    max-width: 100px;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* Заглавие */
.book-title-children {
     color: #333;
    text-decoration: none;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.3s ease;
}

.book-title-link-children {
    color: #333;
    text-decoration: none;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.3s ease;
}

.book-title-link-children:hover {
    color: #FF6B6B;
    text-decoration: underline;
}

/* Автор */
.book-author-children {
      color: #666;
    margin: 0 0 3px 0;
    font-size: 13px;
    font-style: italic;
    height: 18px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.author-link-children {
    color: #666;
    text-decoration: none;
    font-style: italic;
    transition: color 0.3s ease;
}

.author-link-children:hover {
    color: #FF6B6B;
}

/* Цена и продажби */
.price-sales-children {
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.book-price-children {
    color: #e60000;
    font-weight: bold;
    font-size: 17px;
}

.book-sales-children {
    color: #666;
    font-size: 12px;
    background: #f5f5f5;
    padding: 4px 10px;
    border-radius: 12px;
    display: inline-block;
}

/* Бутон */
.add-to-cart-btn-children {
     background: #e60000;
    color: white;
    border: none;
    padding: 12px 0;
    border-radius: 6px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    text-align: center;
    margin-top: auto;
}

.add-to-cart-btn-children:hover {
    background: #c40000;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(230, 0, 0, 0.2);
}

.add-to-cart-btn-children:disabled {
    background: #cccccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Пагинация */
.pagination-section-children {
    margin-top: 40px;
    text-align: center;
}

.pagination-children {
    display: flex;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.pagination-children a {
    padding: 8px 12px;
    background: #f5f5f5;
    border-radius: 5px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s;
    border: 1px solid #ddd;
    min-width: 36px;
    text-align: center;
    font-weight: 500;
    font-size: 14px;
}

.pagination-children a:hover {
    background: #FF6B6B;
    color: white;
    border-color: #FF6B6B;
}

.pagination-children a.active-page {
    background: #FF6B6B;
    color: white;
    border-color: #FF6B6B;
    font-weight: bold;
}

.page-nav-btn-children {
    display: inline-block;
    margin: 0 10px;
    padding: 8px 20px;
    background: #FF6B6B;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: all 0.3s;
    border: 2px solid #FF6B6B;
}

.page-nav-btn-children:hover {
    background: white;
    color: #FF6B6B;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(255, 107, 107, 0.2);
}

/* Празен списък */
.empty-state-children {
    text-align: center;
    padding: 50px 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 30px 0;
    grid-column: 1 / -1;
}

.empty-state-children p {
    color: #666;
    font-size: 16px;
    margin-bottom: 20px;
}

.empty-state-children a {
    color: #FF6B6B;
    text-decoration: none;
    font-weight: bold;
    border: 2px solid #FF6B6B;
    padding: 10px 20px;
    border-radius: 6px;
    display: inline-block;
    transition: all 0.3s;
}

.empty-state-children a:hover {
    background: #FF6B6B;
    color: white;
}

/* Адаптивен дизайн */
@media (max-width: 1400px) {
    .books-grid-children {
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    
    .book-card-children {
        max-width: 260px;
    }
}

@media (max-width: 1200px) {
    .books-grid-children {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .book-card-children {
        max-width: 280px;
    }
}

@media (max-width: 992px) {
    .books-grid-children {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .book-card-children {
        max-width: 300px;
    }
}

@media (max-width: 768px) {
    .page-content-children {
        padding: 15px;
    }
    
    .books-grid-children {
        grid-template-columns: 1fr;
        max-width: 350px;
        margin: 0 auto 30px;
    }
    
    .book-card-children {
        max-width: 350px;
        height: 360px;
    }
    
    .book-image-children {
        height: 130px;
        margin: 15px 0 15px 0;
    }
    
    .book-image-children img {
        max-height: 130px;
    }
}


/* Стилове за действия с книгата - Детска литература */
.book-actions-children {
    display: flex;
    gap: 10px;
    margin-top: auto;
    margin-bottom: 5px;
}

.book-actions-children .add-to-cart-btn-children {
    flex: 1;
    margin-top: 0;
    padding: 12px 0;
}

/* Бутон за любими за детска литература */
.add-to-wishlist-btn-children {
    background: transparent;
    border: 2px solid #FF6B6B; /* Розов цвят като заглавието */
    color: #FF6B6B;
    width: 44px;
    height: 44px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.add-to-wishlist-btn-children:hover {
    background: #FF6B6B;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 107, 107, 0.2);
}

.add-to-wishlist-btn-children.added {
    background: #FF6B6B;
    color: white;
    border-color: #FF6B6B;
}

.add-to-wishlist-btn-children.added i {
    content: "\f004" !important; /* Пълно сърце */
}

/* Увеличи височината на картата */
.book-card-children {
    height: 400px; /* Увеличена височина за да има място за двата бутона */
}

/* Намали малко височината на изображението */
.book-image-children {
    height: 130px; /* Същата или малко по-ниска височина */
}

/* Коригирай позиционирането на текста */
.book-title-children {
    margin-bottom: 8px;
    min-height: 40px; /* Минимална височина за 2 реда */
}

/* Адаптивност за мобилни устройства */
@media (max-width: 768px) {
    .book-card-children {
        height: 380px;
    }
    
    .book-actions-children {
        gap: 8px;
    }
    
    .add-to-wishlist-btn-children {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}


</style>

<div class="page-content-children">
    <h1> Детска литература</h1>
    
    <?php if ($total_books > 0): ?>
    <div class="page-info-children">
        Страница <?= $current_page ?> от <?= $total_pages ?> | 
        Показване на <?= min($books_per_page, $total_books - $offset) ?> книги
        <?php if ($offset + 1 <= $total_books): ?>
        (<?= ($offset + 1) ?>-<?= min($offset + $books_per_page, $total_books) ?>)
        <?php endif; ?>
        от <?= $total_books ?> общо
    </div>
    <?php endif; ?>
    
    <?php
    if ($total_books > 0) {
        // Вземане на книгите
        $sql = "SELECT * FROM books WHERE is_children = 1 ORDER BY id DESC LIMIT ? OFFSET ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $books_per_page, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    
    if ($total_books > 0 && isset($result) && $result->num_rows > 0):
    ?>
    
    <div class="books-grid-children">
        <?php 
        $counter = $offset + 1;
        while ($book = $result->fetch_assoc()): 
        ?>
        <div class="book-card-children">
            <!-- Снимка -->
            <div class="book-image-children">
                <img src="<?= htmlspecialchars($book['image']) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>"
                     onerror="this.src='https://via.placeholder.com/100x140/e0e0e0/333?text=Книга'">
            </div>
            
            <!-- Заглавие -->
            <div class="book-title-children">
                <h3>
                    <a href="book.php?id=<?= $book['id'] ?>" 
                       class="book-title-link-children">
                        <?= htmlspecialchars(mb_strlen($book['title']) > 60 ? mb_substr($book['title'], 0, 57) . '...' : $book['title']) ?>
                    </a>
                </h3>
            </div>
            
            <!-- Автор -->
            <p class="book-author-children">
                <a href="author_books.php?author=<?= urlencode($book['author']) ?>" 
                   class="author-link-children">
                    <?= htmlspecialchars(mb_strlen($book['author']) > 30 ? mb_substr($book['author'], 0, 27) . '...' : $book['author']) ?>
                </a>
            </p>
            
            <!-- Цена и продажби -->
            <div class="price-sales-children">
                <span class="book-price-children"><?= number_format($book['price'], 2) ?> лв.</span>
                <span class="book-sales-children"><?= $book['sales'] ?> продажби</span>
            </div>
            
            <!-- Бутон за добавяне в кошницата -->
            <div class="book-actions-children">
    <button class="add-to-cart-btn-children" 
            onclick="addToCartNew(<?= $book['id'] ?>, this, 'children')">
        Добави в кошницата
    </button>
    <?php if (isset($_SESSION['user_id'])): ?>
    <?php 
    // Проверка дали книгата е вече в любими
    $check_wishlist_sql = "SELECT id FROM wishlist WHERE user_id = ? AND book_id = ?";
    $check_wishlist_stmt = $conn->prepare($check_wishlist_sql);
    $check_wishlist_stmt->bind_param("ii", $_SESSION['user_id'], $book['id']);
    $check_wishlist_stmt->execute();
    $wishlist_result = $check_wishlist_stmt->get_result();
    $is_in_wishlist = $wishlist_result->num_rows > 0;
    $check_wishlist_stmt->close();
    ?>
    <button class="add-to-wishlist-btn-children <?= $is_in_wishlist ? 'added' : '' ?>" 
            onclick="addToWishlistChildren(<?= $book['id'] ?>, this)">
        <i class="<?= $is_in_wishlist ? 'fas fa-heart' : 'far fa-heart' ?>"></i>
    </button>
    <?php endif; ?>
</div>
        </div>
        <?php 
        $counter++;
        endwhile; 
        ?>
    </div>
    
    <?php
    // Пагинация
    if ($total_pages > 1):
    ?>
    <div class="pagination-section-children">
        <div class="pagination-children">
            <?php if ($current_page > 1): ?>
            <a href="children_books.php?page=1">« Първа</a>
            <?php endif; ?>
            
            <?php if ($current_page > 1): ?>
            <a href="children_books.php?page=<?= $current_page - 1 ?>">‹</a>
            <?php endif; ?>
            
            <?php 
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="children_books.php?page=<?= $i ?>" 
               class="<?= $i == $current_page ? 'active-page' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="children_books.php?page=<?= $current_page + 1 ?>">›</a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="children_books.php?page=<?= $total_pages ?>">Последна »</a>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <?php if ($current_page > 1): ?>
            <a href="children_books.php?page=<?= $current_page - 1 ?>" class="page-nav-btn-children">
                ← Предишна страница
            </a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="children_books.php?page=<?= $current_page + 1 ?>" class="page-nav-btn-children">
                Следваща страница →
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="empty-state-children">
        <p>Все още няма добавени книги в "Детска литература".</p>
        <p>Моля, върнете се по-късно или разгледайте нашите <a href="new_books.php">нови книги</a>.</p>
        <a href="index.php">← Върни се към началната страница</a>
    </div>
    <?php endif; ?>
</div>

<script>
// ФУНКЦИЯ ЗА ДОБАВЯНЕ В КОШНИЦАТА
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

// Функция за прости съобщения
function showSimpleMessage(text, type) {
    let msg = document.querySelector('.simple-message');
    if (msg) msg.remove();
    
    msg = document.createElement('div');
    msg.className = 'simple-message';
    msg.textContent = text;
    msg.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4CAF50' : '#f44336'};
        color: white;
        border-radius: 6px;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease;
        font-family: Arial, sans-serif;
        max-width: 300px;
    `;
    
    document.body.appendChild(msg);
    
    setTimeout(() => {
        msg.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => msg.remove(), 300);
    }, 3000);
}

// Добавяне на CSS анимации
if (!document.querySelector('#msg-animations')) {
    const style = document.createElement('style');
    style.id = 'msg-animations';
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
}


// ФУНКЦИЯ ЗА ДОБАВЯНЕ В ЛЮБИМИ (Детска литература)
function addToWishlistChildren(bookId, button) {
    if (!button) return;
    
    // Проверка дали потребителят е влязъл
    <?php if (!isset($_SESSION['user_id'])): ?>
        showSimpleMessage('Моля, влезте в профила си за да добавите в любими!', 'info', 'children');
        return;
    <?php endif; ?>
    
    const isAdded = button.classList.contains('added');
    const originalIcon = button.innerHTML;
    
    if (isAdded) {
        // Премахване от любими
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        removeFromWishlistChildren(bookId, button, originalIcon);
    } else {
        // Добавяне в любими
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        button.disabled = true;
        
        fetch('add_to_wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'book_id=' + bookId + '&action=add'
        })
        .then(response => response.json().catch(() => ({})))
        .then(data => {
            button.disabled = false;
            
            if (data.success) {
                button.classList.add('added');
                button.innerHTML = '<i class="fas fa-heart"></i>';
                showSimpleMessage('✓ Книгата е добавена в любими!', 'success', 'children');
                
                // Обновяване на брояча за любими (ако има такъв)
                const wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    let current = parseInt(wishlistCount.textContent) || 0;
                    wishlistCount.textContent = current + 1;
                }
            } else {
                button.innerHTML = originalIcon;
                showSimpleMessage('✗ ' + (data.message || 'Грешка при добавяне'), 'error', 'children');
            }
        })
        .catch(error => {
            console.error('Грешка:', error);
            button.disabled = false;
            button.innerHTML = originalIcon;
            showSimpleMessage('✗ Възникна грешка', 'error', 'children');
        });
    }
}

// ФУНКЦИЯ ЗА ПРЕМАХВАНЕ ОТ ЛЮБИМИ (Детска литература)
function removeFromWishlistChildren(bookId, button, originalIcon) {
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
            button.classList.remove('added');
            button.innerHTML = '<i class="far fa-heart"></i>';
            showSimpleMessage('✓ Книгата е премахната от любими', 'success', 'children');
            
            // Обновяване на брояча за любими
            const wishlistCount = document.getElementById('wishlist-count');
            if (wishlistCount) {
                let current = parseInt(wishlistCount.textContent) || 0;
                if (current > 0) {
                    wishlistCount.textContent = current - 1;
                }
            }
        } else {
            button.innerHTML = originalIcon;
            showSimpleMessage('✗ ' + (data.message || 'Грешка при премахване'), 'error', 'children');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        button.disabled = false;
        button.innerHTML = originalIcon;
        showSimpleMessage('✗ Възникна грешка', 'error', 'children');
    });
}

// ФУНКЦИЯ ЗА ПРОВЕРКА ДАЛИ КНИГА Е В ЛЮБИМИ (за Детска литература)
function checkWishlistStatusChildren() {
    <?php if (isset($_SESSION['user_id'])): ?>
    const bookIds = [];
    document.querySelectorAll('.add-to-wishlist-btn-children').forEach(btn => {
        const match = btn.getAttribute('onclick')?.match(/addToWishlistChildren\((\d+)/);
        if (match) {
            bookIds.push(match[1]);
        }
    });
    
    if (bookIds.length > 0) {
        fetch('check_wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ book_ids: bookIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Маркираме вече добавените книги
                data.wishlist_books.forEach(bookId => {
                    const btn = document.querySelector(`[onclick*="addToWishlistChildren(${bookId}"]`);
                    if (btn) {
                        btn.classList.add('added');
                        btn.innerHTML = '<i class="fas fa-heart"></i>';
                    }
                });
            }
        })
        .catch(error => console.error('Грешка при проверка:', error));
    }
    <?php endif; ?>
}

// Промени функцията showSimpleMessage да поддържа цвета за "children"
function showSimpleMessage(text, type, pageType = 'default') {
    let msg = document.querySelector('.simple-message');
    if (msg) msg.remove();
    
    // Определяне на цвета според типа на страницата
    let bgColor;
    if (type === 'success') {
        if (pageType === 'children') {
            bgColor = '#FF6B6B'; /* Розов за детска литература */
        } else if (pageType === 'mandatory') {
            bgColor = '#1a237e'; /* Син за задължително четене */
        } else {
            bgColor = '#4CAF50'; /* Зелен по подразбиране */
        }
    } else if (type === 'error') {
        bgColor = '#f44336'; /* Червен за грешки */
    } else if (type === 'info') {
        bgColor = '#2196F3'; /* Светло син за информация */
    } else {
        bgColor = '#4CAF50'; /* По подразбиране */
    }
    
    msg = document.createElement('div');
    msg.className = 'simple-message';
    msg.textContent = text;
    msg.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${bgColor};
        color: white;
        border-radius: 6px;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease;
        font-family: Arial, sans-serif;
        max-width: 300px;
    `;
    
    document.body.appendChild(msg);
    
    setTimeout(() => {
        msg.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => msg.remove(), 300);
    }, 3000);
}

// Извикваме функцията при зареждане на страницата
document.addEventListener('DOMContentLoaded', function() {
    checkWishlistStatusChildren();
});
</script>
<?php 
if (isset($stmt)) {
    $stmt->close();
}
include 'footer.php'; 
?>