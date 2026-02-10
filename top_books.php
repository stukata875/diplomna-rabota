<?php
include 'config.php';
$page_title = 'Топ заглавия - Препоръчани книги';
include 'header.php';

// Брой книги на страница
$books_per_page = 50;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $books_per_page;

// Проверка дали има колона top_position
$check_sql = "SHOW COLUMNS FROM books LIKE 'top_position'";
$check_result = $conn->query($check_sql);
$has_top_position = $check_result && $check_result->num_rows > 0;

// Вземане на общия брой топ книги
$total_stmt = $conn->prepare("SELECT COUNT(*) as total FROM books WHERE is_top = 1");
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_books = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_books / $books_per_page);

if ($total_pages > 0 && $current_page > $total_pages) {
    $current_page = $total_pages;
} elseif ($total_pages == 0) {
    $current_page = 1;
}
?> 
<style>
/* Стилове за Топ заглавия - като promo_books.php */
.page-content-top {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-content-top h1 {
    color: #333;
    padding-bottom: 15px;
    margin-bottom: 30px;
    font-size: 32px;
    font-weight: 600;
    border-bottom: 3px solid #e60000;
    text-align: center;
}

/* Мрежа от книги - 5 колони */
.books-grid-top {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 40px;
    justify-items: center;
}

/* Картичка за книга */
.book-card-top {
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
    position: relative;
    height: 420px; /* Увеличена височина */
}

.book-card-top:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: #e60000;
}

/* Снимка */
.book-image-top {
    text-align: center;
    margin-bottom: 15px;
    flex-shrink: 0;
    height: 150px; /* Намалена височина */
    display: flex;
    align-items: center;
    justify-content: center;
}

