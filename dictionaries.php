<?php
include 'config.php';
$page_title = 'Речници и разговорници - Езици и превод';
include 'header.php';

// Брой книги на страница
$books_per_page = 50;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $books_per_page;

// Вземане на общия брой книги за речници и разговорници
$total_stmt = $conn->prepare("SELECT COUNT(*) as total FROM books WHERE is_dictionaries = 1");
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
/* Стилове за Речници и разговорници - ОРАНЖЕВ цвят */
.page-content-dictionaries {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-content-dictionaries h1 {
    color: #333;
    padding-bottom: 15px;
    margin-bottom: 30px;
    font-size: 32px;
    font-weight: 600;
    border-bottom: 3px solid #FF9800;
    text-align: center;
}

/* Инфо панел за категории */
.info-panel-dictionaries {
    background: #FFF3E0;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 25px;
    border-left: 4px solid #FF9800;
    font-size: 14px;
}

.info-panel-dictionaries p {
    margin: 0 0 5px 0;
    color: #EF6C00;
}

.info-panel-dictionaries strong {
    font-weight: 600;
}

/* Мрежа от книги - 5 колони */
.books-grid-dictionaries {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 40px;
    justify-items: center;
}

/* Картичка за книга */
.book-card-dictionaries {
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

.book-card-dictionaries:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: #FF9800;
}

/* Снимка */
.book-image-dictionaries {
    text-align: center;
    margin-bottom: 15px;
    flex-shrink: 0;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.book-image-dictionaries img {
    max-height: 160px;
    max-width: 120px;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* Заглавие - ОГРАНИЧЕНО ДО 2 РЕДА */
.book-title-dictionaries {
    margin-bottom: 10px;
    min-height: 40px;
    display: flex;
    align-items: flex-start;
}

.book-title-link-dictionaries {
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

.book-title-link-dictionaries:hover {
    color: #FF9800;
    text-decoration: underline;
}

/* Автор */
.book-author-dictionaries {
    color: #666;
    margin: 8px 0 12px 0;
    font-size: 13px;
    font-style: italic;
    min-height: 36px;
    display: flex;
    align-items: flex-start;
}

.author-link-dictionaries {
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

.author-link-dictionaries:hover {
    color: #FF9800;
    text-decoration: underline;
}

/* Цена и продажби */
.price-sales-dictionaries {
    margin-bottom: 15px;
    min-height: 45px;
}

.book-price-dictionaries {
    color: #FF9800;
    font-weight: bold;
    font-size: 17px;
    margin-bottom: 4px;
}

.book-sales-dictionaries {
    color: #666;
    font-size: 12px;
    background: #f5f5f5;
    padding: 4px 10px;
    border-radius: 12px;
    display: inline-block;
}

/* Стилове за действия с книгата - Речници */
.book-actions-dictionaries {
    display: flex;
    gap: 10px;
    margin-top: auto;
    margin-bottom: 5px;
}

/* Бутон за кошница за речници */
.add-to-cart-btn-dictionaries {
    background: #FF9800; /* Оранжев цвят за речници */
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

.add-to-cart-btn-dictionaries:hover {
    background: #F57C00; /* По-тъмен оранжев при hover */
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(255, 152, 0, 0.2);
}

.add-to-cart-btn-dictionaries:disabled {
    background: #cccccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Бутон за любими за речници - КАКТО В ДЕТСКАТА ЛИТЕРАТУРА */
.add-to-wishlist-btn-dictionaries {
    background: transparent;
    border: 2px solid #FF9800; /* Оранжев цвят като заглавието */
    color: #FF9800;
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

.add-to-wishlist-btn-dictionaries:hover {
    background: #FF9800;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 152, 0, 0.2);
}

.add-to-wishlist-btn-dictionaries.added {
    background: #FF9800;
    color: white;
    border-color: #FF9800;
}

.add-to-wishlist-btn-dictionaries.added i {
    content: "\f004" !important; /* Пълно сърце */
}

/* Увеличи височината на картата за да има място за двата бутона */
.book-card-dictionaries {
    height: 400px; /* Увеличена височина за да има място за двата бутона */
}

/* Намали малко височината на изображението */
.book-image-dictionaries {
    height: 140px; /* Същата или малко по-ниска височина */
}

/* Коригирай позиционирането на текста */
.book-title-dictionaries {
    margin-bottom: 8px;
    min-height: 40px; /* Минимална височина за 2 реда */
}

/* Пагинация */
.pagination-section-dictionaries {
    margin-top: 40px;
    text-align: center;
}

.pagination-dictionaries {
    display: flex;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.pagination-dictionaries a {
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

.pagination-dictionaries a:hover {
    background: #FF9800;
    color: white;
    border-color: #FF9800;
}

.pagination-dictionaries a.active-page {
    background: #FF9800;
    color: white;
    border-color: #FF9800;
    font-weight: bold;
}

.page-info-dictionaries {
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
.empty-state-dictionaries {
    text-align: center;
    padding: 50px 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 30px 0;
}

.empty-state-dictionaries p {
    color: #666;
    font-size: 16px;
    margin-bottom: 20px;
}

.empty-state-dictionaries a {
    color: #FF9800;
    text-decoration: none;
    font-weight: bold;
    border: 2px solid #FF9800;
    padding: 10px 20px;
    border-radius: 6px;
    display: inline-block;
    transition: all 0.3s;
}

.empty-state-dictionaries a:hover {
    background: #FF9800;
    color: white;
}

/* Навигация между страници */
.page-nav-btn-dictionaries {
    display: inline-block;
    margin: 0 10px;
    padding: 8px 20px;
    background: #FF9800;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: all 0.3s;
    border: 2px solid #FF9800;
}

.page-nav-btn-dictionaries:hover {
    background: white;
    color: #FF9800;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(255, 152, 0, 0.2);
}

/* Адаптивен дизайн */
@media (max-width: 1400px) {
    .books-grid-dictionaries {
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    
    .book-card-dictionaries {
        max-width: 260px;
        height: 410px;
    }
    
    .book-title-dictionaries {
        min-height: 44px;
    }
    
    .book-image-dictionaries {
        height: 135px;
    }
}

@media (max-width: 1200px) {
    .books-grid-dictionaries {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .book-card-dictionaries {
        max-width: 280px;
        height: 420px;
    }
    
    .book-title-dictionaries {
        min-height: 48px;
    }
    
    .book-author-dictionaries {
        min-height: 40px;
    }
    
    .book-image-dictionaries {
        height: 130px;
    }
}

@media (max-width: 992px) {
    .books-grid-dictionaries {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .book-card-dictionaries {
        max-width: 300px;
        height: 430px;
    }
    
    .book-title-dictionaries {
        min-height: 52px;
    }
    
    .book-author-dictionaries {
        min-height: 44px;
    }
    
    .book-image-dictionaries {
        height: 125px;
    }
}

@media (max-width: 768px) {
    .page-content-dictionaries {
        padding: 15px;
    }
    
    .books-grid-dictionaries {
        grid-template-columns: 1fr;
        max-width: 350px;
        margin: 0 auto 30px;
    }
    
    .book-card-dictionaries {
        max-width: 350px;
        height: 390px;
    }
    
    .book-image-dictionaries {
        height: 120px;
    }
    
    .book-image-dictionaries img {
        max-height: 120px;
    }
    
    .book-title-dictionaries {
        min-height: 48px;
    }
    
    .book-author-dictionaries {
        min-height: 40px;
    }
    
    .book-actions-dictionaries {
        gap: 8px;
    }
    
    .add-to-wishlist-btn-dictionaries {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}

</style>

<div class="page-content-dictionaries">
    <h1>Речници и разговорници</h1>
    
    <?php if ($total_books > 0): ?>
    <div class="page-info-dictionaries">
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
        // Вземане на книги за речници и разговорници
        $sql = "
            SELECT * FROM books 
            WHERE is_dictionaries = 1 
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
    
    <div class="books-grid-dictionaries">
        <?php 
        $counter = $offset + 1;
        while ($book = $result->fetch_assoc()): 
        ?>
        <div class="book-card-dictionaries">
            <div class="book-image-dictionaries">
                <img src="<?= htmlspecialchars($book['image']) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>"
                     onerror="this.src='https://via.placeholder.com/120x160/FFF3E0/FF9800?text=Речник'">
            </div>
            
            <div class="book-title-dictionaries">
                <h3>
                    <a href="book.php?id=<?= $book['id'] ?>" 
                       class="book-title-link-dictionaries">
                        <?= htmlspecialchars($book['title']) ?>
                    </a>
                </h3>
            </div>
            
            <div class="book-author-dictionaries">
                <a href="author_books.php?author=<?= urlencode($book['author']) ?>" 
                   class="author-link-dictionaries">
                    <?= htmlspecialchars($book['author']) ?>
                </a>
            </div>
            
            <div class="price-sales-dictionaries">
                <span class="book-price-dictionaries"><?= number_format($book['price'], 2) ?> лв.</span>
                <span class="book-sales-dictionaries"><?= $book['sales'] ?> продажби</span>
            </div>
            
            <!-- Бутон за добавяне в кошницата и любими -->
            <div class="book-actions-dictionaries">
                <button class="add-to-cart-btn-dictionaries" 
                        onclick="addToCartNew(<?= $book['id'] ?>, this, 'dictionaries')">
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
                <button class="add-to-wishlist-btn-dictionaries <?= $is_in_wishlist ? 'added' : '' ?>" 
                        onclick="addToWishlistDictionaries(<?= $book['id'] ?>, this)">
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
    <div class="pagination-section-dictionaries">
        <div class="pagination-dictionaries">
            <?php if ($current_page > 1): ?>
            <a href="dictionaries.php?page=1">« Първа</a>
            <?php endif; ?>
            
            <?php if ($current_page > 1): ?>
            <a href="dictionaries.php?page=<?= $current_page - 1 ?>">‹</a>
            <?php endif; ?>
            
            <?php 
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="dictionaries.php?page=<?= $i ?>" 
               class="<?= $i == $current_page ? 'active-page' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="dictionaries.php?page=<?= $current_page + 1 ?>">›</a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="dictionaries.php?page=<?= $total_pages ?>">Последна »</a>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <?php if ($current_page > 1): ?>
            <a href="dictionaries.php?page=<?= $current_page - 1 ?>" class="page-nav-btn-dictionaries">
                ← Предишна страница
            </a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="dictionaries.php?page=<?= $current_page + 1 ?>" class="page-nav-btn-dictionaries">
                Следваща страница →
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="empty-state-dictionaries">
        <p>Все още няма налични речници и разговорници.</p>
        <p>Моля, върнете се по-късно или разгледайте нашите <a href="top_books.php">топ заглавия</a>.</p>
        <a href="index.php">← Върни се към началната страница</a>
    </div>
    <?php endif; ?>
</div>

<script>
// ФУНКЦИЯ ЗА ДОБАВЯНЕ В КОШНИЦАТА - за речници и разговорници
function addToCartNew(productId, button, pageType = 'dictionaries') {
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

// ФУНКЦИЯ ЗА ДОБАВЯНЕ В ЛЮБИМИ (Речници и разговорници)
function addToWishlistDictionaries(bookId, button) {
    if (!button) return;
    
    // Проверка дали потребителят е влязъл
    <?php if (!isset($_SESSION['user_id'])): ?>
        showSimpleMessage('Моля, влезте в профила си за да добавите в любими!', 'info', 'dictionaries');
        return;
    <?php endif; ?>
    
    const isAdded = button.classList.contains('added');
    const originalIcon = button.innerHTML;
    
    if (isAdded) {
        // Премахване от любими
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        removeFromWishlistDictionaries(bookId, button, originalIcon);
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
                showSimpleMessage('✓ Книгата е добавена в любими!', 'success', 'dictionaries');
                
                // Обновяване на брояча за любими (ако има такъв)
                const wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    let current = parseInt(wishlistCount.textContent) || 0;
                    wishlistCount.textContent = current + 1;
                }
            } else {
                button.innerHTML = originalIcon;
                showSimpleMessage('✗ ' + (data.message || 'Грешка при добавяне'), 'error', 'dictionaries');
            }
        })
        .catch(error => {
            console.error('Грешка:', error);
            button.disabled = false;
            button.innerHTML = originalIcon;
            showSimpleMessage('✗ Възникна грешка', 'error', 'dictionaries');
        });
    }
}

// ФУНКЦИЯ ЗА ПРЕМАХВАНЕ ОТ ЛЮБИМИ (Речници и разговорници)
function removeFromWishlistDictionaries(bookId, button, originalIcon) {
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
            showSimpleMessage('✓ Книгата е премахната от любими', 'success', 'dictionaries');
            
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
            showSimpleMessage('✗ ' + (data.message || 'Грешка при премахване'), 'error', 'dictionaries');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        button.disabled = false;
        button.innerHTML = originalIcon;
        showSimpleMessage('✗ Възникна грешка', 'error', 'dictionaries');
    });
}

// ФУНКЦИЯ ЗА ПРОВЕРКА ДАЛИ КНИГА Е В ЛЮБИМИ (за Речници)
function checkWishlistStatusDictionaries() {
    <?php if (isset($_SESSION['user_id'])): ?>
    const bookIds = [];
    document.querySelectorAll('.add-to-wishlist-btn-dictionaries').forEach(btn => {
        const match = btn.getAttribute('onclick')?.match(/addToWishlistDictionaries\((\d+)/);
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
                    const btn = document.querySelector(`[onclick*="addToWishlistDictionaries(${bookId}"]`);
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

// Функция за прости съобщения с цвят за речници
function showSimpleMessage(text, type, pageType = 'default') {
    let msg = document.querySelector('.simple-message');
    if (msg) msg.remove();
    
    // Определяне на цвета според типа на страницата
    let bgColor;
    if (type === 'success') {
        if (pageType === 'dictionaries') {
            bgColor = '#FF9800'; // ОРАНЖЕВ за речници
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
        } else if (pageType === 'children') {
            bgColor = '#FF6B6B'; // Розов за детска литература
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
    checkWishlistStatusDictionaries();
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