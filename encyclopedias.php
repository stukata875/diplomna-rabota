<?php
include 'config.php';
$page_title = 'Енциклопедии - Изчерпателни знания';
include 'header.php';

// Брой книги на страница
$books_per_page = 50;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $books_per_page;

// Вземане на общия брой енциклопедии
$total_stmt = $conn->prepare("SELECT COUNT(*) as total FROM books WHERE is_encyclopedia = 1");
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
/* Стилове за Енциклопедии - подобни на промо книгите */
.page-content-encyclopedia {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-content-encyclopedia h1 {
    color: #333;
    padding-bottom: 15px;
    margin-bottom: 30px;
    font-size: 32px;
    font-weight: 600;
    border-bottom: 3px solid #0097a7;
    text-align: center;
}

/* Мрежа от книги - 5 колони */
.books-grid-encyclopedia {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 40px;
    justify-items: center;
}

/* Картичка за книга */
.book-card-encyclopedia {
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
    height: 420px; /* УВЕЛИЧЕНА ВИСОЧИНА */
}

.book-card-encyclopedia:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: #0097a7;
}

/* Снимка */
.book-image-encyclopedia {
    text-align: center;
    margin-bottom: 15px;
    flex-shrink: 0;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.book-image-encyclopedia img {
    max-height: 160px;
    max-width: 120px;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* Заглавие */
.book-title-encyclopedia {
    margin-bottom: 10px;
    min-height: 52px; /* Увеличена височина */
    display: flex;
    align-items: flex-start;
}

.book-title-link-encyclopedia {
    color: #333;
    text-decoration: none;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 3; /* Позволява до 3 реда */
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.3s ease;
    width: 100%;
}

.book-title-link-encyclopedia:hover {
    color: #0097a7;
    text-decoration: underline;
}

/* Автор - ПОПРАВЕНО */
.book-author-encyclopedia {
    color: #666;
    margin: 8px 0 12px 0; /* Повече място */
    font-size: 13px;
    font-style: italic;
    min-height: 36px; /* Минимална височина за 2 реда */
    display: flex;
    align-items: flex-start;
}

.author-link-encyclopedia {
    color: #666;
    text-decoration: none;
    font-style: italic;
    transition: color 0.3s ease;
    display: -webkit-box;
    -webkit-line-clamp: 2; /* Позволява до 2 реда */
    -webkit-box-orient: vertical;
    overflow: hidden;
    width: 100%;
    line-height: 1.4;
}

.author-link-encyclopedia:hover {
    color: #0097a7;
    text-decoration: underline;
}

/* Цена и продажби */
.price-sales-encyclopedia {
    margin-bottom: 15px;
    min-height: 45px; /* Минимална височина */
}

.book-price-encyclopedia {
    color: #0097a7;
    font-weight: bold;
    font-size: 17px;
    margin-bottom: 4px;
}

.book-sales-encyclopedia {
    color: #666;
    font-size: 12px;
    background: #f5f5f5;
    padding: 4px 10px;
    border-radius: 12px;
    display: inline-block;
}

/* Бутон - ПОПРАВЕН */
.add-to-cart-btn-encyclopedia {
    background: #0097a7;
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

.add-to-cart-btn-encyclopedia:hover {
    background: #00838f;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 151, 167, 0.2);
}

/* Пагинация */
.pagination-section-encyclopedia {
    margin-top: 40px;
    text-align: center;
}

.pagination-encyclopedia {
    display: flex;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.pagination-encyclopedia a {
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

.pagination-encyclopedia a:hover {
    background: #0097a7;
    color: white;
    border-color: #0097a7;
}

.pagination-encyclopedia a.active-page {
    background: #0097a7;
    color: white;
    border-color: #0097a7;
    font-weight: bold;
}

.page-info-encyclopedia {
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
.empty-state-encyclopedia {
    text-align: center;
    padding: 50px 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 30px 0;
}

.empty-state-encyclopedia p {
    color: #666;
    font-size: 16px;
    margin-bottom: 20px;
}

.empty-state-encyclopedia a {
    color: #0097a7;
    text-decoration: none;
    font-weight: bold;
    border: 2px solid #0097a7;
    padding: 10px 20px;
    border-radius: 6px;
    display: inline-block;
    transition: all 0.3s;
}

.empty-state-encyclopedia a:hover {
    background: #0097a7;
    color: white;
}

/* Навигация между страници */
.page-nav-btn-encyclopedia {
    display: inline-block;
    margin: 0 10px;
    padding: 8px 20px;
    background: #0097a7;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: all 0.3s;
    border: 2px solid #0097a7;
}

.page-nav-btn-encyclopedia:hover {
    background: white;
    color: #0097a7;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 151, 167, 0.2);
}

/* Адаптивен дизайн */
@media (max-width: 1400px) {
    .books-grid-encyclopedia {
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    
    .book-card-encyclopedia {
        max-width: 260px;
        height: 430px;
    }
}

@media (max-width: 1200px) {
    .books-grid-encyclopedia {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .book-card-encyclopedia {
        max-width: 280px;
        height: 440px;
    }
    
    .book-title-encyclopedia {
        min-height: 56px;
    }
    
    .book-author-encyclopedia {
        min-height: 40px;
    }
}

@media (max-width: 992px) {
    .books-grid-encyclopedia {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .book-card-encyclopedia {
        max-width: 300px;
        height: 450px;
    }
    
    .book-title-encyclopedia {
        min-height: 60px;
    }
    
    .book-author-encyclopedia {
        min-height: 44px;
    }
}

@media (max-width: 768px) {
    .page-content-encyclopedia {
        padding: 15px;
    }
    
    .books-grid-encyclopedia {
        grid-template-columns: 1fr;
        max-width: 350px;
        margin: 0 auto 30px;
    }
    
    .book-card-encyclopedia {
        max-width: 350px;
        height: 430px;
    }
    
    .book-image-encyclopedia {
        height: 150px;
    }
    
    .book-image-encyclopedia img {
        max-height: 150px;
    }
    
    .book-title-encyclopedia {
        min-height: 56px;
    }
    
    .book-author-encyclopedia {
        min-height: 40px;
    }
}

/* Стилове за действия с книгата - Енциклопедии */
.book-actions-encyclopedia {
    display: flex;
    gap: 10px;
    margin-top: auto;
    margin-bottom: 5px;
}


/* Бутон за любими за енциклопедии */
.add-to-wishlist-btn-encyclopedia {
    background: transparent;
    border: 2px solid #0097a7; /* Син цвят като заглавието */
    color: #0097a7;
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

.add-to-wishlist-btn-encyclopedia:hover {
    background: #0097a7;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 151, 167, 0.2);
}

.add-to-wishlist-btn-encyclopedia.added {
    background: #0097a7;
    color: white;
    border-color: #0097a7;
}

/* Намали малко височината на картата за по-добро разпределение */
.book-card-encyclopedia {
    height: 420px;
}

/* Намали височината на изображението */
.book-image-encyclopedia {
    height: 140px;
}

/* Адаптивност за мобилни устройства */
@media (max-width: 768px) {
    .book-card-encyclopedia {
        height: 400px;
    }
    
    .book-actions-encyclopedia {
        gap: 8px;
    }
    
    .add-to-wishlist-btn-encyclopedia {
        width: 44px;
        height: 44px;
        font-size: 16px;
    }
}

</style>

<div class="page-content-encyclopedia">
    <h1>Енциклопедии</h1>
    
    <?php if ($total_books > 0): ?>
    <div class="page-info-encyclopedia">
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
        // Вземане на енциклопедии
        $sql = "
            SELECT * FROM books 
            WHERE is_encyclopedia = 1 
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
    
    <div class="books-grid-encyclopedia">
        <?php 
        $counter = $offset + 1;
        while ($book = $result->fetch_assoc()): 
        ?>
        <div class="book-card-encyclopedia">
            <div class="book-image-encyclopedia">
                <img src="<?= htmlspecialchars($book['image']) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>"
                     onerror="this.src='https://via.placeholder.com/120x160/e0f7fa/0097a7?text=Енциклопедия'">
            </div>
            
            <div class="book-title-encyclopedia">
                <h3>
                    <a href="book.php?id=<?= $book['id'] ?>" 
                       class="book-title-link-encyclopedia">
                        <?= htmlspecialchars($book['title']) ?>
                    </a>
                </h3>
            </div>
            
            <div class="book-author-encyclopedia">
                <a href="author_books.php?author=<?= urlencode($book['author']) ?>" 
                   class="author-link-encyclopedia">
                    <?= htmlspecialchars($book['author']) ?>
                </a>
            </div>
            
            <div class="price-sales-encyclopedia">
                <span class="book-price-encyclopedia"><?= number_format($book['price'], 2) ?> лв.</span>
                <span class="book-sales-encyclopedia"><?= $book['sales'] ?> продажби</span>
            </div>
            
            <!-- Бутони за действия с книгата -->
            <div class="book-actions-encyclopedia">
                <button class="add-to-cart-btn-encyclopedia" 
                        onclick="addToCartEncyclopedia(<?= $book['id'] ?>, this)">
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
                <button class="add-to-wishlist-btn-encyclopedia <?= $is_in_wishlist ? 'added' : '' ?>" 
                        onclick="addToWishlistEncyclopedia(<?= $book['id'] ?>, this)">
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
    <div class="pagination-section-encyclopedia">
        <div class="pagination-encyclopedia">
            <?php if ($current_page > 1): ?>
            <a href="encyclopedia.php?page=1">« Първа</a>
            <?php endif; ?>
            
            <?php if ($current_page > 1): ?>
            <a href="encyclopedia.php?page=<?= $current_page - 1 ?>">‹</a>
            <?php endif; ?>
            
            <?php 
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="encyclopedia.php?page=<?= $i ?>" 
               class="<?= $i == $current_page ? 'active-page' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="encyclopedia.php?page=<?= $current_page + 1 ?>">›</a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="encyclopedia.php?page=<?= $total_pages ?>">Последна »</a>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <?php if ($current_page > 1): ?>
            <a href="encyclopedia.php?page=<?= $current_page - 1 ?>" class="page-nav-btn-encyclopedia">
                ← Предишна страница
            </a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="encyclopedia.php?page=<?= $current_page + 1 ?>" class="page-nav-btn-encyclopedia">
                Следваща страница →
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="empty-state-encyclopedia">
        <p>Все още няма налични енциклопедии.</p>
        <p>Моля, върнете се по-късно или разгледайте нашите <a href="top_books.php">топ заглавия</a>.</p>
        <a href="index.php">← Върни се към началната страница</a>
    </div>
    <?php endif; ?>
</div>

<script>
// ФУНКЦИЯ ЗА ДОБАВЯНЕ В КОШНИЦАТА - за енциклопедии
function addToCartEncyclopedia(productId, button) {
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
            
            showSimpleMessage('✓ Книгата е добавена в кошницата!', 'success', 'encyclopedia');
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

// Функция за прости съобщения с цвят за енциклопедии
function showSimpleMessage(text, type, pageType = 'default') {
    let msg = document.querySelector('.simple-message');
    if (msg) msg.remove();
    
    // Определяне на цвета според типа на страницата
    let bgColor;
    if (type === 'success') {
        if (pageType === 'encyclopedia') {
            bgColor = '#0097a7'; // Тюркоазен син за енциклопедии
        } else if (pageType === 'mandatory') {
            bgColor = '#1a237e'; // Син за задължително четене
        } else if (pageType === 'ezoteric') {
            bgColor = '#7d4caf'; // Лилав за езотерика
        } else if (pageType === 'new') {
            bgColor = '#2196F3'; // Син за нови книги
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

// ФУНКЦИЯ ЗА ДОБАВЯНЕ В ЛЮБИМИ (Енциклопедии)
function addToWishlistEncyclopedia(bookId, button) {
    if (!button) return;
    
    // Проверка дали потребителят е влязъл
    <?php if (!isset($_SESSION['user_id'])): ?>
        showSimpleMessage('Моля, влезте в профила си за да добавите в любими!', 'info', 'encyclopedia');
        return;
    <?php endif; ?>
    
    const isAdded = button.classList.contains('added');
    const originalIcon = button.innerHTML;
    
    if (isAdded) {
        // Премахване от любими
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        removeFromWishlistEncyclopedia(bookId, button, originalIcon);
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
                showSimpleMessage('✓ Книгата е добавена в любими!', 'success', 'encyclopedia');
                
                // Обновяване на брояча за любими (ако има такъв)
                const wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    let current = parseInt(wishlistCount.textContent) || 0;
                    wishlistCount.textContent = current + 1;
                }
            } else {
                button.innerHTML = originalIcon;
                showSimpleMessage('✗ ' + (data.message || 'Грешка при добавяне'), 'error', 'encyclopedia');
            }
        })
        .catch(error => {
            console.error('Грешка:', error);
            button.disabled = false;
            button.innerHTML = originalIcon;
            showSimpleMessage('✗ Възникна грешка', 'error', 'encyclopedia');
        });
    }
}

// ФУНКЦИЯ ЗА ПРЕМАХВАНЕ ОТ ЛЮБИМИ (Енциклопедии)
function removeFromWishlistEncyclopedia(bookId, button, originalIcon) {
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
            showSimpleMessage('✓ Книгата е премахната от любими', 'success', 'encyclopedia');
            
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
            showSimpleMessage('✗ ' + (data.message || 'Грешка при премахване'), 'error', 'encyclopedia');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        button.disabled = false;
        button.innerHTML = originalIcon;
        showSimpleMessage('✗ Възникна грешка', 'error', 'encyclopedia');
    });
}

// ФУНКЦИЯ ЗА ПРОВЕРКА ДАЛИ КНИГА Е В ЛЮБИМИ (за Енциклопедии)
function checkWishlistStatusEncyclopedia() {
    <?php if (isset($_SESSION['user_id'])): ?>
    const bookIds = [];
    document.querySelectorAll('.add-to-wishlist-btn-encyclopedia').forEach(btn => {
        const match = btn.getAttribute('onclick')?.match(/addToWishlistEncyclopedia\((\d+)/);
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
                    const btn = document.querySelector(`[onclick*="addToWishlistEncyclopedia(${bookId}"]`);
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

// Променете функцията showSimpleMessage да поддържа 'info' тип
function showSimpleMessage(text, type, pageType = 'default') {
    let msg = document.querySelector('.simple-message');
    if (msg) msg.remove();
    
    // Определяне на цвета според типа на страницата
    let bgColor;
    if (type === 'success') {
        if (pageType === 'encyclopedia') {
            bgColor = '#0097a7'; /* Тюркоазен син за енциклопедии */
        } else if (pageType === 'mandatory') {
            bgColor = '#1a237e'; /* Син за задължително четене */
        } else if (pageType === 'ezoteric') {
            bgColor = '#7d4caf'; /* Лилав за езотерика */
        } else if (pageType === 'children') {
            bgColor = '#FF6B6B'; /* Розов за детска литература */
        } else {
            bgColor = '#4CAF50'; /* Зелен по подразбиране */
        }
    } else if (type === 'error') {
        bgColor = '#f44336'; /* Червен за грешки */
    } else if (type === 'info') {
        bgColor = '#2196F3'; /* Син за информация */
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
    checkWishlistStatusEncyclopedia();
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