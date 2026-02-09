<?php
include 'config.php';
$page_title = 'Художествена литература - Романи, разкази, поезия';
include 'header.php';

// ФУНКЦИИ ЗА РАБОТА С ЖАНРОВЕТЕ
function getAllGenres($conn) {
    $genres = [];
    $sql = "SELECT id, name, slug, color, icon, description 
            FROM genres 
            ORDER BY sort_order, name";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $genres[] = $row;
        }
    }
    return $genres;
}

function getBookGenres($conn, $book_id) {
    $genres = [];
    $sql = "SELECT g.id, g.name, g.slug, g.color, g.icon
            FROM genres g
            JOIN book_genres bg ON g.id = bg.genre_id
            WHERE bg.book_id = ?
            ORDER BY g.sort_order, g.name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $genres[] = $row;
        }
    }
    $stmt->close();
    return $genres;
}

// Брой книги на страница
$books_per_page = 50;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;
$offset = ($current_page - 1) * $books_per_page;

// ВЗИМАНЕ НА ВСИЧКИ ЖАНРОВЕ
$all_genres = getAllGenres($conn);

// ФИЛТЪР ПО ЖАНР (по slug)
$selected_genre_slug = isset($_GET['genre']) ? $_GET['genre'] : '';
$selected_genre_id = 0;
$selected_genre_name = '';

if ($selected_genre_slug) {
    foreach ($all_genres as $genre) {
        if ($genre['slug'] == $selected_genre_slug) {
            $selected_genre_id = $genre['id'];
            $selected_genre_name = $genre['name'];
            break;
        }
    }
}

// ВЗИМАНЕ НА ОБЩИЯ БРОЙ КНИГИ (С ФИЛТЪР)
if ($selected_genre_id > 0) {
    // Книги с конкретен жанр
    $total_sql = "SELECT COUNT(DISTINCT b.id) as total 
                  FROM books b
                  JOIN book_genres bg ON b.id = bg.book_id
                  WHERE b.is_fiction = 1 AND bg.genre_id = ?";
    $total_stmt = $conn->prepare($total_sql);
    $total_stmt->bind_param("i", $selected_genre_id);
} else {
    // Всички художествени книги
    $total_sql = "SELECT COUNT(*) as total FROM books WHERE is_fiction = 1";
    $total_stmt = $conn->prepare($total_sql);
}

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
/* Стилове за Художествена литература - ПУРПУРЕН цвят (#9C27B0) */
.page-content-fiction {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-content-fiction h1 {
    color: #333;
    padding-bottom: 15px;
    margin-bottom: 30px;
    font-size: 32px;
    font-weight: 600;
    border-bottom: 3px solid #9C27B0;
    text-align: center;
}

/* Инфо панел за категории */
.info-panel-fiction {
    background: #F3E5F5;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 25px;
    border-left: 4px solid #9C27B0;
    font-size: 14px;
}

.info-panel-fiction p {
    margin: 0 0 5px 0;
    color: #7B1FA2;
}

.info-panel-fiction strong {
    font-weight: 600;
}

/* Жанрове - тагове */
.genre-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin: 15px 0;
}

.genre-tag {
    padding: 8px 14px;
    background: #E1BEE7;
    color: #4A148C;
    border-radius: 20px;
    font-size: 13px;
    text-decoration: none;
    transition: all 0.3s;
    border: 1px solid #CE93D8;
}

.genre-tag:hover {
    background: #9C27B0;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(156, 39, 176, 0.2);
    text-decoration: none;
}

/* Мрежа от книги - 5 колони */
.books-grid-fiction {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 40px;
    justify-items: center;
}

/* Картичка за книга */
.book-card-fiction {
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
    height: 430px;
}

.book-card-fiction:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: #9C27B0;
}

