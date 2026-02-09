<?php
include 'config.php';

$author = isset($_GET['author']) ? urldecode($_GET['author']) : '';

if (empty($author)) {
    $page_title = 'Грешка';
    include 'header.php';
    echo '<div class="container mt-5 alert alert-danger">Не е посочен автор.</div>';
    include 'footer.php';
    exit;
}

$page_title = 'Книги от ' . htmlspecialchars($author);
include 'header.php';

// Взимаме всички книги на автора - И от books И от upcoming_books
$sql = "SELECT 
            id, 
            title, 
            'book' as type, 
            price, 
            image, 
            sales, 
            description, 
            is_new,
            author,
            '' as status  -- За книгите няма статус
        FROM books 
        WHERE author = ?
        
        UNION ALL
        
        SELECT 
            id, 
            title, 
            'upcoming' as type, 
            price, 
            cover_image as image, 
            0 as sales, 
            description, 
            'upcoming' as is_new,
            author,
            status
        FROM upcoming_books 
        WHERE author = ? AND status = 'upcoming'
        
        ORDER BY type, title ASC";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $author, $author);
$stmt->execute();
$result = $stmt->get_result();

// Броим книгите
$book_count = 0;
$upcoming_count = 0;
?>

<style>
/* СТИЛОВЕ ЗА СТРАНИЦАТА С АВТОР */
.author-page-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.author-header {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 2px solid #e60000;
}

.author-header h1 {
    color: #333;
    font-size: 32px;
    font-weight: 700;
    margin-bottom: 10px;
}

.author-subtitle {
    color: #666;
    font-size: 18px;
    font-style: italic;
}

