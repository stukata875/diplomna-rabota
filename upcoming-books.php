<?php
require_once 'config.php';

$page_title = 'Предстоящи книги';
include 'header.php';

$books_per_page = 50; // Променено на 50
$current_page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($current_page - 1) * $books_per_page;

// Брой книги
$count_sql = "SELECT COUNT(*) FROM upcoming_books WHERE status = 'upcoming'";
$total_books = $conn->query($count_sql)->fetch_row()[0];
$total_pages = ceil($total_books / $books_per_page);

// Коригиране на текущата страница
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
}

// Книги
$sql = "
    SELECT id, title, author, description, cover_image, price
    FROM upcoming_books
    WHERE status = 'upcoming'
    ORDER BY id DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $books_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<style>
/* СТИЛОВЕ ЗА ПРЕДСТОЯЩИ КНИГИ - ИДЕНТИЧНИ С НОВИТЕ КНИГИ */
.page-content-upcoming {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.page-content-upcoming h1 {
    color: #333;
    padding-bottom: 15px;
    margin-bottom: 30px;
    font-size: 32px;
    font-weight: 600;
    border-bottom: 3px solid #ff9800; /* Оранжево за предстоящи */
    text-align: center;
}

/* МРЕЖА ОТ КНИГИ - 5 КОЛОНИ */
.books-grid-upcoming {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 40px;
    justify-items: center;
}

/* КАРТИЧКА ЗА КНИГА */
.book-card-upcoming {
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
    height: 380px;
}

.book-card-upcoming:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: #ff9800; /* Оранжево при ховър */
}

/* БАДЖ ЗА ПРЕДСТОЯЩА КНИГА */
.upcoming-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #ff9800;
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    z-index: 1;
}