.book-image-top img {
    max-height: 150px;
    max-width: 120px;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* Заглавие */
.book-title-top {
    margin-bottom: 10px;
    min-height: 45px; /* Увеличена височина */
}

.book-title-link-top {
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

.book-title-link-top:hover {
    color: #e60000;
    text-decoration: underline;
}

/* Автор */
.book-author-top {
    color: #666;
    margin: 0 0 8px 0; /* Увеличен марджин */
    font-size: 13px;
    font-style: italic;
    height: 18px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.author-link-top {
    color: #666;
    text-decoration: none;
    font-style: italic;
    transition: color 0.3s ease;
}

.author-link-top:hover {
    color: #e60000;
    text-decoration: underline;
}

/* Цена и продажби */
.price-sales-top {
    margin-bottom: 15px;
}

.book-price-top {
    color: #e60000;
    font-weight: bold;
    font-size: 17px;
    margin-bottom: 4px;
}

.book-sales-top {
    color: #666;
    font-size: 12px;
    background: #f5f5f5;
    padding: 4px 10px;
    border-radius: 12px;
    display: inline-block;
}

/* Стилове за действия с книгата - Топ заглавия */
.book-actions-top {
    display: flex;
    gap: 10px;
    margin-top: auto;
    margin-bottom: 5px;
}

.book-actions-top .add-to-cart-btn-new {
    flex: 1;
    margin-top: 0;
    padding: 12px 0;
}

/* Бутон за добавяне в кошницата */
.add-to-cart-btn-new {
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
}

.add-to-cart-btn-new:hover {
    background: #c40000;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(230, 0, 0, 0.2);
}

/* Бутон за любими */
.add-to-wishlist-btn-top {
    background: transparent;
    border: 2px solid #e60000;
    color: #e60000;
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

.add-to-wishlist-btn-top:hover {
    background: #e60000;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(230, 0, 0, 0.2);
}

.add-to-wishlist-btn-top.added {
    background: #e60000;
    color: white;
    border-color: #e60000;
}

.add-to-wishlist-btn-top.added i {
    content: "\f004" !important; /* Пълно сърце */
}

/* Пагинация */
.pagination-section-top {
    margin-top: 40px;
    text-align: center;
}

.pagination-top {
    display: flex;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.pagination-top a {
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

.pagination-top a:hover {
    background: #e60000;
    color: white;
    border-color: #e60000;
}

.pagination-top a.active-page {
    background: #e60000;
    color: white;
    border-color: #e60000;
    font-weight: bold;
}

.page-info-top {
    text-align: center;
    margin-bottom: 25px;
    color: #666;
    font-size: 15px;
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

/* Празен списък */
.empty-state-top {
    text-align: center;
    padding: 50px 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 30px 0;
}

.empty-state-top p {
    color: #666;
    font-size: 16px;
    margin-bottom: 20px;
}

.empty-state-top a {
    color: #e60000;
    text-decoration: none;
    font-weight: bold;
    border: 2px solid #e60000;
    padding: 10px 20px;
    border-radius: 6px;
    display: inline-block;
    transition: all 0.3s;
}

.empty-state-top a:hover {
    background: #e60000;
    color: white;
}

/* Навигация между страници */
.page-nav-btn {
    display: inline-block;
    margin: 0 10px;
    padding: 8px 20px;
    background: #e60000;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: all 0.3s;
    border: 2px solid #e60000;
}

.page-nav-btn:hover {
    background: white;
    color: #e60000;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(230, 0, 0, 0.2);
}

/* Адаптивен дизайн */
@media (max-width: 1400px) {
    .books-grid-top {
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    
    .book-card-top {
        max-width: 260px;
    }
}

@media (max-width: 1200px) {
    .books-grid-top {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .book-card-top {
        max-width: 280px;
    }
}

@media (max-width: 992px) {
    .books-grid-top {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .book-card-top {
        max-width: 300px;
    }
}

@media (max-width: 768px) {
    .page-content-top {
        padding: 15px;
    }
    
    .books-grid-top {
        grid-template-columns: 1fr;
        max-width: 350px;
        margin: 0 auto 30px;
    }
    
    .book-card-top {
        max-width: 350px;
        height: 400px;
    }
    
    .book-image-top {
        height: 140px;
    }
    
    .book-image-top img {
        max-height: 140px;
    }
    
    .book-actions-top {
        gap: 8px;
    }
    
    .add-to-wishlist-btn-top {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}
</style>
<div class="page-content-top">
    <h1>Топ заглавия - Препоръчани книги</h1>
    
    <?php if ($total_books > 0): ?>
    <div class="page-info-top">
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
        // Различен SQL в зависимост от това дали имаме top_position
        if ($has_top_position) {
            $sql = "
                SELECT * FROM books 
                WHERE is_top = 1 
                ORDER BY 
                    CASE WHEN top_position IS NOT NULL THEN 0 ELSE 1 END,
                    top_position ASC,
                    sales DESC,
                    id DESC 
                LIMIT ? OFFSET ?
            ";
        } else {
            // Прост SQL без top_position
            $sql = "
                SELECT * FROM books 
                WHERE is_top = 1 
                ORDER BY sales DESC, id DESC 
                LIMIT ? OFFSET ?
            ";
        }
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $books_per_page, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    
    if ($total_books > 0 && $result->num_rows > 0):
    ?>
    
    <div class="books-grid-top">
        <?php 
        $counter = $offset + 1;
        while ($book = $result->fetch_assoc()): 
        ?>
        <div class="book-card-top">
            <!-- НЯМА БЕЙДЖ ТУК -->
            
            <div class="book-image-top">
                <img src="<?= htmlspecialchars($book['image']) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>"
                     onerror="this.src='https://via.placeholder.com/120x160/e0e0e0/333?text=Книга'">
            </div>
            
            <div class="book-title-top">
                <h3>
                    <a href="book.php?id=<?= $book['id'] ?>" 
                       class="book-title-link-top">
                        <?= htmlspecialchars(mb_strlen($book['title']) > 60 ? mb_substr($book['title'], 0, 57) . '...' : $book['title']) ?>
                    </a>
                </h3>
            </div>
            
      <p class="book-author-top">
    <a href="author_books.php?author=<?= urlencode($book['author']) ?>" 
       class="author-link-top">
        <?= htmlspecialchars(mb_strlen($book['author']) > 30 ? mb_substr($book['author'], 0, 27) . '...' : $book['author']) ?>
    </a>
</p>     
            <div class="price-sales-top">
                <span class="book-price-top"><?= number_format($book['price'], 2) ?> лв.</span>
                <span class="book-sales-top"><?= $book['sales'] ?> продажби</span>
            </div>
            
            <div class="book-actions-top">
                <button class="add-to-cart-btn-new" 
                        onclick="addToCartNew(<?= $book['id'] ?>, this)">
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
                <button class="add-to-wishlist-btn-top <?= $is_in_wishlist ? 'added' : '' ?>" 
                        onclick="addToWishlistTop(<?= $book['id'] ?>, this)"
                        >
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
    <div class="pagination-section-top">
        <div class="pagination-top">
            <?php if ($current_page > 1): ?>
            <a href="top_books.php?page=1">« Първа</a>
            <?php endif; ?>
            
            <?php if ($current_page > 1): ?>
            <a href="top_books.php?page=<?= $current_page - 1 ?>">‹</a>
            <?php endif; ?>
            
            <?php 
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="top_books.php?page=<?= $i ?>" 
               class="<?= $i == $current_page ? 'active-page' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="top_books.php?page=<?= $current_page + 1 ?>">›</a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="top_books.php?page=<?= $total_pages ?>">Последна »</a>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <?php if ($current_page > 1): ?>
            <a href="top_books.php?page=<?= $current_page - 1 ?>" class="page-nav-btn">
                ← Предишна страница
            </a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="top_books.php?page=<?= $current_page + 1 ?>" class="page-nav-btn">
                Следваща страница →
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="empty-state-top">
        <p>Все още няма добавени книги в "Топ заглавия".</p>
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
            const cartCount = document.querySelector('.cart-link span');
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

// ФУНКЦИЯ ЗА ДОБАВЯНЕ В ЛЮБИМИ (Топ заглавия)
function addToWishlistTop(bookId, button) {
    if (!button) return;
    
    // Проверка дали потребителят е влязъл
    <?php if (!isset($_SESSION['user_id'])): ?>
        showSimpleMessage('Моля, влезте в профила си за да добавите в любими!', 'info');
        return;
    <?php endif; ?>
    
    const isAdded = button.classList.contains('added');
    const originalIcon = button.innerHTML;
    
    if (isAdded) {
        // Премахване от любими
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        removeFromWishlistTop(bookId, button, originalIcon);
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
               
                showSimpleMessage('✓ Книгата е добавена в любими!', 'success');
                
                // Обновяване на брояча за любими (ако има такъв)
                const wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    let current = parseInt(wishlistCount.textContent) || 0;
                    wishlistCount.textContent = current + 1;
                }
            } else {
                button.innerHTML = originalIcon;
                showSimpleMessage('✗ ' + (data.message || 'Грешка при добавяне'), 'error');
            }
        })
        .catch(error => {
            console.error('Грешка:', error);
            button.disabled = false;
            button.innerHTML = originalIcon;
            showSimpleMessage('✗ Възникна грешка', 'error');
        });
    }
}

// ФУНКЦИЯ ЗА ПРЕМАХВАНЕ ОТ ЛЮБИМИ (Топ заглавия)
function removeFromWishlistTop(bookId, button, originalIcon) {
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
            
            showSimpleMessage('✓ Книгата е премахната от любими', 'success');
            
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

// ФУНКЦИЯ ЗА ПРОВЕРКА ДАЛИ КНИГА Е В ЛЮБИМИ (за Топ заглавия)
function checkWishlistStatusTop() {
    <?php if (isset($_SESSION['user_id'])): ?>
    const bookIds = [];
    document.querySelectorAll('.add-to-wishlist-btn-top').forEach(btn => {
        const match = btn.getAttribute('onclick')?.match(/addToWishlistTop\((\d+)/);
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
                    const btn = document.querySelector(`[onclick*="addToWishlistTop(${bookId}"]`);
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

// Функция за прости съобщения (разширена версия)
function showSimpleMessage(text, type) {
    let msg = document.querySelector('.simple-message');
    if (msg) msg.remove();
    
    let bgColor;
    switch(type) {
        case 'success': bgColor = '#4CAF50'; break;
        case 'error': bgColor = '#f44336'; break;
        case 'info': bgColor = '#2196F3'; break;
        default: bgColor = '#4CAF50';
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

// Извикваме функцията при зареждане на страницата
document.addEventListener('DOMContentLoaded', function() {
    checkWishlistStatusTop();
});
</script>
<?php 
if (isset($stmt)) {
    $stmt->close();
}
include 'footer.php'; 
?>