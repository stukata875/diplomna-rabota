<?php
include 'config.php';
$page_title = 'Промо книги - Специални оферти';
include 'header.php';

// Брой книги на страница
$books_per_page = 50;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $books_per_page;

// Вземане на общия брой промо книги
$total_stmt = $conn->prepare("SELECT COUNT(*) as total FROM books WHERE is_promo = 1");
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
/* Стилове за Промо книги - като top_books.php */
.page-content-promo {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-content-promo h1 {
    color: #333;
    padding-bottom: 15px;
    margin-bottom: 30px;
    font-size: 32px;
    font-weight: 600;
    border-bottom: 3px solid #e60000;
    text-align: center;
}

/* Мрежа от книги - 5 колони */
.books-grid-promo {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 40px;
    justify-items: center;
}

/* Картичка за книга */
.book-card-promo {
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
    height: 400px;
}

.book-card-promo:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: #e60000;
}



/* Снимка */
.book-image-promo {
    text-align: center;
    margin-bottom: 15px;
    flex-shrink: 0;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.book-image-promo img {
    max-height: 160px;
    max-width: 120px;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* Заглавие */
.book-title-promo {
    margin-bottom: 10px;
    min-height: 42px;
}

.book-title-link-promo {
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

.book-title-link-promo:hover {
    color: #e60000;
    text-decoration: underline;
}

/* Автор */
.book-author-promo {
    color: #666;
    margin: 0 0 3px 0;
    font-size: 13px;
    font-style: italic;
    height: 18px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.author-link-promo {
    color: #666;
    text-decoration: none;
    font-style: italic;
    transition: color 0.3s ease;
}

.author-link-promo:hover {
    color: #e60000;
    text-decoration: underline;
}

/* Цена и продажби */
.price-sales-promo {
    margin-bottom: 15px;
}

.book-price-promo {
    color: #e60000;
    font-weight: bold;
    font-size: 17px;
    margin-bottom: 4px;
}

.book-sales-promo {
    color: #666;
    font-size: 12px;
    background: #f5f5f5;
    padding: 4px 10px;
    border-radius: 12px;
    display: inline-block;
}

/* Бутон */
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
    margin-top: auto;
}

.add-to-cart-btn-new:hover {
    background: #c40000;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(230, 0, 0, 0.2);
}

/* Пагинация */
.pagination-section-promo {
    margin-top: 40px;
    text-align: center;
}

.pagination-promo {
    display: flex;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.pagination-promo a {
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

.pagination-promo a:hover {
    background: #e60000;
    color: white;
    border-color: #e60000;
}

.pagination-promo a.active-page {
    background: #e60000;
    color: white;
    border-color: #e60000;
    font-weight: bold;
}

.page-info-promo {
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
.empty-state-promo {
    text-align: center;
    padding: 50px 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 30px 0;
}

.empty-state-promo p {
    color: #666;
    font-size: 16px;
    margin-bottom: 20px;
}

.empty-state-promo a {
    color: #e60000;
    text-decoration: none;
    font-weight: bold;
    border: 2px solid #e60000;
    padding: 10px 20px;
    border-radius: 6px;
    display: inline-block;
    transition: all 0.3s;
}

.empty-state-promo a:hover {
    background: #e60000;
    color: white;
}

/* Навигация между страници */
.page-nav-btn-promo {
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

.page-nav-btn-promo:hover {
    background: white;
    color: #e60000;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(230, 0, 0, 0.2);
}

/* Адаптивен дизайн */
@media (max-width: 1400px) {
    .books-grid-promo {
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    
    .book-card-promo {
        max-width: 260px;
    }
}

@media (max-width: 1200px) {
    .books-grid-promo {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .book-card-promo {
        max-width: 280px;
    }
}

@media (max-width: 992px) {
    .books-grid-promo {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .book-card-promo {
        max-width: 300px;
    }
}

@media (max-width: 768px) {
    .page-content-promo {
        padding: 15px;
    }
    
    .books-grid-promo {
        grid-template-columns: 1fr;
        max-width: 350px;
        margin: 0 auto 30px;
    }
    
    .book-card-promo {
        max-width: 350px;
        height: 380px;
    }
    
    .book-image-promo {
        height: 150px;
    }
    
    .book-image-promo img {
        max-height: 150px;
    }
}

/* Стилове за действия с книгата - Промо книги */
.book-actions-promo {
    display: flex;
    gap: 10px;
    margin-top: auto;
    margin-bottom: 5px;
}

.book-actions-promo .add-to-cart-btn-new {
    flex: 1;
    margin-top: 0;
    padding: 12px 0;
}

/* Бутон за любими за промо книги */
.add-to-wishlist-btn-promo {
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

.add-to-wishlist-btn-promo:hover {
    background: #e60000;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(230, 0, 0, 0.2);
}

.add-to-wishlist-btn-promo.added {
    background: #e60000;
    color: white;
    border-color: #e60000;
}

.add-to-wishlist-btn-promo.added i {
    content: "\f004" !important; /* Пълно сърце */
}

/* Увеличи височината на картата */
.book-card-promo {
    height: 420px; /* Увеличена височина */
}

/* Намали височината на изображението */
.book-image-promo {
    height: 150px; /* Намалена височина */
}

/* Коригирай позиционирането на текста */
.book-title-promo {
    min-height: 45px; /* Малко повече място за заглавието */
}

.book-author-promo {
    margin-bottom: 8px; /* Малко повече място под автора */
}

/* Адаптивност за мобилни устройства */
@media (max-width: 768px) {
    .book-card-promo {
        height: 400px;
    }
    
    .book-actions-promo {
        gap: 8px;
    }
    
    .add-to-wishlist-btn-promo {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}

</style>

<div class="page-content-promo">
    <h1>Промо книги</h1>
    
    <?php if ($total_books > 0): ?>
    <div class="page-info-promo">
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
        // Вземане на промо книги с цена по-малка от 10 лв.
        $sql = "
            SELECT * FROM books 
            WHERE is_promo = 1 
            ORDER BY price ASC, sales DESC, id DESC 
            LIMIT ? OFFSET ?
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $books_per_page, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    
    if ($total_books > 0 && $result->num_rows > 0):
    ?>
    
    <div class="books-grid-promo">
        <?php 
        $counter = $offset + 1;
        while ($book = $result->fetch_assoc()): 
        ?>
        <div class="book-card-promo">
          
            
            <div class="book-image-promo">
                <img src="<?= htmlspecialchars($book['image']) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>"
                     onerror="this.src='https://via.placeholder.com/120x160/e0e0e0/333?text=Книга'">
            </div>
            
            <div class="book-title-promo">
                <h3>
                    <a href="book.php?id=<?= $book['id'] ?>" 
                       class="book-title-link-promo">
                        <?= htmlspecialchars(mb_strlen($book['title']) > 60 ? mb_substr($book['title'], 0, 57) . '...' : $book['title']) ?>
                    </a>
                </h3>
            </div>
            
            <p class="book-author-promo">
                <a href="author_books.php?author=<?= urlencode($book['author']) ?>" 
                   class="author-link-promo">
                    <?= htmlspecialchars(mb_strlen($book['author']) > 30 ? mb_substr($book['author'], 0, 27) . '...' : $book['author']) ?>
                </a>
            </p>
            
            <div class="price-sales-promo">
                <span class="book-price-promo"><?= number_format($book['price'], 2) ?> лв.</span>
                <span class="book-sales-promo"><?= $book['sales'] ?> продажби</span>
            </div>
            
           <div class="book-actions-promo">
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
    <button class="add-to-wishlist-btn-promo <?= $is_in_wishlist ? 'added' : '' ?>" 
            onclick="addToWishlistPromo(<?= $book['id'] ?>, this)">
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
    <div class="pagination-section-promo">
        <div class="pagination-promo">
            <?php if ($current_page > 1): ?>
            <a href="promo_books.php?page=1">« Първа</a>
            <?php endif; ?>
            
            <?php if ($current_page > 1): ?>
            <a href="promo_books.php?page=<?= $current_page - 1 ?>">‹</a>
            <?php endif; ?>
            
            <?php 
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="promo_books.php?page=<?= $i ?>" 
               class="<?= $i == $current_page ? 'active-page' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="promo_books.php?page=<?= $current_page + 1 ?>">›</a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="promo_books.php?page=<?= $total_pages ?>">Последна »</a>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <?php if ($current_page > 1): ?>
            <a href="promo_books.php?page=<?= $current_page - 1 ?>" class="page-nav-btn-promo">
                ← Предишна страница
            </a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="promo_books.php?page=<?= $current_page + 1 ?>" class="page-nav-btn-promo">
                Следваща страница →
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="empty-state-promo">
        <p>Все още няма налични промо книги.</p>
        <p>Моля, върнете се по-късно или разгледайте нашите <a href="top_books.php">топ заглавия</a>.</p>
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

// Функция за подчертаване на авторите при ховър
document.addEventListener('DOMContentLoaded', function() {
    // Намери всички връзки на автори
    const authorLinks = document.querySelectorAll('.author-link-promo');
    
    // Добави събитие за ховър
    authorLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.textDecoration = 'underline';
        });
        
        link.addEventListener('mouseleave', function() {
            this.style.textDecoration = 'none';
        });
    });
});



// ФУНКЦИЯ ЗА ДОБАВЯНЕ В ЛЮБИМИ (Промо книги)
function addToWishlistPromo(bookId, button) {
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
        removeFromWishlistPromo(bookId, button, originalIcon);
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

// ФУНКЦИЯ ЗА ПРЕМАХВАНЕ ОТ ЛЮБИМИ (Промо книги)
function removeFromWishlistPromo(bookId, button, originalIcon) {
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

// ФУНКЦИЯ ЗА ПРОВЕРКА ДАЛИ КНИГА Е В ЛЮБИМИ (за Промо книги)
function checkWishlistStatusPromo() {
    <?php if (isset($_SESSION['user_id'])): ?>
    const bookIds = [];
    document.querySelectorAll('.add-to-wishlist-btn-promo').forEach(btn => {
        const match = btn.getAttribute('onclick')?.match(/addToWishlistPromo\((\d+)/);
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
                    const btn = document.querySelector(`[onclick*="addToWishlistPromo(${bookId}"]`);
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

// Промени функцията showSimpleMessage да поддържа 'info' тип
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

// Извикваме функцията при зареждане на страницата
document.addEventListener('DOMContentLoaded', function() {
    checkWishlistStatusPromo();
});

</script>

<?php 
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
include 'footer.php'; 
?>