<?php
include 'config.php';
$page_title = 'Справочници - Бързи справки';
include 'header.php';

// Брой книги на страница
$books_per_page = 50;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $books_per_page;

// Вземане на общия брой книги за справочници
$total_stmt = $conn->prepare("SELECT COUNT(*) as total FROM books WHERE is_reference = 1");
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
/* Стилове за Справочници - КОРАЛОВ цвят (#FF7F50) */
.page-content-reference {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-content-reference h1 {
    color: #333;
    padding-bottom: 15px;
    margin-bottom: 30px;
    font-size: 32px;
    font-weight: 600;
    border-bottom: 3px solid #FF7F50;
    text-align: center;
}

/* Инфо панел за категории */
.info-panel-reference {
    background: #FFE4E1;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 25px;
    border-left: 4px solid #FF7F50;
    font-size: 14px;
}

.info-panel-reference p {
    margin: 0 0 5px 0;
    color: #FF4500;
}

.info-panel-reference strong {
    font-weight: 600;
}

/* Мрежа от книги - 5 колони */
.books-grid-reference {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 40px;
    justify-items: center;
}

/* Картичка за книга */
.book-card-reference {
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

.book-card-reference:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: #FF7F50;
}

/* Снимка */
.book-image-reference {
    text-align: center;
    margin-bottom: 15px;
    flex-shrink: 0;
    height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.book-image-reference img {
    max-height: 140px;
    max-width: 120px;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* Заглавие - ОГРАНИЧЕНО ДО 2 РЕДА */
.book-title-reference {
    margin-bottom: 8px;
    min-height: 40px;
    display: flex;
    align-items: flex-start;
}

.book-title-link-reference {
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
    width: 100%;
}

.book-title-link-reference:hover {
    color: #FF7F50;
    text-decoration: underline;
}

/* Автор */
.book-author-reference {
    color: #666;
      margin: 0 0 8px 0; /* Увеличен марджин */
    font-size: 13px;
    font-style: italic;
    height: 18px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

.author-link-reference {
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

.author-link-reference:hover {
    color: #FF7F50;
    text-decoration: underline;
}

/* Цена и продажби */
.price-sales-reference {
    margin-bottom: 15px;
    min-height: 45px;
}

.book-price-reference {
    color: #FF7F50;
    font-weight: bold;
    font-size: 17px;
    margin-bottom: 4px;
}

.book-sales-reference {
    color: #666;
    font-size: 12px;
    background: #f5f5f5;
    padding: 4px 10px;
    border-radius: 12px;
    display: inline-block;
}

/* Стилове за действия с книгата - Справочници */
.book-actions-reference {
    display: flex;
    gap: 10px;
    margin-top: auto;
    margin-bottom: 5px;
}

/* Бутон за кошница за справочници */
.add-to-cart-btn-reference {
    background: #FF7F50; /* Коралов цвят за справочници */
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
    min-height: 45px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 1;
    margin-top: 0;
}

.add-to-cart-btn-reference:hover {
    background: #FF6347; /* По-тъмен коралов при hover */
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(255, 127, 80, 0.2);
}

.add-to-cart-btn-reference:disabled {
    background: #cccccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Бутон за любими за справочници */
.add-to-wishlist-btn-reference {
    background: transparent;
    border: 2px solid #FF7F50; /* Коралов цвят като заглавието */
    color: #FF7F50;
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

.add-to-wishlist-btn-reference:hover {
    background: #FF7F50;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 127, 80, 0.2);
}

.add-to-wishlist-btn-reference.added {
    background: #FF7F50;
    color: white;
    border-color: #FF7F50;
}

.add-to-wishlist-btn-reference.added i {
    content: "\f004" !important; /* Пълно сърце */
}

/* Пагинация */
.pagination-section-reference {
    margin-top: 40px;
    text-align: center;
}

.pagination-reference {
    display: flex;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.pagination-reference a {
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

.pagination-reference a:hover {
    background: #FF7F50;
    color: white;
    border-color: #FF7F50;
}

.pagination-reference a.active-page {
    background: #FF7F50;
    color: white;
    border-color: #FF7F50;
    font-weight: bold;
}

.page-info-reference {
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
.empty-state-reference {
    text-align: center;
    padding: 50px 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 30px 0;
}

.empty-state-reference p {
    color: #666;
    font-size: 16px;
    margin-bottom: 20px;
}

.empty-state-reference a {
    color: #FF7F50;
    text-decoration: none;
    font-weight: bold;
    border: 2px solid #FF7F50;
    padding: 10px 20px;
    border-radius: 6px;
    display: inline-block;
    transition: all 0.3s;
}

.empty-state-reference a:hover {
    background: #FF7F50;
    color: white;
}

/* Навигация между страници */
.page-nav-btn-reference {
    display: inline-block;
    margin: 0 10px;
    padding: 8px 20px;
    background: #FF7F50;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: all 0.3s;
    border: 2px solid #FF7F50;
}

.page-nav-btn-reference:hover {
    background: white;
    color: #FF7F50;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(255, 127, 80, 0.2);
}

/* Адаптивен дизайн */
@media (max-width: 1400px) {
    .books-grid-reference {
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    
    .book-card-reference {
        max-width: 260px;
        height: 410px;
    }
    
    .book-image-reference {
        height: 135px;
    }
    
    .book-title-reference {
        min-height: 44px;
    }
}

@media (max-width: 1200px) {
    .books-grid-reference {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .book-card-reference {
        max-width: 280px;
        height: 420px;
    }
    
    .book-title-reference {
        min-height: 48px;
    }
    
    .book-author-reference {
        min-height: 40px;
    }
    
    .book-image-reference {
        height: 130px;
    }
}

@media (max-width: 992px) {
    .books-grid-reference {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .book-card-reference {
        max-width: 300px;
        height: 430px;
    }
    
    .book-title-reference {
        min-height: 52px;
    }
    
    .book-author-reference {
        min-height: 44px;
    }
    
    .book-image-reference {
        height: 125px;
    }
}

@media (max-width: 768px) {
    .page-content-reference {
        padding: 15px;
    }
    
    .books-grid-reference {
        grid-template-columns: 1fr;
        max-width: 350px;
        margin: 0 auto 30px;
    }
    
    .book-card-reference {
        max-width: 350px;
        height: 390px;
    }
    
    .book-image-reference {
        height: 120px;
    }
    
    .book-image-reference img {
        max-height: 120px;
    }
    
    .book-title-reference {
        min-height: 48px;
    }
    
    .book-author-reference {
        min-height: 40px;
    }
    
    .book-actions-reference {
        gap: 8px;
    }
    
    .add-to-wishlist-btn-reference {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}
</style>

<div class="page-content-reference">
    <h1>Справочници - Бързи справки</h1>
    

    
    <?php if ($total_books > 0): ?>
    <div class="page-info-reference">
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
        // Вземане на книги за справочници
        $sql = "
            SELECT * FROM books 
            WHERE is_reference = 1 
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
    
    <div class="books-grid-reference">
        <?php 
        $counter = $offset + 1;
        while ($book = $result->fetch_assoc()): 
        ?>
        <div class="book-card-reference">
            <div class="book-image-reference">
                <img src="<?= htmlspecialchars($book['image']) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>"
                     onerror="this.src='https://via.placeholder.com/120x160/FFE4E1/FF7F50?text=Справочник'">
            </div>
            
            <div class="book-title-reference">
                <h3>
                    <a href="book.php?id=<?= $book['id'] ?>" 
                       class="book-title-link-reference">
                        <?= htmlspecialchars($book['title']) ?>
                    </a>
                </h3>
            </div>
            
            <div class="book-author-reference">
                <a href="author_books.php?author=<?= urlencode($book['author']) ?>" 
                   class="author-link-reference">
                    <?= htmlspecialchars($book['author']) ?>
                </a>
            </div>
            
            <div class="price-sales-reference">
                <span class="book-price-reference"><?= number_format($book['price'], 2) ?> лв.</span>
                <span class="book-sales-reference"><?= $book['sales'] ?> продажби</span>
            </div>
            
          
<div class="book-actions-reference">
    <button class="add-to-cart-btn-reference" 
            onclick="addToCartNew(<?= $book['id'] ?>, this, 'reference')">
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
    <button class="add-to-wishlist-btn-reference <?= $is_in_wishlist ? 'added' : '' ?>" 
            onclick="addToWishlistReference(<?= $book['id'] ?>, this)">
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
    <div class="pagination-section-reference">
        <div class="pagination-reference">
            <?php if ($current_page > 1): ?>
            <a href="reference.php?page=1">« Първа</a>
            <?php endif; ?>
            
            <?php if ($current_page > 1): ?>
            <a href="reference.php?page=<?= $current_page - 1 ?>">‹</a>
            <?php endif; ?>
            
            <?php 
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="reference.php?page=<?= $i ?>" 
               class="<?= $i == $current_page ? 'active-page' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="reference.php?page=<?= $current_page + 1 ?>">›</a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="reference.php?page=<?= $total_pages ?>">Последна »</a>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <?php if ($current_page > 1): ?>
            <a href="reference.php?page=<?= $current_page - 1 ?>" class="page-nav-btn-reference">
                ← Предишна страница
            </a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="reference.php?page=<?= $current_page + 1 ?>" class="page-nav-btn-reference">
                Следваща страница →
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="empty-state-reference">
        <p>Все още няма налични справочници.</p>
        <p>Моля, върнете се по-късно или разгледайте нашите <a href="top_books.php">топ заглавия</a>.</p>
        <a href="index.php">← Върни се към началната страница</a>
    </div>
    <?php endif; ?>
</div>

<script>
// ФУНКЦИЯ ЗА ДОБАВЯНЕ В КОШНИЦАТА - за справочници
function addToCartReference(productId, button) {
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
            
            showSimpleMessage('✓ Книгата е добавена в кошницата!', 'success', 'reference');
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

// Функция за прости съобщения с цвят за справочници
function showSimpleMessage(text, type, pageType = 'default') {
    let msg = document.querySelector('.simple-message');
    if (msg) msg.remove();
    
    // Определяне на цвета според типа на страницата
    let bgColor;
    if (type === 'success') {
        if (pageType === 'reference') {
            bgColor = '#FF7F50'; // КОРАЛОВ за справочници
        } else if (pageType === 'dictionaries') {
            bgColor = '#FF9800'; // Оранжев за речници
        } else if (pageType === 'psychology') {
            bgColor = '#8e24aa'; // Виолетово за психология
        } else if (pageType === 'science') {
            bgColor = '#303f9f'; // Тъмен син за наука
        } else if (pageType === 'teen') {
            bgColor = '#ec407a'; // Розов за тийнеджъри
        } else if (pageType === 'history') {
            bgColor = '#795548'; // Кафяв за история
        } else if (pageType === 'economics') {
            bgColor = '#388e3c'; // Зелен за икономика
        } else if (pageType === 'art') {
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

// ФУНКЦИЯ ЗА ДОБАВЯНЕ В ЛЮБИМИ (Справочници)
function addToWishlistReference(bookId, button) {
    if (!button) return;
    
    // Проверка дали потребителят е влязъл
    <?php if (!isset($_SESSION['user_id'])): ?>
        showSimpleMessage('Моля, влезте в профила си за да добавите в любими!', 'info', 'reference');
        return;
    <?php endif; ?>
    
    const isAdded = button.classList.contains('added');
    const originalIcon = button.innerHTML;
    
    if (isAdded) {
        // Премахване от любими
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        removeFromWishlistReference(bookId, button, originalIcon);
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
                showSimpleMessage('✓ Книгата е добавена в любими!', 'success', 'reference');
                
                // Обновяване на брояча за любими (ако има такъв)
                const wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    let current = parseInt(wishlistCount.textContent) || 0;
                    wishlistCount.textContent = current + 1;
                }
            } else {
                button.innerHTML = originalIcon;
                showSimpleMessage('✗ ' + (data.message || 'Грешка при добавяне'), 'error', 'reference');
            }
        })
        .catch(error => {
            console.error('Грешка:', error);
            button.disabled = false;
            button.innerHTML = originalIcon;
            showSimpleMessage('✗ Възникна грешка', 'error', 'reference');
        });
    }
}

// ФУНКЦИЯ ЗА ПРЕМАХВАНЕ ОТ ЛЮБИМИ (Справочници)
function removeFromWishlistReference(bookId, button, originalIcon) {
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
            showSimpleMessage('✓ Книгата е премахната от любими', 'success', 'reference');
            
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
            showSimpleMessage('✗ ' + (data.message || 'Грешка при премахване'), 'error', 'reference');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        button.disabled = false;
        button.innerHTML = originalIcon;
        showSimpleMessage('✗ Възникна грешка', 'error', 'reference');
    });
}

// ФУНКЦИЯ ЗА ПРОВЕРКА ДАЛИ КНИГА Е В ЛЮБИМИ (за Справочници)
function checkWishlistStatusReference() {
    <?php if (isset($_SESSION['user_id'])): ?>
    const bookIds = [];
    document.querySelectorAll('.add-to-wishlist-btn-reference').forEach(btn => {
        const match = btn.getAttribute('onclick')?.match(/addToWishlistReference\((\d+)/);
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
                    const btn = document.querySelector(`[onclick*="addToWishlistReference(${bookId}"]`);
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

// Промени функцията за добавяне в кошница
function addToCartNew(productId, button, pageType = 'reference') {
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
            
            showSimpleMessage('✓ Книгата е добавена в кошницата!', 'success', pageType);
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

// Добавете 'reference' към функцията showSimpleMessage
// Това вече съществува, просто добавете 'reference' към списъка:
// if (pageType === 'reference') {
//     bgColor = '#FF7F50'; // Коралов за справочници
// }

// Извикваме функцията при зареждане на страницата
document.addEventListener('DOMContentLoaded', function() {
    checkWishlistStatusReference();
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