/* СНИМКА */
.book-image-upcoming {
    text-align: center;
    margin-bottom: 15px;
    flex-shrink: 0;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.book-image-upcoming img {
    max-height: 160px;
    max-width: 120px;
    object-fit: contain;
    border-radius: 6px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

/* ЗАГЛАВИЕ */
.book-title-upcoming {
    margin-bottom: 10px;
    min-height: 42px;
}

.book-title-link-upcoming {
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

.book-title-link-upcoming:hover {
    color: #ff9800; /* Оранжево при ховър */
    text-decoration: underline;
}

/* АВТОР */
.book-author-upcoming {
    color: #666;
    margin: 0 0 12px 0;
    font-size: 13px;
    font-style: italic;
    height: 18px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

/* ЦЕНА */
.book-price-upcoming {
    color: #ff9800;
    font-weight: bold;
    font-size: 17px;
    margin-bottom: 4px;
}

/* БУТОН */
.view-details-btn-upcoming {
    background: #ff9800; /* Оранжево за предстоящи */
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
    text-decoration: none;
    display: block;
}

.view-details-btn-upcoming:hover {
    background: #f57c00; /* По-тъмно оранжево */
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(255, 152, 0, 0.2);
}

/* ПАГИНАЦИЯ */
.pagination-section-upcoming {
    margin-top: 20px;
    text-align: center;
}

.pagination-upcoming {
    display: flex;
    justify-content: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.pagination-upcoming a {
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

.pagination-upcoming a:hover {
    background: #ff9800; /* Оранжево */
    color: white;
    border-color: #ff9800;
}

.pagination-upcoming a.active-page {
    background: #ff9800; /* Оранжево */
    color: white;
    border-color: #ff9800;
    font-weight: bold;
}

.page-info-upcoming {
    text-align: center;
    margin-bottom: 25px;
    color: #666;
    font-size: 15px;
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #e0e0e0;
}

/* ПРАЗЕН СПИСЪК */
.empty-state-upcoming {
    text-align: center;
    padding: 50px 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 30px 0;
}

.empty-state-upcoming p {
    color: #666;
    font-size: 16px;
    margin-bottom: 20px;
}

.empty-state-upcoming a {
    color: #ff9800; /* Оранжево */
    text-decoration: none;
    font-weight: bold;
    border: 2px solid #ff9800;
    padding: 10px 20px;
    border-radius: 6px;
    display: inline-block;
    transition: all 0.3s;
}

.empty-state-upcoming a:hover {
    background: #ff9800;
    color: white;
}

/* ИНФОРМАЦИОНЕН ПАНЕЛ */
.info-panel-upcoming {
    background: #fff8e1; /* Светло оранжев фон */
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 25px;
    font-size: 14px;
    border-left: 4px solid #ff9800;
    color: #5d4037; /* Кафяво-оранжев текст */
}

.info-panel-upcoming strong {
    color: #ff9800;
}

/* ЛИНК НА АВТОР */
.author-link-upcoming {
    color: #666;
    text-decoration: none;
    font-style: italic;
    transition: color 0.3s ease;
}

.author-link-upcoming:hover {
    color: #ff9800; /* Оранжево при ховър */
    text-decoration: underline;
}

/* АДАПТИВЕН ДИЗАЙН */
@media (max-width: 1400px) {
    .books-grid-upcoming {
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    
    .book-card-upcoming {
        max-width: 260px;
    }
}

@media (max-width: 1200px) {
    .books-grid-upcoming {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .book-card-upcoming {
        max-width: 280px;
    }
}

@media (max-width: 992px) {
    .books-grid-upcoming {
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    .book-card-upcoming {
        max-width: 300px;
    }
}

@media (max-width: 768px) {
    .page-content-upcoming {
        padding: 15px;
    }
    
    .books-grid-upcoming {
        grid-template-columns: 1fr;
        max-width: 350px;
        margin: 0 auto 30px;
    }
    
    .book-card-upcoming {
        max-width: 350px;
        height: 360px;
    }
    
    .book-image-upcoming {
        height: 150px;
    }
    
    .book-image-upcoming img {
        max-height: 150px;
    }
}

@media (max-width: 576px) {
    .pagination-upcoming a {
        padding: 6px 10px;
        font-size: 13px;
        min-width: 32px;
    }
}
</style>

<div class="page-content-upcoming">
    <h1>Предстоящи книги</h1>
    
    <?php if ($result->num_rows > 0): ?>
<div class="page-info-upcoming">
    Страница <?= $current_page ?> от <?= $total_pages ?> | 
    Показване на <?= min($books_per_page, $total_books - $offset) ?> книги (<?= ($offset + 1) ?>-<?= min($offset + $books_per_page, $total_books) ?>) от <?= $total_books ?> общо
</div>
<?php endif; ?>

    <?php if ($total_pages > 1): ?>
    <!-- Пагинация отгоре -->
    <div class="pagination-section-upcoming">
        <div class="pagination-upcoming">
            <?php if ($current_page > 1): ?>
            <a href="?page=<?= $current_page - 1 ?>">‹</a>
            <?php endif; ?>
            
            <?php 
            $start_page = max(1, $current_page - 2);
            $end_page = min($total_pages, $current_page + 2);
            
            for ($i = $start_page; $i <= $end_page; $i++): ?>
            <a href="?page=<?= $i ?>" 
               class="<?= $i == $current_page ? 'active-page' : '' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            
            <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?= $current_page + 1 ?>">›</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($result->num_rows === 0): ?>
        <div class="empty-state-upcoming">
            <div style="font-size: 48px; color: #ff9800; margin-bottom: 20px;">⏳</div>
            <h3 style="color: #666; margin-bottom: 15px;">В момента няма предстоящи книги</h3>
            <p>Очаквайте скоро нови издания!</p>
            <a href="books.php">Виж наличните книги</a>
        </div>
    <?php else: ?>
        <div class="books-grid-upcoming">
            <?php while ($book = $result->fetch_assoc()): ?>
                <div class="book-card-upcoming">
                    <!-- Бадж за предстояща книга -->
                    <div class="upcoming-badge">Предстояща</div>
                    
                    <!-- Снимка -->
                    <div class="book-image-upcoming">
                        <?php if (!empty($book['cover_image'])): ?>
                            <img src="<?= htmlspecialchars($book['cover_image']) ?>" 
                                 alt="<?= htmlspecialchars($book['title']) ?>">
                        <?php else: ?>
                            <div style="background: #f5f5f5; width: 120px; height: 160px; 
                                        display: flex; align-items: center; justify-content: center;
                                        border-radius: 6px; color: #999; font-size: 14px;">
                                Няма снимка
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Информация -->
                    <div class="book-title-upcoming">
                        <a href="upcomingbooks-details.php?id=<?= $book['id'] ?>" 
                           class="book-title-link-upcoming">
                            <?= htmlspecialchars($book['title']) ?>
                        </a>
                    </div>
                    
                    <p class="book-author-upcoming">
                        <a href="author_books.php?author=<?= urlencode($book['author']) ?>" 
                           class="author-link-upcoming">
                            <?= htmlspecialchars($book['author']) ?>
                        </a>
                    </p>
                    
                    <div class="book-price-upcoming"><?= number_format($book['price'], 2) ?> лв.</div>
                    
                    <a href="upcomingbooks-details.php?id=<?= $book['id'] ?>" 
                       class="view-details-btn-upcoming">
                        Виж повече
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
        
        <?php if ($total_pages > 1): ?>
        <!-- Пагинация отдолу -->
        <div class="pagination-section-upcoming">
            <div class="pagination-upcoming">
                <?php if ($current_page > 1): ?>
                <a href="?page=<?= $current_page - 1 ?>">‹</a>
                <?php endif; ?>
                
                <?php 
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++): ?>
                <a href="?page=<?= $i ?>" 
                   class="<?= $i == $current_page ? 'active-page' : '' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                <a href="?page=<?= $current_page + 1 ?>">›</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
$stmt->close();
$conn->close();
include 'footer.php';
?>