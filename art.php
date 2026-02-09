<?php
include 'config.php';
$page_title = 'Изкуство - Художествени творби';
include 'header.php';

// Брой книги на страница
$books_per_page = 50;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $books_per_page;

// Вземане на общия брой книги за изкуство
$total_stmt = $conn->prepare("SELECT COUNT(*) as total FROM books WHERE is_art = 1");
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
/* Стилове за Изкуство */
.page-content-art {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-content-art h1 {
    color: #333;
    padding-bottom: 15px;
    margin-bottom: 30px;
    font-size: 32px;
    font-weight: 600;
    border-bottom: 3px solid #d32f2f;
    text-align: center;
}

/* Мрежа от книги - 5 колони */
.books-grid-art {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 40px;
    justify-items: center;
}

/* Картичка за книга */
.book-card-art {
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
    height: 420px;
}

.book-card-art:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: #d32f2f;
}

/* Снимка */
.book-image-art {
    text-align: center;
    margin-bottom: 15px;
    flex-shrink: 0;
    height: 150px; /* Намалена височина като в топ */
    display: flex;
    align-items: center;
    justify-content: center;
}

.book-image-art img {
    max-height: 150px;
    max-width: 120px;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* Заглавие */
.book-title-art {
    margin-bottom: 10px;
    min-height: 45px; /* Като в топ */
}

.book-title-link-art {
    color: #333;
    text-decoration: none;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2; /* 2 реда като в топ */
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.3s ease;
}

.book-title-link-art:hover {
    color: #d32f2f;
    text-decoration: underline;
}

/* Автор - ПОПРАВЕНО */
.book-author-art {
    color: #666;
    margin: 0 0 8px 0; /* Като в топ */
    font-size: 13px;
    font-style: italic;
    height: 18px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.author-link-art {
    color: #666;
    text-decoration: none;
    font-style: italic;
    transition: color 0.3s ease;
}

.author-link-art:hover {
    color: #d32f2f;
    text-decoration: underline;
}

/* Цена и продажби */
.price-sales-art {
    margin-bottom: 15px;
}

.book-price-art {
    color: #d32f2f;
    font-weight: bold;
    font-size: 17px;
    margin-bottom: 4px;
}

.book-sales-art {
    color: #666;
    font-size: 12px;
    background: #f5f5f5;
    padding: 4px 10px;
    border-radius: 12px;
    display: inline-block;
}

/* Стилове за действия с книгата - Изкуство */
.book-actions-art {
    display: flex;
    gap: 10px;
    margin-top: auto;
    margin-bottom: 5px;
}

/* БУТОН ЗА ДОБАВЯНЕ В КОШНИЦАТА - КАТО В ТОП */
.add-to-cart-btn-art {
    background: #d32f2f;
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
    flex: 1;
    margin-top: 0;
}

.add-to-cart-btn-art:hover {
    background: #b71c1c;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(211, 47, 47, 0.2);
}

/* Бутон за любими за изкуство */
.add-to-wishlist-btn-art {
    background: transparent;
    border: 2px solid #d32f2f; /* Червен цвят като заглавието */
    color: #d32f2f;
    width: 44px; /* Като в топ */
    height: 44px; /* Като в топ */
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.add-to-wishlist-btn-art:hover {
    background: #d32f2f;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(211, 47, 47, 0.2);
}

.add-to-wishlist-btn-art.added {
    background: #d32f2f;
    color: white;
    border-color: #d32f2f;
}

/* Пагинация */
.pagination-section-art {
    margin-top: 40px;
    text-align: center;
}

.pagination-art {
    display: flex;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.pagination-art a {
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

.pagination-art a:hover {
    background: #d32f2f;
    color: white;
    border-color: #d32f2f;
}

.pagination-art a.active-page {
    background: #d32f2f;
    color: white;
    border-color: #d32f2f;
    font-weight: bold;
}

.page-info-art {
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
.empty-state-art {
    text-align: center;
    padding: 50px 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 30px 0;
}

.empty-state-art p {
    color: #666;
    font-size: 16px;
    margin-bottom: 20px;
}

.empty-state-art a {
    color: #d32f2f;
    text-decoration: none;
    font-weight: bold;
    border: 2px solid #d32f2f;
    padding: 10px 20px;
    border-radius: 6px;
    display: inline-block;
    transition: all 0.3s;
}

.empty-state-art a:hover {
    background: #d32f2f;
    color: white;
}

/* Навигация между страници */
.page-nav-btn-art {
    display: inline-block;
    margin: 0 10px;
    padding: 8px 20px;
    background: #d32f2f;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: all 0.3s;
    border: 2px solid #d32f2f;
}

.page-nav-btn-art:hover {
    background: white;
    color: #d32f2f;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(211, 47, 47, 0.2);
}

/* Адаптивен дизайн */
@media (max-width: 1400px) {
    .books-grid-art {
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    
    .book-card-art {
        max-width: 260px;
    }
}

@media (max-width: 1200px) {
    .books-grid-art {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .book-card-art {
        max-width: 280px;
    }
}

@media (max-width: 992px) {
    .books-grid-art {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .book-card-art {
        max-width: 300px;
    }
}

@media (max-width: 768px) {
    .page-content-art {
        padding: 15px;
    }
    
    .books-grid-art {
        grid-template-columns: 1fr;
        max-width: 350px;
        margin: 0 auto 30px;
    }
    
    .book-card-art {
        max-width: 350px;
        height: 400px;
    }
    
    .book-image-art {
        height: 140px;
    }
    
    .book-image-art img {
        max-height: 140px;
    }
    
    .book-actions-art {
        gap: 8px;
    }
    
    .add-to-wishlist-btn-art {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}

</style>

<div class="page-content-art">
    <h1>Изкуство</h1>
    
    <?php if ($total_books > 0): ?>
    <div class="page-info-art">
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
        // Вземане на книги за изкуство
        $sql = "
            SELECT * FROM books 
            WHERE is_art = 1 
            ORDER BY sales DESC, id DESC 
            LIMIT ? OFFSET ?
        ";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $books_per_page, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
    }
    
    if ($total_books > 0 && $result->num_rows > 0):
    ?>
    
    <div class="books-grid-art">
        <?php 
        $counter = $offset + 1;
        while ($book = $result->fetch_assoc()): 
        ?>
        <div class="book-card-art">
            <div class="book-image-art">
                <img src="<?= htmlspecialchars($book['image']) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>"
                     onerror="this.src='https://via.placeholder.com/120x160/ffebee/d32f2f?text=Изкуство'">
            </div>
            
            <div class="book-title-art">
                <h3>
                    <a href="book.php?id=<?= $book['id'] ?>" 
                       class="book-title-link-art">
                        <?= htmlspecialchars($book['title']) ?>
                    </a>
                </h3>
            </div>
            
            <div class="book-author-art">
                <a href="author_books.php?author=<?= urlencode($book['author']) ?>" 
                   class="author-link-art">
                    <?= htmlspecialchars($book['author']) ?>
                </a>
            </div>
            
           <div class="price-sales-art">
    <span class="book-price-art"><?= number_format($book['price'], 2) ?> лв.</span>
    <span class="book-sales-art"><?= $book['sales'] ?> продажби</span>
</div>

<!-- Бутони за действия с книгата -->
<div class="book-actions-art">
    <button class="add-to-cart-btn-art" 
            onclick="addToCartArt(<?= $book['id'] ?>, this)">
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
    <button class="add-to-wishlist-btn-art <?= $is_in_wishlist ? 'added' : '' ?>" 
            onclick="addToWishlistArt(<?= $book['id'] ?>, this)">
        <i class="<?= $is_in_wishlist ? 'fas fa-heart' : 'far fa-heart' ?>"></i>
    </button>
    <?php endif; ?>
</div>
        </div>
        <?php 
        $counter++;
        endwhile; 
        ?>
    
    <?php
    // Пагинация
    if ($total_pages > 1):
    ?>
    <div class="pagination-section-art">
        <div class="pagination-art">
            <?php if ($current_page > 1): ?>
            <a href="art.php?page=1">« Първа</a>
            <?php endif; ?>
            
            <?php if ($current_page > 1): ?>
            <a href="art.php?page=<?= $current_page - 1 ?>">‹</a>
            <?php endif; ?>
            
            <?php 
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="art.php?page=<?= $i ?>" 
               class="<?= $i == $current_page ? 'active-page' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="art.php?page=<?= $current_page + 1 ?>">›</a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="art.php?page=<?= $total_pages ?>">Последна »</a>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <?php if ($current_page > 1): ?>
            <a href="art.php?page=<?= $current_page - 1 ?>" class="page-nav-btn-art">
                ← Предишна страница
            </a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="art.php?page=<?= $current_page + 1 ?>" class="page-nav-btn-art">
                Следваща страница →
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="empty-state-art">
        <p>Все още няма налични книги за изкуство.</p>
        <p>Моля, върнете се по-късно или разгледайте нашите <a href="top_books.php">топ заглавия</a>.</p>
        <a href="index.php">← Върни се към началната страница</a>
    </div>
    <?php endif; ?>
</div>

<script>
// ФУНКЦИЯ ЗА ДОБАВЯНЕ В КОШНИЦАТА - за изкуство
function addToCartArt(productId, button) {
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
            
            showSimpleMessage('✓ Книгата е добавена в кошницата!', 'success', 'art');
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

// Функция за прости съобщения с цвят за изкуство
function showSimpleMessage(text, type, pageType = 'default') {
    let msg = document.querySelector('.simple-message');
    if (msg) msg.remove();
    
    // Определяне на цвета според типа на страницата
    let bgColor;
    if (type === 'success') {
        if (pageType === 'art') {
            bgColor = '#d32f2f'; // Червен за изкуство
        } else if (pageType === 'encyclopedia') {
            bgColor = '#0097a7'; // Тюркоазен син за енциклопедии
        } else if (pageType === 'mandatory') {
            bgColor = '#1a237e'; // Син за задължително четене
        } else if (pageType === 'ezoteric') {
            bgColor = '#7d4caf'; // Лилав за езотерика
        } else if (pageType === 'new') {
            bgColor = '#2196F3'; // Син за нови книги
        } else if (pageType === 'promo') {
            bgColor = '#e60000'; // Ярко червен за промоции
        } else if (pageType === 'top') {
            bgColor = '#e60000'; // Червен за топ заглавия
        } else {
            bgColor = '#4CAF50'; // Зелен по подразбиране
        }
    } else {
        bgColor = '#f44336'; // Червен за грешки
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

// ФУНКЦИЯ ЗА ДОБАВЯНЕ В ЛЮБИМИ (Изкуство)
function addToWishlistArt(bookId, button) {
    if (!button) return;
    
    // Проверка дали потребителят е влязъл
    <?php if (!isset($_SESSION['user_id'])): ?>
        showSimpleMessage('Моля, влезте в профила си за да добавите в любими!', 'info', 'art');
        return;
    <?php endif; ?>
    
    const isAdded = button.classList.contains('added');
    const originalIcon = button.innerHTML;
    
    if (isAdded) {
        // Премахване от любими
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        removeFromWishlistArt(bookId, button, originalIcon);
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
                showSimpleMessage('✓ Книгата е добавена в любими!', 'success', 'art');
                
                // Обновяване на брояча за любими (ако има такъв)
                const wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    let current = parseInt(wishlistCount.textContent) || 0;
                    wishlistCount.textContent = current + 1;
                }
            } else {
                button.innerHTML = originalIcon;
                showSimpleMessage('✗ ' + (data.message || 'Грешка при добавяне'), 'error', 'art');
            }
        })
        .catch(error => {
            console.error('Грешка:', error);
            button.disabled = false;
            button.innerHTML = originalIcon;
            showSimpleMessage('✗ Възникна грешка', 'error', 'art');
        });
    }
}

// ФУНКЦИЯ ЗА ПРЕМАХВАНЕ ОТ ЛЮБИМИ (Изкуство)
function removeFromWishlistArt(bookId, button, originalIcon) {
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
            showSimpleMessage('✓ Книгата е премахната от любими', 'success', 'art');
            
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
            showSimpleMessage('✗ ' + (data.message || 'Грешка при премахване'), 'error', 'art');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        button.disabled = false;
        button.innerHTML = originalIcon;
        showSimpleMessage('✗ Възникна грешка', 'error', 'art');
    });
}

// ФУНКЦИЯ ЗА ПРОВЕРКА ДАЛИ КНИГА Е В ЛЮБИМИ (за Изкуство)
function checkWishlistStatusArt() {
    <?php if (isset($_SESSION['user_id'])): ?>
    const bookIds = [];
    document.querySelectorAll('.add-to-wishlist-btn-art').forEach(btn => {
        const match = btn.getAttribute('onclick')?.match(/addToWishlistArt\((\d+)/);
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
                    const btn = document.querySelector(`[onclick*="addToWishlistArt(${bookId}"]`);
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

// Променете функцията showSimpleMessage да поддържа 'art' тип
function showSimpleMessage(text, type, pageType = 'default') {
    let msg = document.querySelector('.simple-message');
    if (msg) msg.remove();
    
    // Определяне на цвета според типа на страницата
    let bgColor;
    if (type === 'success') {
        if (pageType === 'art') {
            bgColor = '#d32f2f'; // Червен за изкуство
        } else if (pageType === 'encyclopedia') {
            bgColor = '#0097a7'; // Тюркоазен син за енциклопедии
        } else if (pageType === 'mandatory') {
            bgColor = '#1a237e'; // Син за задължително четене
        } else if (pageType === 'ezoteric') {
            bgColor = '#7d4caf'; // Лилав за езотерика
        } else if (pageType === 'children') {
            bgColor = '#FF6B6B'; // Розов за детска литература
        } else if (pageType === 'promo') {
            bgColor = '#e60000'; // Ярко червен за промоции
        } else {
            bgColor = '#4CAF50'; // Зелен по подразбиране
        }
    } else if (type === 'error') {
        bgColor = '#f44336'; // Червен за грешки
    } else if (type === 'info') {
        bgColor = '#2196F3'; // Син за информация
    } else {
        bgColor = '#4CAF50'; // По подразбиране
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
    checkWishlistStatusArt();
});

</script>

<?php 
if (isset($stmt)) {
    $stmt->close();
}
if (isset($total_stmt)) {
    $total_stmt->close();
}
$conn->close();
include 'footer.php'; 
?>