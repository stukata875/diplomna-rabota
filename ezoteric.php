<?php
include 'config.php';
$page_title = 'Езотерика и духовни учения';
include 'header.php';

// Брой книги на страница
$books_per_page = 50;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $books_per_page;

// Промяна тук: Взимаме книги, където is_ezoteric = 1
// Вземане на общия брой книги в езотерика
$total_stmt = $conn->prepare("
    SELECT COUNT(*) as total 
    FROM books 
    WHERE is_ezoteric = 1 
");
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
/* Стилове за Езотерика страница */
.page-content-esoteric {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-content-esoteric h1 {
    color: #333;
    padding-bottom: 15px;
    margin-bottom: 30px;
    font-size: 32px;
    font-weight: 600;
    border-bottom: 3px solid #7d4caf;
    text-align: center;
}

/* Мрежа от книги - 5 колони */
.books-grid-esoteric {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 40px;
    justify-items: center;
}

/* Картичка за книга */
.book-card-esoteric {
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

.book-card-esoteric:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: #7d4caf;
}

/* Снимка */
.book-image-esoteric {
    text-align: center;
    margin-bottom: 15px;
    flex-shrink: 0;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.book-image-esoteric img {
    max-height: 160px;
    max-width: 120px;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* Заглавие - ПОПРАВЕНО за дълги заглавия */
.book-title-esoteric {
    margin-bottom: 10px;
    min-height: 52px;
    display: flex;
    align-items: flex-start; /* Променено от center на flex-start */
}

.book-title-link-esoteric {
    color: #333;
    text-decoration: none;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    transition: color 0.3s ease;
    width: 100%;
}

.book-title-link-esoteric:hover {
    color: #7d4caf;
    text-decoration: underline;
}

/* Автор - ПОПРАВЕНО за дълги имена */
.book-author-esoteric {
    color: #666;
    margin: 8px 0 12px 0; /* ПОВЕЧЕ МЯСТО ОТДОЛУ */
    font-size: 13px;
    font-style: italic;
    min-height: 36px;
    display: flex;
    align-items: flex-start; /* Променено от center на flex-start */
}

.author-link-esoteric {
    color: #666;
    text-decoration: none;
    font-style: italic;
    transition: color 0.3s ease;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    width: 100%;
    line-height: 1.4;
}

.author-link-esoteric:hover {
    color: #7d4caf;
    text-decoration: underline;
}

/* Цена и продажби */
.price-sales-esoteric {
    margin-bottom: 15px;
    min-height: 45px;
}

.book-price-esoteric {
    color: #7d4caf;
    font-weight: bold;
    font-size: 17px;
    margin-bottom: 4px;
}

.book-sales-esoteric {
    color: #666;
    font-size: 12px;
    background: #f5f5f5;
    padding: 4px 10px;
    border-radius: 12px;
    display: inline-block;
}

/* Бутон - ПОПРАВЕН */


/* По-голям бутон за добавяне в кошницата - ПОПРАВЕН */
.add-to-cart-btn-esoteric-large {
      background: #6b3d9a;
      color: white;
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

.add-to-cart-btn-esoteric-large:hover {
    background: #6b3d9a;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(230, 0, 0, 0.2);
}

/* Пагинация */
.pagination-section-esoteric {
    margin-top: 40px;
    text-align: center;
}

.pagination-esoteric {
    display: flex;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.pagination-esoteric a {
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

.pagination-esoteric a:hover {
    background: #7d4caf;
    color: white;
    border-color: #7d4caf;
}

.pagination-esoteric a.active-page {
    background: #7d4caf;
    color: white;
    border-color: #7d4caf;
    font-weight: bold;
}

.page-info-esoteric {
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
.empty-state-esoteric {
    text-align: center;
    padding: 50px 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 30px 0;
}

.empty-state-esoteric p {
    color: #666;
    font-size: 16px;
    margin-bottom: 20px;
}

.empty-state-esoteric a {
    color: #7d4caf;
    text-decoration: none;
    font-weight: bold;
    border: 2px solid #7d4caf;
    padding: 10px 20px;
    border-radius: 6px;
    display: inline-block;
    transition: all 0.3s;
}

.empty-state-esoteric a:hover {
    background: #7d4caf;
    color: white;
}

/* Навигационни бутони */
.page-nav-btn-esoteric {
    display: inline-block;
    margin: 0 10px;
    padding: 8px 20px;
    background: #7d4caf;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: all 0.3s;
    border: 2px solid #7d4caf;
}

.page-nav-btn-esoteric:hover {
    background: white;
    color: #7d4caf;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(125, 76, 175, 0.2);
}

/* Адаптивен дизайн */
@media (max-width: 1400px) {
    .books-grid-esoteric {
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    
    .book-card-esoteric {
        max-width: 260px;
        height: 430px;
    }
}

@media (max-width: 1200px) {
    .books-grid-esoteric {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .book-card-esoteric {
        max-width: 280px;
        height: 440px;
    }
    
    .book-title-esoteric {
        min-height: 56px;
    }
    
    .book-author-esoteric {
        min-height: 40px;
    }
}

@media (max-width: 992px) {
    .books-grid-esoteric {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .book-card-esoteric {
        max-width: 300px;
        height: 450px;
    }
    
    .book-title-esoteric {
        min-height: 60px;
    }
    
    .book-author-esoteric {
        min-height: 44px;
    }
}

@media (max-width: 768px) {
    .page-content-esoteric {
        padding: 15px;
    }
    
    .books-grid-esoteric {
        grid-template-columns: 1fr;
        max-width: 350px;
        margin: 0 auto 30px;
    }
    
    .book-card-esoteric {
        max-width: 350px;
        height: 430px;
    }
    
    .book-image-esoteric {
        height: 150px;
    }
    
    .book-image-esoteric img {
        max-height: 150px;
    }
    
    .book-title-esoteric {
        min-height: 56px;
    }
    
    .book-author-esoteric {
        min-height: 40px;
    }
}

/* Стилове за действия с книгата - Езотерика */
.book-actions-esoteric {
    display: flex;
    gap: 10px;
    margin-top: auto;
    margin-bottom: 5px;
}

.book-actions-esoteric .add-to-cart-btn-esoteric-large {
    flex: 1;
    margin-top: 0;
    padding: 12px 0;
}

/* Бутон за любими за езотерика */
.add-to-wishlist-btn-esoteric {
    background: transparent;
    border: 2px solid #7d4caf; /* Лилав цвят като бутона за кошница */
    color: #7d4caf;
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

.add-to-wishlist-btn-esoteric:hover {
    background: #7d4caf;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(125, 76, 175, 0.2);
}

.add-to-wishlist-btn-esoteric.added {
    background: #7d4caf;
    color: white;
    border-color: #7d4caf;
}

.add-to-wishlist-btn-esoteric.added i {
    content: "\f004" !important; /* Пълно сърце */
}

/* Увеличи малко височината на картата */
.book-card-esoteric {
    height: 440px; /* Увеличена височина */
}

/* Намали малко височината на изображението */
.book-image-esoteric {
    height: 150px;
}

/* Коригирай позиционирането на текста */
.book-title-esoteric {
    min-height: 45px;
}

.book-author-esoteric {
    margin-bottom: 8px;
}

/* Адаптивност за мобилни устройства */
@media (max-width: 768px) {
    .book-card-esoteric {
        height: 420px;
    }
    
    .book-actions-esoteric {
        gap: 8px;
    }
    
    .add-to-wishlist-btn-esoteric {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}

</style>

<div class="page-content-esoteric">
    <h1>Езотерика и духовни учения</h1>
    
    <?php if ($total_books > 0): ?>
    <div class="page-info-esoteric">
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
        // Промяна тук: Взимаме книги, където is_ezoteric = 1
        $sql = "
            SELECT * FROM books 
            WHERE is_ezoteric = 1 
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
    
    <div class="books-grid-esoteric">
        <?php 
        $counter = $offset + 1;
        while ($book = $result->fetch_assoc()): 
        ?>
        <div class="book-card-esoteric">
            <div class="book-image-esoteric">
                <img src="<?= htmlspecialchars($book['image']) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>"
                     onerror="this.src='https://via.placeholder.com/120x160/f0e6ff/7d4caf?text=Езотерика'">
            </div>
            
            <div class="book-title-esoteric">
                <h3>
                    <a href="book.php?id=<?= $book['id'] ?>" 
                       class="book-title-link-esoteric">
                        <?= htmlspecialchars($book['title']) ?>
                    </a>
                </h3>
            </div>
            
            <div class="book-author-esoteric">
                <a href="author_books.php?author=<?= urlencode($book['author']) ?>" 
                   class="author-link-esoteric">
                    <?= htmlspecialchars($book['author']) ?>
                </a>
            </div>
            
            <div class="price-sales-esoteric">
                <span class="book-price-esoteric"><?= number_format($book['price'], 2) ?> лв.</span>
                <span class="book-sales-esoteric"><?= $book['sales'] ?> продажби</span>
            </div>
            
           <!-- Бутони за действия с книгата -->
<div class="book-actions-esoteric">
    <button class="add-to-cart-btn-esoteric-large" 
            onclick="addToCartEsoteric(<?= $book['id'] ?>, this)">
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
    <button class="add-to-wishlist-btn-esoteric <?= $is_in_wishlist ? 'added' : '' ?>" 
            onclick="addToWishlistEsoteric(<?= $book['id'] ?>, this)">
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
    <div class="pagination-section-esoteric">
        <div class="pagination-esoteric">
            <?php if ($current_page > 1): ?>
            <a href="esoteric.php?page=1">« Първа</a>
            <?php endif; ?>
            
            <?php if ($current_page > 1): ?>
            <a href="esoteric.php?page=<?= $current_page - 1 ?>">‹</a>
            <?php endif; ?>
            
            <?php 
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="esoteric.php?page=<?= $i ?>" 
               class="<?= $i == $current_page ? 'active-page' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="esoteric.php?page=<?= $current_page + 1 ?>">›</a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="esoteric.php?page=<?= $total_pages ?>">Последна »</a>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <?php if ($current_page > 1): ?>
            <a href="esoteric.php?page=<?= $current_page - 1 ?>" class="page-nav-btn-esoteric">
                ← Предишна страница
            </a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="esoteric.php?page=<?= $current_page + 1 ?>" class="page-nav-btn-esoteric">
                Следваща страница →
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="empty-state-esoteric">
        <p>Все още няма книги в категория "Езотерика и духовни учения".</p>
        <p>Моля, върнете се по-късно или разгледайте нашите <a href="top_books.php">топ заглавия</a>.</p>
        <a href="index.php">← Върни се към началната страница</a>
    </div>
    <?php endif; ?>
</div>

<script>
// ФУНКЦИЯ ЗА ДОБАВЯНЕ В КОШНИЦАТА ЗА ЕЗОТЕРИКА СТРАНИЦАТА
function addToCartEsoteric(productId, button) {
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
            
            showEsotericMessage('✓ Книгата е добавена в кошницата!', 'success');
        } else {
            showEsotericMessage('✗ ' + (data.message || 'Грешка при добавяне'), 'error');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        button.innerHTML = originalText;
        button.disabled = false;
        button.style.backgroundColor = originalColor;
        showEsotericMessage('✗ Възникна грешка', 'error');
    });
}

// Функция за съобщения в езотерика страницата
function showEsotericMessage(text, type) {
    let msg = document.querySelector('.esoteric-message');
    if (msg) msg.remove();
    
    msg = document.createElement('div');
    msg.className = 'esoteric-message';
    msg.textContent = text;
    msg.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#7d4caf' : '#f44336'};
        color: white;
        border-radius: 6px;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideInEsoteric 0.3s ease;
        font-family: Arial, sans-serif;
        max-width: 300px;
    `;
    
    document.body.appendChild(msg);
    
    setTimeout(() => {
        msg.style.animation = 'slideOutEsoteric 0.3s ease';
        setTimeout(() => msg.remove(), 300);
    }, 3000);
}

// Добавяне на CSS анимации за езотерика
if (!document.querySelector('#esoteric-msg-animations')) {
    const style = document.createElement('style');
    style.id = 'esoteric-msg-animations';
    style.textContent = `
        @keyframes slideInEsoteric {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutEsoteric {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
}

// ФУНКЦИЯ ЗА ДОБАВЯНЕ В ЛЮБИМИ (Езотерика)
function addToWishlistEsoteric(bookId, button) {
    if (!button) return;
    
    // Проверка дали потребителят е влязъл
    <?php if (!isset($_SESSION['user_id'])): ?>
        showEsotericMessage('Моля, влезте в профила си за да добавите в любими!', 'info');
        return;
    <?php endif; ?>
    
    const isAdded = button.classList.contains('added');
    const originalIcon = button.innerHTML;
    
    if (isAdded) {
        // Премахване от любими
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        removeFromWishlistEsoteric(bookId, button, originalIcon);
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
                showEsotericMessage('✓ Книгата е добавена в любими!', 'success');
                
                // Обновяване на брояча за любими (ако има такъв)
                const wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    let current = parseInt(wishlistCount.textContent) || 0;
                    wishlistCount.textContent = current + 1;
                }
            } else {
                button.innerHTML = originalIcon;
                showEsotericMessage('✗ ' + (data.message || 'Грешка при добавяне'), 'error');
            }
        })
        .catch(error => {
            console.error('Грешка:', error);
            button.disabled = false;
            button.innerHTML = originalIcon;
            showEsotericMessage('✗ Възникна грешка', 'error');
        });
    }
}

// ФУНКЦИЯ ЗА ПРЕМАХВАНЕ ОТ ЛЮБИМИ (Езотерика)
function removeFromWishlistEsoteric(bookId, button, originalIcon) {
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
            showEsotericMessage('✓ Книгата е премахната от любими', 'success');
            
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
            showEsotericMessage('✗ ' + (data.message || 'Грешка при премахване'), 'error');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        button.disabled = false;
        button.innerHTML = originalIcon;
        showEsotericMessage('✗ Възникна грешка', 'error');
    });
}

// ФУНКЦИЯ ЗА ПРОВЕРКА ДАЛИ КНИГА Е В ЛЮБИМИ (за Езотерика)
function checkWishlistStatusEsoteric() {
    <?php if (isset($_SESSION['user_id'])): ?>
    const bookIds = [];
    document.querySelectorAll('.add-to-wishlist-btn-esoteric').forEach(btn => {
        const match = btn.getAttribute('onclick')?.match(/addToWishlistEsoteric\((\d+)/);
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
                    const btn = document.querySelector(`[onclick*="addToWishlistEsoteric(${bookId}"]`);
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

// Промени функцията showEsotericMessage да поддържа 'info' тип
function showEsotericMessage(text, type) {
    let msg = document.querySelector('.esoteric-message');
    if (msg) msg.remove();
    
    let bgColor;
    if (type === 'success') {
        bgColor = '#7d4caf'; /* Лилав за езотерика */
    } else if (type === 'error') {
        bgColor = '#f44336'; /* Червен за грешки */
    } else if (type === 'info') {
        bgColor = '#2196F3'; /* Син за информация */
    } else {
        bgColor = '#7d4caf'; /* По подразбиране лилав */
    }
    
    msg = document.createElement('div');
    msg.className = 'esoteric-message';
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
        animation: slideInEsoteric 0.3s ease;
        font-family: Arial, sans-serif;
        max-width: 300px;
    `;
    
    document.body.appendChild(msg);
    
    setTimeout(() => {
        msg.style.animation = 'slideOutEsoteric 0.3s ease';
        setTimeout(() => msg.remove(), 300);
    }, 3000);
}

// Извикваме функцията при зареждане на страницата
document.addEventListener('DOMContentLoaded', function() {
    checkWishlistStatusEsoteric();
});

</script>
<?php 
if (isset($stmt)) {
    $stmt->close();
}
if (isset($total_stmt)) {
    $total_stmt->close();
}
include 'footer.php'; 
?>