/* СТАТИСТИКА */
.author-stats {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 25px;
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.stat-icon {
    font-size: 20px;
}

.stat-count {
    font-weight: bold;
    font-size: 18px;
    color: #e60000;
}

.stat-label {
    color: #666;
    font-size: 14px;
}

/* ГРИД С КНИГИ */
.books-grid-author {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 20px;
    margin-bottom: 40px;
}

@media (max-width: 1400px) {
    .books-grid-author { grid-template-columns: repeat(4, 1fr); }
}

@media (max-width: 1200px) {
    .books-grid-author { grid-template-columns: repeat(3, 1fr); }
}

@media (max-width: 900px) {
    .books-grid-author { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 600px) {
    .books-grid-author { grid-template-columns: 1fr; }
}

/* КАРТИЧКА ЗА КНИГА */
.book-card-author {
    background: white;
    border-radius: 10px;
    padding: 15px;
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 380px;
    position: relative;
}

.book-card-author:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    border-color: #e60000;
}

/* БАДЖ ЗА ПРЕДСТОЯЩА КНИГА */
.upcoming-badge-author {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #ff9800;
    color: white;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: bold;
    z-index: 1;
}

/* СНИМКА */
.book-image-author {
    text-align: center;
    margin-bottom: 15px;
    height: 160px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.book-image-author img {
    max-height: 160px;
    max-width: 120px;
    object-fit: contain;
    border-radius: 6px;
}

/* ЗАГЛАВИЕ */
.book-title-author {
    margin-bottom: 10px;
    min-height: 42px;
}

.book-title-author a {
    color: #333;
    text-decoration: none;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.book-title-author a:hover {
    color: #e60000;
    text-decoration: underline;
}

/* ЦЕНА */
.book-price-author {
    color: #e60000;
    font-weight: bold;
    font-size: 17px;
    margin-bottom: 4px;
}

/* БУТОН */
.book-btn-author {
    background: #e60000;
    color: white;
    border: none;
    padding: 10px 0;
    border-radius: 6px;
    font-size: 14px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    width: 100%;
    text-align: center;
    margin-top: auto;
    text-decoration: none;
    display: block;
}

.book-btn-author:hover {
    background: #c40000;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(230, 0, 0, 0.2);
}

/* КОГАТО НЯМА КНИГИ */
.no-books-author {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 30px 0;
}

.no-books-author h3 {
    color: #666;
    margin-bottom: 15px;
}

.no-books-icon {
    font-size: 48px;
    color: #ddd;
    margin-bottom: 20px;
}

/* ФИЛЬТЪР ЗА ТИП КНИГИ */
.books-filter {
    display: flex;
    gap: 15px;
    margin-bottom: 25px;
    flex-wrap: wrap;
}

.filter-btn {
    padding: 8px 20px;
    background: #f0f0f0;
    border: 1px solid #ddd;
    border-radius: 20px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
}

.filter-btn:hover {
    background: #e0e0e0;
}

.filter-btn.active {
    background: #e60000;
    color: white;
    border-color: #e60000;
}

.filter-btn.upcoming-active {
    background: #ff9800;
    color: white;
    border-color: #ff9800;
}
</style>

<div class="author-page-container">
    <div class="author-header">
        <h1>Книги от <?= htmlspecialchars($author) ?></h1>
        <div class="author-subtitle">Всички книги на автора</div>
    </div>

    <?php 
    // Броим книгите докато ги взимаме
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
        if ($row['type'] == 'book') {
            $book_count++;
        } else {
            $upcoming_count++;
        }
    }
    ?>

   

    <?php if (empty($books)): ?>
        <!-- Когато няма книги -->
        <div class="no-books-author">
            <div class="no-books-icon"></div>
            <h3>Все още няма добавени книги за <?= htmlspecialchars($author) ?></h3>
            <p>Можете да проверите:</p>
            <ul style="text-align: left; max-width: 500px; margin: 20px auto;">
                <li>Дали авторът е записан по същия начин в базата данни</li>
                <li>Дали има интервали или специални символи</li>
                <li>Дали книгата е маркирана като активна</li>
            </ul>
            <a href="index.php" class="book-btn-author" style="display: inline-block; width: auto; padding: 10px 30px;">
                ← Върни се към началната страница
            </a>
        </div>
    <?php else: ?>
        <!-- Списък с книги -->
        <div class="books-grid-author">
            <?php foreach ($books as $book): ?>
                <div class="book-card-author">
                    <!-- Бадж за предстояща книга -->
                    <?php if ($book['type'] == 'upcoming'): ?>
                        <div class="upcoming-badge-author">Предстояща</div>
                    <?php endif; ?>
                    
                    <!-- Снимка -->
                    <div class="book-image-author">
                        <?php if (!empty($book['image'])): ?>
                            <img src="<?= htmlspecialchars($book['image']) ?>" 
                                 alt="<?= htmlspecialchars($book['title']) ?>">
                        <?php else: ?>
                            <div style="background: #f5f5f5; width: 120px; height: 160px; 
                                        display: flex; align-items: center; justify-content: center;
                                        border-radius: 6px; color: #999; font-size: 14px;">
                                Няма снимка
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Заглавие -->
                    <div class="book-title-author">
                        <a href="<?= $book['type'] == 'book' ? 'book.php' : 'upcomingbooks-details.php' ?>?id=<?= $book['id'] ?>">
                            <?= htmlspecialchars($book['title']) ?>
                        </a>
                    </div>
                    
                    <!-- Цена -->
                    <div class="book-price-author"><?= number_format($book['price'], 2) ?> лв.</div>
                    
                    <!-- Бутон -->
                    <a href="<?= $book['type'] == 'book' ? 'book.php' : 'upcomingbooks-details.php' ?>?id=<?= $book['id'] ?>" 
                       class="book-btn-author">
                        <?= $book['type'] == 'book' ? 'Виж повече' : 'Виж детайли' ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// JavaScript за филтриране на книгите (по избор)
function filterBooks(type) {
    const cards = document.querySelectorAll('.book-card-author');
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    // Промяна на активния бутон
    filterBtns.forEach(btn => {
        btn.classList.remove('active', 'upcoming-active');
        if (btn.dataset.type === type) {
            if (type === 'upcoming') {
                btn.classList.add('upcoming-active');
            } else {
                btn.classList.add('active');
            }
        }
    });
    
    // Показване/скриване на книги
    cards.forEach(card => {
        if (type === 'all') {
            card.style.display = 'flex';
        } else {
            const isUpcoming = card.querySelector('.upcoming-badge-author');
            if ((type === 'upcoming' && isUpcoming) || (type === 'book' && !isUpcoming)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        }
    });
}
</script>

<?php
$stmt->close();
$conn->close();
include 'footer.php';
?>