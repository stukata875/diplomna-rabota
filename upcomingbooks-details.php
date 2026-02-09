<?php
require_once 'config.php';

// Вземане на ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $page_title = 'Грешка';
    include 'header.php';
    echo '<div class="container mt-5 text-center alert alert-danger">Невалиден ID на книга.</div>';
    include 'footer.php';
    exit;
}

// Вземаме информация за книгата
$sql = "SELECT * FROM upcoming_books WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die('Грешка в SQL заявката: ' . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$book = $result->fetch_assoc();
$stmt->close();

if (!$book) {
    $page_title = 'Книгата не е намерена';
    include 'header.php';
    echo '<div class="container mt-5 text-center alert alert-warning">Книгата не е намерена.</div>';
    include 'footer.php';
    exit;
}

$page_title = htmlspecialchars($book['title']);
include 'header.php';
?>

<style>
/* ОСНОВЕН КОНТЕЙНЕР */
.book-details-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* БРЕЙДКРУМС */
.breadcrumb-upcoming {
    background: transparent;
    padding: 0;
    margin-bottom: 30px;
    font-size: 14px;
}

.breadcrumb-upcoming .breadcrumb-item a {
    color: #ff9800;
    text-decoration: none;
    font-weight: 500;
}

.breadcrumb-upcoming .breadcrumb-item.active {
    color: #666;
}

.breadcrumb-upcoming .breadcrumb-item + .breadcrumb-item::before {
    color: #999;
    content: ">";
}

/* ОСНОВЕН БЛОК С ИНФО */
.book-details-container {
    display: flex;
    gap: 50px;
    background: white;
    padding: 35px;
    border-radius: 12px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
    margin-bottom: 35px;
    border: 1px solid #eee;
}

/* КОЛОНА СЪС СНИМКА */
.book-image-column {
    flex: 0 0 380px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.book-image-column img {
    width: 100%;
    border-radius: 10px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.12);
}

.no-image {
    width: 100%;
    height: 450px;
    background: #f5f5f5;
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #888;
    font-size: 18px;
    border: 1px solid #ddd;
}

/* КОЛОНА С ИНФОРМАЦИЯ */
.book-info-column {
    flex: 1;
}

.book-info-column h1 {
    margin: 0 0 25px 0;
    font-size: 32px;
    color: #333;
    font-weight: 700;
    line-height: 1.3;
}

.book-author {
    font-size: 18px;
    color: #666;
    margin: 0 0 25px 0;
    font-weight: 500;
}

.book-author strong {
    color: #333;
    font-weight: 600;
}

/* ЦЕНА И СТАТУС СЕКЦИЯ */
.book-price-section-upcoming {
    margin: 30px 0;
}

.book-price-upcoming {
    font-size: 32px;
    color: #e60000;
    font-weight: 800;
    margin-bottom: 20px;
    display: block;
}

.upcoming-badge-main {
    display: inline-block;
    background: #ff9800;
    color: white;
    padding: 8px 20px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 15px;
}

.availability-upcoming {
    color: #ff9800;
    margin-bottom: 30px;
    font-size: 16px;
    font-weight: 500;
    display: block;
}

/* БУТОН ЗА УВЕДОМЯВАНЕ */
.btn-notify-upcoming {
    padding: 15px 30px;
    background: #ff9800;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 17px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    margin-top: 20px;
}

.btn-notify-upcoming:hover {
    background: #f57c00;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
}

/* ОПИСАНИЕ */
.book-description-upcoming {
    margin-top: 40px;
    padding: 30px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.08);
    border: 1px solid #eee;
}

.book-description-upcoming h2 {
    color: #333;
    margin: 0 0 20px 0;
    padding-bottom: 15px;
    border-bottom: 2px solid #ff9800;
    font-size: 24px;
    font-weight: 700;
}

.description-content-upcoming {
    line-height: 1.8;
    color: #444;
    font-size: 16px;
}
/* Стилове за линка на автора в upcoming_books.php */
.book-author a {
    color: #666;
    text-decoration: none;
    font-style: italic;
    transition: all 0.3s ease;
    display: inline-block;
    margin-left: 5px;
}

.book-author a:hover {
    color: #e60000;
    text-decoration: underline;
}
/* АДАПТИВЕН ДИЗАЙН */
@media (max-width: 900px) {
    .book-details-container {
        flex-direction: column;
        gap: 30px;
        padding: 25px;
    }
    
    .book-image-column {
        flex: 0 0 auto;
        align-items: center;
    }
    
    .book-image-column img {
        max-width: 300px;
    }
    
    .no-image {
        width: 300px;
        height: 400px;
    }
}

@media (max-width: 768px) {
    .book-details-page {
        padding: 15px;
    }
    
    .book-info-column h1 {
        font-size: 28px;
    }
    
    .book-price-upcoming {
        font-size: 28px;
    }
    
    .btn-notify-upcoming {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .book-details-container {
        padding: 20px;
    }
    
    .book-info-column h1 {
        font-size: 24px;
    }
    
    .book-price-upcoming {
        font-size: 24px;
    }
    
    .book-description-upcoming {
        padding: 20px;
    }
}
</style>

<div class="book-details-page">
    <div class="book-details-container">
        <!-- Снимка на книгата -->
        <div class="book-image-column">
            <?php if (!empty($book['cover_image'])): ?>
                <img src="<?= htmlspecialchars($book['cover_image']) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>">
            <?php else: ?>
                <div class="no-image">
                    <span>Няма снимка</span>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Информация за книгата -->
        <div class="book-info-column">
            <h1><?= htmlspecialchars($book['title']) ?></h1>
            
            <p class="book-author">
    <strong>Автор:</strong> 
    <a href="author_books.php?author=<?= urlencode($book['author']) ?>" 
       class="author-link" >
       <?= htmlspecialchars($book['author']) ?>
    </a>
</p>
              
            <span class="availability-upcoming">
                Тази книга скоро ще бъде налична за продажба
            </span>
            
            <div class="book-price-section-upcoming">
                <span class="book-price-upcoming"><?= number_format($book['price'], 2) ?> лв.</span>
                
                <button class="btn-notify-upcoming" 
                        onclick="notifyAboutBook(<?= $book['id'] ?>)">
                    Уведоми ме при излизане
                </button>
            </div>
        </div>
    </div>
    
    <?php if (!empty($book['description'])): ?>
    <div class="book-description-upcoming">
        <h2>Описание</h2>
        <div class="description-content-upcoming">
            <?= nl2br(htmlspecialchars($book['description'])) ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function notifyAboutBook(bookId) {
    if (confirm('Искате ли да бъдете уведомен, когато тази книга излезе?')) {
        alert('Ще ви уведомим, когато книгата излезе!');
    }
}
</script>

<?php 
$conn->close();
include 'footer.php'; 
?>