/* Стикър за жанр */
.genre-sticker {
    position: absolute;
    top: 10px;
    right: 10px;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: bold;
    z-index: 1;
    max-width: 80px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Допълнителни жанрове */
.additional-genres {
    position: absolute;
    top: 10px;
    left: 10px;
    background: rgba(0,0,0,0.7);
    color: white;
    padding: 3px 6px;
    border-radius: 3px;
    font-size: 10px;
    z-index: 1;
}

/* Снимка */
.book-image-fiction {
    text-align: center;
    margin-bottom: 15px;
    flex-shrink: 0;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.book-image-fiction img {
    max-height: 160px;
    max-width: 120px;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* Заглавие - ОГРАНИЧЕНО ДО 2 РЕДА */
.book-title-fiction {
    margin-bottom: 10px;
    min-height: 40px;
    display: flex;
    align-items: flex-start;
}

.book-title-link-fiction {
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

.book-title-link-fiction:hover {
    color: #9C27B0;
    text-decoration: underline;
}

/* Автор */
.book-author-fiction {
    color: #666;
    margin: 8px 0 12px 0;
    font-size: 13px;
    font-style: italic;
    min-height: 36px;
    display: flex;
    align-items: flex-start;
}

.author-link-fiction {
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

.author-link-fiction:hover {
    color: #9C27B0;
    text-decoration: underline;
}

/* Цена и продажби */
.price-sales-fiction {
    margin-bottom: 15px;
    min-height: 45px;
}

.book-price-fiction {
    color: #9C27B0;
    font-weight: bold;
    font-size: 17px;
    margin-bottom: 4px;
}

.book-sales-fiction {
    color: #666;
    font-size: 12px;
    background: #f5f5f5;
    padding: 4px 10px;
    border-radius: 12px;
    display: inline-block;
}

/* Бутон */
.add-to-cart-btn-fiction {
    background: #9C27B0;
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
}

.add-to-cart-btn-fiction:hover {
    background: #7B1FA2;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(156, 39, 176, 0.2);
}

/* Пагинация */
.pagination-section-fiction {
    margin-top: 40px;
    text-align: center;
}

.pagination-fiction {
    display: flex;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.pagination-fiction a {
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

.pagination-fiction a:hover {
    background: #9C27B0;
    color: white;
    border-color: #9C27B0;
}

.pagination-fiction a.active-page {
    background: #9C27B0;
    color: white;
    border-color: #9C27B0;
    font-weight: bold;
}

.page-info-fiction {
    text-align: center;
    margin-bottom: 25px;
    color: #666;
    font-size: 15px;
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

/* Филтър по жанр */
.genre-filter {
    background: #F8F9FA;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 25px;
    border: 1px solid #E0E0E0;
}

.genre-filter h3 {
    color: #333;
    margin-bottom: 10px;
    font-size: 16px;
}

.genre-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.genre-btn {
    padding: 10px 18px;
    background: white;
    color: #666;
    border: 1px solid #DDD;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 14px;
    border-left-width: 4px;
}

.genre-btn:hover {
    background: #F3E5F5;
    border-color: #9C27B0;
    transform: translateY(-2px);
}

.genre-btn.active {
    background: #9C27B0;
    color: white;
    border-color: #9C27B0;
}

/* Празен списък */
.empty-state-fiction {
    text-align: center;
    padding: 50px 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 30px 0;
}

.empty-state-fiction p {
    color: #666;
    font-size: 16px;
    margin-bottom: 20px;
}

.empty-state-fiction a {
    color: #9C27B0;
    text-decoration: none;
    font-weight: bold;
    border: 2px solid #9C27B0;
    padding: 10px 20px;
    border-radius: 6px;
    display: inline-block;
    transition: all 0.3s;
}

.empty-state-fiction a:hover {
    background: #9C27B0;
    color: white;
}

/* Навигация между страници */
.page-nav-btn-fiction {
    display: inline-block;
    margin: 0 10px;
    padding: 8px 20px;
    background: #9C27B0;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-weight: bold;
    transition: all 0.3s;
    border: 2px solid #9C27B0;
}

.page-nav-btn-fiction:hover {
    background: white;
    color: #9C27B0;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(156, 39, 176, 0.2);
}


/* Стилове за действия с книгата - Художествена литература */
.book-actions-fiction {
    display: flex;
    gap: 10px;
    margin-top: auto;
    margin-bottom: 5px;
}

/* Бутон за кошница за художествена литература */
.add-to-cart-btn-fiction {
    background: #9C27B0; /* Пурпурен цвят за художествена литература */
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

.add-to-cart-btn-fiction:hover {
    background: #7B1FA2; /* По-тъмен пурпурен при hover */
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(156, 39, 176, 0.2);
}

.add-to-cart-btn-fiction:disabled {
    background: #cccccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Бутон за любими за художествена литература */
.add-to-wishlist-btn-fiction {
    background: transparent;
    border: 2px solid #9C27B0; /* Пурпурен цвят като заглавието */
    color: #9C27B0;
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

.add-to-wishlist-btn-fiction:hover {
    background: #9C27B0;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(156, 39, 176, 0.2);
}

.add-to-wishlist-btn-fiction.added {
    background: #9C27B0;
    color: white;
    border-color: #9C27B0;
}

.add-to-wishlist-btn-fiction.added i {
    content: "\f004" !important; /* Пълно сърце */
}

/* Увеличи височината на картата за да има място за двата бутона */
.book-card-fiction {
    height: 430px; /* Увеличена височина за да има място за двата бутона */
}

/* Намали малко височината на изображението */
.book-image-fiction {
    height: 150px; /* Същата или малко по-ниска височина */
}

/* Коригирай позиционирането на текста */
.book-title-fiction {
    margin-bottom: 8px;
    min-height: 40px; /* Минимална височина за 2 реда */
}

/* Адаптивност за мобилни устройства */
@media (max-width: 768px) {
    .book-actions-fiction {
        gap: 8px;
    }
    
    .add-to-wishlist-btn-fiction {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
}

/* Адаптивен дизайн */
@media (max-width: 1400px) {
    .books-grid-fiction {
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    
    .book-card-fiction {
        max-width: 260px;
        height: 440px;
    }
    
    .book-title-fiction {
        min-height: 44px;
    }
}

@media (max-width: 1200px) {
    .books-grid-fiction {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .book-card-fiction {
        max-width: 280px;
        height: 450px;
    }
    
    .book-title-fiction {
        min-height: 48px;
    }
    
    .book-author-fiction {
        min-height: 40px;
    }
}

@media (max-width: 992px) {
    .books-grid-fiction {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .book-card-fiction {
        max-width: 300px;
        height: 460px;
    }
    
    .book-title-fiction {
        min-height: 52px;
    }
    
    .book-author-fiction {
        min-height: 44px;
    }
    
    .genre-buttons {
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .page-content-fiction {
        padding: 15px;
    }
    
    .books-grid-fiction {
        grid-template-columns: 1fr;
        max-width: 350px;
        margin: 0 auto 30px;
    }
    
    .book-card-fiction {
        max-width: 350px;
        height: 440px;
    }
    
    .book-image-fiction {
        height: 150px;
    }
    
    .book-image-fiction img {
        max-height: 150px;
    }
    
    .book-title-fiction {
        min-height: 48px;
    }
    
    .book-author-fiction {
        min-height: 40px;
    }
    
    .genre-tags {
        justify-content: center;
    }
    
    .genre-buttons .genre-btn {
        padding: 8px 12px;
        font-size: 12px;
    }
}
</style>

<div class="page-content-fiction">
    <h1>Художествена литература</h1>
    
    <div class="genre-filter">
        <h3>Филтрирай по жанр:</h3>
        <div class="genre-buttons">
            <button class="genre-btn <?= !$selected_genre_slug ? 'active' : '' ?>" 
                    onclick="window.location.href='fiction.php'">
                Всички
            </button>
            
            <?php foreach ($all_genres as $genre): ?>
            <button class="genre-btn <?= $selected_genre_slug == $genre['slug'] ? 'active' : '' ?>" 
                    style="border-left-color: <?= $genre['color'] ?>;"
                    onclick="window.location.href='fiction.php?genre=<?= urlencode($genre['slug']) ?>'">
                <?= htmlspecialchars($genre['name']) ?>
            </button>
            <?php endforeach; ?>
        </div>
    </div>
    
    <?php if ($total_books > 0): ?>
    <div class="page-info-fiction">
        Страница <?= $current_page ?> от <?= $total_pages ?> | 
        Показване на <?= min($books_per_page, $total_books - $offset) ?> книги
        <?php if ($offset + 1 <= $total_books): ?>
        (<?= ($offset + 1) ?>-<?= min($offset + $books_per_page, $total_books) ?>)
        <?php endif; ?>
        от <?= $total_books ?> общо
        <?php if ($selected_genre_slug): ?>
        | Жанр: <strong><?= htmlspecialchars($selected_genre_name) ?></strong>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <?php
    // ВЗИМАНЕ НА КНИГИТЕ (С ФИЛТЪР)
    if ($total_books > 0) {
        if ($selected_genre_id > 0) {
            // Книги с конкретен жанр
            $sql = "SELECT DISTINCT b.* 
                    FROM books b
                    JOIN book_genres bg ON b.id = bg.book_id
                    WHERE b.is_fiction = 1 AND bg.genre_id = ?
                    ORDER BY sales DESC, id DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("iii", $selected_genre_id, $books_per_page, $offset);
        } else {
            // Всички художествени книги
            $sql = "SELECT * FROM books 
                    WHERE is_fiction = 1 
                    ORDER BY sales DESC, id DESC
                    LIMIT ? OFFSET ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $books_per_page, $offset);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
    }
    
    if ($total_books > 0 && $result->num_rows > 0):
    ?>
    
    <div class="books-grid-fiction">
        <?php 
        $counter = $offset + 1;
        while ($book = $result->fetch_assoc()): 
            // Вземане на жанровете за тази книга
            $book_genres = getBookGenres($conn, $book['id']);
        ?>
        <div class="book-card-fiction">
            <?php if (!empty($book_genres)): 
                // Показване на първия жанр като стикер
                $main_genre = $book_genres[0];
            ?>
                <div class="genre-sticker" style="background: <?= $main_genre['color'] ?>;">
                    <?= htmlspecialchars($main_genre['name']) ?>
                </div>
                
                <?php if (count($book_genres) > 1): ?>
                <div class="additional-genres" title="<?= implode(', ', array_column($book_genres, 'name')) ?>">
                    +<?= count($book_genres) - 1 ?> още
                </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <div class="book-image-fiction">
                <img src="<?= htmlspecialchars($book['image']) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>"
                     onerror="this.src='https://via.placeholder.com/120x160/F3E5F5/9C27B0?text=Литература'">
            </div>
            
            <div class="book-title-fiction">
                <h3>
                    <a href="book.php?id=<?= $book['id'] ?>" 
                       class="book-title-link-fiction">
                        <?= htmlspecialchars($book['title']) ?>
                    </a>
                </h3>
            </div>
            
            <div class="book-author-fiction">
                <a href="author_books.php?author=<?= urlencode($book['author']) ?>" 
                   class="author-link-fiction">
                    <?= htmlspecialchars($book['author']) ?>
                </a>
            </div>
            
            <div class="price-sales-fiction">
                <span class="book-price-fiction"><?= number_format($book['price'], 2) ?> лв.</span>
                <span class="book-sales-fiction"><?= $book['sales'] ?> продажби</span>
            </div>
            <!-- Заменете текущия бутон с този код: -->
<div class="book-actions-fiction">
    <button class="add-to-cart-btn-fiction" 
            onclick="addToCartNew(<?= $book['id'] ?>, this, 'fiction')">
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
    <button class="add-to-wishlist-btn-fiction <?= $is_in_wishlist ? 'added' : '' ?>" 
            onclick="addToWishlistFiction(<?= $book['id'] ?>, this)">
        <i class="<?= $is_in_wishlist ? 'fas fa-heart' : 'far fa-heart' ?>"></i>
    </button>
    <?php endif; ?>
</div>
        </div>
        <?php endwhile; ?>
    
    <?php
    // ПАГИНАЦИЯ
    if ($total_pages > 1):
        // Създаване на query string за пагинация
        $query_params = $_GET;
        unset($query_params['page']); // Премахваме page параметъра
        $base_query = http_build_query($query_params);
        $base_url = 'fiction.php' . ($base_query ? '?' . $base_query : '');
    ?>
    <div class="pagination-section-fiction">
        <div class="pagination-fiction">
            <?php if ($current_page > 1): ?>
            <a href="<?= $base_url ?>&page=1">« Първа</a>
            <?php endif; ?>
            
            <?php if ($current_page > 1): ?>
            <a href="<?= $base_url ?>&page=<?= $current_page - 1 ?>">‹</a>
            <?php endif; ?>
            
            <?php 
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="<?= $base_url ?>&page=<?= $i ?>" 
               class="<?= $i == $current_page ? 'active-page' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="<?= $base_url ?>&page=<?= $current_page + 1 ?>">›</a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="<?= $base_url ?>&page=<?= $total_pages ?>">Последна »</a>
            <?php endif; ?>
        </div>
        
        <div style="text-align: center; margin-top: 20px;">
            <?php if ($current_page > 1): ?>
            <a href="<?= $base_url ?>&page=<?= $current_page - 1 ?>" class="page-nav-btn-fiction">
                ← Предишна страница
            </a>
            <?php endif; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="<?= $base_url ?>&page=<?= $current_page + 1 ?>" class="page-nav-btn-fiction">
                Следваща страница →
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="empty-state-fiction">
        <p>Все още няма налични книги в категория "Художествена литература".</p>
        <p>Моля, върнете се по-късно или разгледайте нашите <a href="top_books.php">топ заглавия</a>.</p>
        <a href="index.php">← Върни се към началната страница</a>
    </div>
    <?php endif; ?>
</div>

<script>
// ФУНКЦИЯ ЗА ДОБАВЯНЕ В КОШНИЦАТА
function addToCartFiction(productId, button) {
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
            
            showSimpleMessage('✓ Книгата е добавена в кошницата!', 'success', 'fiction');
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
function showSimpleMessage(text, type, pageType = 'default') {
    let msg = document.querySelector('.simple-message');
    if (msg) msg.remove();
    
    let bgColor = type === 'success' ? '#9C27B0' : '#f44336';
    
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

// ФУНКЦИЯ ЗА ДОБАВЯНЕ В ЛЮБИМИ (Художествена литература)
function addToWishlistFiction(bookId, button) {
    if (!button) return;
    
    // Проверка дали потребителят е влязъл
    <?php if (!isset($_SESSION['user_id'])): ?>
        showSimpleMessage('Моля, влезте в профила си за да добавите в любими!', 'info', 'fiction');
        return;
    <?php endif; ?>
    
    const isAdded = button.classList.contains('added');
    const originalIcon = button.innerHTML;
    
    if (isAdded) {
        // Премахване от любими
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        removeFromWishlistFiction(bookId, button, originalIcon);
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
                showSimpleMessage('✓ Книгата е добавена в любими!', 'success', 'fiction');
                
                // Обновяване на брояча за любими (ако има такъв)
                const wishlistCount = document.getElementById('wishlist-count');
                if (wishlistCount) {
                    let current = parseInt(wishlistCount.textContent) || 0;
                    wishlistCount.textContent = current + 1;
                }
            } else {
                button.innerHTML = originalIcon;
                showSimpleMessage('✗ ' + (data.message || 'Грешка при добавяне'), 'error', 'fiction');
            }
        })
        .catch(error => {
            console.error('Грешка:', error);
            button.disabled = false;
            button.innerHTML = originalIcon;
            showSimpleMessage('✗ Възникна грешка', 'error', 'fiction');
        });
    }
}

// ФУНКЦИЯ ЗА ПРЕМАХВАНЕ ОТ ЛЮБИМИ (Художествена литература)
function removeFromWishlistFiction(bookId, button, originalIcon) {
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
            showSimpleMessage('✓ Книгата е премахната от любими', 'success', 'fiction');
            
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
            showSimpleMessage('✗ ' + (data.message || 'Грешка при премахване'), 'error', 'fiction');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        button.disabled = false;
        button.innerHTML = originalIcon;
        showSimpleMessage('✗ Възникна грешка', 'error', 'fiction');
    });
}

// ФУНКЦИЯ ЗА ПРОВЕРКА ДАЛИ КНИГА Е В ЛЮБИМИ (за Художествена литература)
function checkWishlistStatusFiction() {
    <?php if (isset($_SESSION['user_id'])): ?>
    const bookIds = [];
    document.querySelectorAll('.add-to-wishlist-btn-fiction').forEach(btn => {
        const match = btn.getAttribute('onclick')?.match(/addToWishlistFiction\((\d+)/);
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
                    const btn = document.querySelector(`[onclick*="addToWishlistFiction(${bookId}"]`);
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
function addToCartNew(productId, button, pageType = 'fiction') {
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

// Промени функцията showSimpleMessage да поддържа цвят за "fiction"
function showSimpleMessage(text, type, pageType = 'default') {
    let msg = document.querySelector('.simple-message');
    if (msg) msg.remove();
    
    // Определяне на цвета според типа на страницата
    let bgColor;
    if (type === 'success') {
        if (pageType === 'fiction') {
            bgColor = '#9C27B0'; /* Пурпурен за художествена литература */
        } else if (pageType === 'children') {
            bgColor = '#FF6B6B'; /* Розов за детска литература */
        } else if (pageType === 'reference') {
            bgColor = '#FF7F50'; /* Коралов за справочници */
        } else if (pageType === 'tourism') {
            bgColor = '#32CD32'; /* Лимоново зелен за туризъм */
        } else if (pageType === 'dictionaries') {
            bgColor = '#FF9800'; /* Оранжев за речници */
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
    checkWishlistStatusFiction();
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