<?php
include 'config.php';
$page_title = 'Начало';
include 'header.php';
?>

<style>
/* HERO СЕКЦИЯ */
.hero-section {
    text-align: center;
    padding: 60px 20px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    margin-bottom: 50px;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

.hero-section h1 {
    color: #333;
    margin-bottom: 25px;
    font-size: 42px;
    font-weight: 700;
    border-bottom: none;
}

.hero-section p {
    color: #555;
    font-size: 20px;
    max-width: 800px;
    margin: 0 auto 35px;
    line-height: 1.6;
}

.hero-btn {
    background: #e60000;
    color: white;
    padding: 16px 40px;
    text-decoration: none;
    border-radius: 10px;
    font-weight: bold;
    font-size: 18px;
    display: inline-block;
    transition: all 0.3s ease;
    border: 2px solid #e60000;
    box-shadow: 0 4px 12px rgba(230, 0, 0, 0.2);
}

.hero-btn:hover {
    background: #ffffff;
    color: #e60000;
    text-decoration: none;
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(230, 0, 0, 0.3);
}

/* СЕКЦИИ С КНИГИ */
.books-section {
    max-width: 1300px;
    margin: 0 auto 70px;
    padding: 0 20px;
}

.books-section h2 {
    color: #333;
    padding-bottom: 15px;
    margin-bottom: 35px;
    font-size: 32px;
    font-weight: 600;
    border-bottom: 3px solid #e60000;
    text-align: center;
}

/* ГРИД ЗА КНИГИ - ЦЕНТРИРАН */
.books-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 30px;
    margin-bottom: 50px;
    justify-items: center;
}

@media (max-width: 1200px) {
    .books-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }
}

@media (max-width: 992px) {
    .books-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
    }
}

@media (max-width: 768px) {
    .books-grid {
        grid-template-columns: 1fr;
        max-width: 400px;
        margin: 0 auto 40px;
    }
}

/* КАРТИЧКА ЗА КНИГА */
.book-card {
    width: 100%;
    max-width: 280px;
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    background: white;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.book-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    border-color: #e60000;
}

.book-image {
    text-align: center;
    margin-bottom: 10px;
    flex-shrink:0;
}

.book-image img {
    width: 170px;
    height: 230px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.book-info {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.book-info h3 {
    margin: 0 0 12px 0;
    font-size: 17px;
    font-weight: 600;
    line-height: 1.4;
    min-height: 48px;
}

.book-info h3 a {
    color: #333;
    text-decoration: none;
    transition: color 0.3s ease;
}

.book-info h3 a:hover {
    color: #e60000;
    text-decoration: underline;
}

.author {
    color: #666;
    margin: 0 0 15px 0;
    font-size: 15px;
    font-style: italic;
}

.price-sales {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: auto 0 15px 0;
}

.price {
    color: #e60000;
    font-weight: bold;
    font-size: 20px;
}

.sales {
    color: #666;
    font-size: 14px;
    background: #f5f5f5;
    padding: 5px 12px;
    border-radius: 15px;
}

.add-to-cart-form {
    margin-top: 10px;
}

.add-to-cart-btn {
    background: #e60000;
    color: white;
    border: none;
    padding: 14px 0;
    border-radius: 8px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    text-align: center;
}

.add-to-cart-btn:hover {
    background: #c40000;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(230, 0, 0, 0.2);
}

/* СТИЛОВЕ ЗА СЕКЦИЯТА С НАЙ-НОВИ КНИГИ */
.new-books-section {
    margin-bottom: 80px;
}

.new-books-container {
    position: relative;
    overflow: hidden;
    border-radius: 15px;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    padding: 30px 20px;
    max-width: 1300px;
    margin: 0 auto;
    box-shadow: 0 8px 25px rgba(0,0,0,0.05);
}

.new-books-slider {
    display: flex;
    gap: 30px;
    transition: transform 0.3s ease;
    padding: 10px 5px;
}

.new-book-card {
    width: 280px; /* ФИКСИРАНА ШИРИНА */
    flex-shrink: 0; /* Важно - да не се свиват картите */
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    background: white;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}

.new-book-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.1);
    border-color: #e60000;
}

.no-new-books {
    text-align: center;
    padding: 60px 20px;
    width: 100%;
}

.no-new-books p {
    font-size: 18px;
    color: #666;
    font-style: italic;
}

/* АДАПТИВНОСТ */
@media (max-width: 1200px) {
    .new-book-card {
        width: 260px;
    }
}

@media (max-width: 992px) {
    .new-book-card {
        width: 240px;
    }
}

@media (max-width: 768px) {
    .new-books-container {
        padding: 20px 15px;
    }
    
    .new-book-card {
        width: 220px;
    }
}

@media (max-width: 576px) {
    .new-book-card {
        width: 200px;
    }
}

/* ДОБАВИ ТОЗИ CSS В КРАЯ НА СТИЛОВЕТЕ В index.php */

/* СТИЛОВЕ ЗА КАРТИЧКИТЕ В СЛАЙДЕРА - ДА СА КАТО В new_books.php */
.new-book-card {
    width: 280px; /* ФИКСИРАНА ШИРИНА */
    flex-shrink: 0; /* Важно - да не се свиват картите */
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    padding: 20px;
    background: white;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column; /* Важно за подреждането */
    height: 400px; /* СЪЩАТА ВИСОЧИНА като в new_books.php */
    position: relative; /* За позициониране на абсолютни елементи */
}

.new-book-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 25px rgba(0,0,0,0.1);
    border-color: #e60000;
}

/* СНИМКА В СЛАЙДЕРА */
.new-book-card .book-image {
    text-align: center;
    height: 180px; /* ФИКСИРАНА ВИСОЧИНА */
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0; /* Да не се свива */
}

.new-book-card .book-image img {
    max-height: 180px;
    max-width: 140px;
    object-fit: contain;
    border-radius: 4px;
}

/* ИНФО ЧАСТ В СЛАЙДЕРА */
.new-book-card .book-info {
    flex-grow: 1; /* Заема останалото пространство */
    display: flex;
    flex-direction: column;
}

/* ЗАГЛАВИЕ В СЛАЙДЕРА */
.new-book-card .book-info h3 {
    margin: 0 0 10px 0;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.3;
    height: 45px; /* ФИКСИРАНА ВИСОЧИНА ЗА 2 РЕДА */
    overflow: hidden;
}

.new-book-card .book-info h3 a {
    color: #333;
    text-decoration: none;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.3;
}

/* АВТОР В СЛАЙДЕРА */
.new-book-card .author {
    color: #666;
    font-size: 14px;
    margin-bottom: 15px;
    font-style: italic;
    height: 20px;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
}

/* ЦЕНА И ПРОДАЖБИ В СЛАЙДЕРА */
.new-book-card .price-sales {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.new-book-card .price {
    color: #e60000;
    font-weight: bold;
    font-size: 18px;
}

.new-book-card .sales {
    color: #888;
    font-size: 13px;
    background: #f5f5f5;
    padding: 4px 10px;
    border-radius: 12px;
}

/* БУТОН В СЛАЙДЕРА - АБСОЛЮТНО ПОЗИЦИОНИРАН */
.new-book-card .add-to-cart-btn {
    background: #e60000;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
    width: calc(100% - 40px); /* 100% минус padding на картата */
    margin-top: auto; /* БУТА БУТОНА НАДОЛУ */
    position: absolute;
    bottom: 20px;
    left: 20px;
    right: 20px;
    transition: background 0.3s;
}

.new-book-card .add-to-cart-btn:hover {
    background: #c40000;
}

/* СТИЛОВЕ ЗА СЕКЦИЯТА С ИЗБРАНИ АВТОРИ */
.featured-authors-section {
    margin-bottom: 80px;
    padding: 0 20px;
    max-width: 1300px;
    margin-left: auto;
    margin-right: auto;
}

.featured-authors-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    flex-wrap: wrap;
    gap: 15px;
}

.featured-authors-header h2 {
    color: #333;
    font-size: 32px;
    font-weight: 600;
    margin: 0;
    padding-bottom: 10px;
    border-bottom: 3px solid #e60000;
}

.view-all-authors {
    color: #e60000;
    text-decoration: none;
    font-weight: bold;
    font-size: 16px;
    padding: 8px 16px;
    border: 2px solid #e60000;
    border-radius: 8px;
    transition: all 0.3s ease;
    display: inline-block;
}

.view-all-authors:hover {
    background: #e60000;
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(230, 0, 0, 0.2);
}

.authors-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

@media (max-width: 1200px) {
    .authors-grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
}

@media (max-width: 992px) {
    .authors-grid {
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
}

@media (max-width: 768px) {
    .authors-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
}

@media (max-width: 576px) {
    .authors-grid {
        grid-template-columns: 1fr;
        max-width: 300px;
        margin: 0 auto;
    }
}

.author-card {
    background: white;
    border-radius: 12px;
    padding: 25px 20px;
    text-align: center;
    transition: all 0.3s ease;
    border: 1px solid #e0e0e0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    cursor: pointer;
}

.author-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-color: #e60000;
}

.author-image {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    margin: 0 auto 15px;
    border: 0px solid #f0f0f0;
    transition: border-color 0.3s ease;
}

.author-card:hover .author-image {
    border-color: #e60000;
}

.author-name {
    color: #333;
    font-size: 16px;
    font-weight: 600;
    margin: 0;
    line-height: 1.3;
}

.author-books-count {
    color: #666;
    font-size: 13px;
    margin-top: 5px;
    font-style: italic;
}

.no-authors {
    text-align: center;
    padding: 40px 20px;
    background: #f8f9fa;
    border-radius: 10px;
    margin: 30px 0;
    grid-column: 1 / -1;
}

.no-authors p {
    color: #666;
    font-size: 16px;
    margin-bottom: 20px;
}

/* Стилове за линка на автора в началната страница */
.author-link-index {
    color: #666;
    text-decoration: none;
    transition: color 0.3s ease;
}

.author-link-index:hover {
    color: #e60000;
}

/* Стилове специално за секцията "Най-нови книги" */
.new-books-section .book-title-link-index {
    color: #333;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-block;
}

.new-books-section .book-title-link-index:hover {
    color: #e60000;
    text-decoration: underline;
    transform: translateY(-1px);
}

.new-books-section .author-link-index {
    color: #666;
    text-decoration: none;
    font-style: italic;
    transition: all 0.3s ease;
    display: inline-block;
}

.new-books-section .author-link-index:hover {
    color: #e60000;
    text-decoration: underline;
    transform: translateY(-1px);
}

/* Стилове за секцията с най-нови книги */
/* Стилове за секцията с най-нови книги - ПОПРАВКА */
.new-books-section {
    margin-bottom: 80px;
    padding: 0 20px;
    max-width: 1300px;
    margin-left: auto;
    margin-right: auto;
}

/* Заглавие на секцията */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding: 0 10px;
}

.section-header h2 {
    color: #333;
    font-size: 32px;
    font-weight: 600;
    margin: 0;
    padding-bottom: 10px;
    border-bottom: 3px solid #e60000;
    border-bottom: none !important; /* Това е важно - да няма черта под заглавието */
    text-align: left; /* Заглавието да е вляво */
}

/* Линк "Виж всички" */
.view-all-link {
    text-align: right;
    margin-bottom: 25px;
    padding: 0 10px;
}

.view-all {
    color: #e60000;
    text-decoration: none;
    font-weight: bold;
    font-size: 16px;
    padding: 8px 16px;
    border: 2px solid #e60000;
    border-radius: 8px;
    transition: all 0.3s ease;
    display: inline-block;
}

.view-all:hover {
    background: #e60000;
    color: white;
    text-decoration: none;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(230, 0, 0, 0.2);
}

/* Контейнер за слайдера */
.new-books-container {
    position: relative;
    overflow: hidden;
    border-radius: 15px;
    background: white; /* Променено от градиент на бяло */
    padding: 30px;
    max-width: 1300px;
    margin: 0 auto;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: 1px solid #e0e0e0;
}

/* Самия слайдер */
.new-books-slider {
    display: flex;
    gap: 30px;
    transition: transform 0.3s ease;
    padding: 10px 5px;
}

/* Бутони за навигация - да са в дясно на заглавието */
.nav-buttons {
    display: flex;
    gap: 10px;
    align-items: center;
}

.nav-btn {
    width: 44px;
    height: 44px;
    background: #e60000;
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 3px 10px rgba(230, 0, 0, 0.2);
}

.nav-btn:hover {
    background: #c40000;
    transform: scale(1.1);
    box-shadow: 0 5px 15px rgba(230, 0, 0, 0.3);
}

.nav-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* СТИЛ ЗА НОВИЯ БУТОН ЗА КОШНИЦАТА - КАТО СТАРИЯ */
.add-to-cart-btn-new {
    background: #e60000;
    color: white;
    border: none;
    padding: 14px 0;
    border-radius: 8px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    text-align: center;
    margin-top: 10px;
}

.add-to-cart-btn-new:hover {
    background: #c40000;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(230, 0, 0, 0.2);
}

/* За картите в слайдера */
.new-book-card .add-to-cart-btn-new {
    background: #e60000;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    font-size: 14px;
    width: calc(100% - 40px);
    margin-top: auto;
    position: absolute;
    bottom: 20px;
    left: 20px;
    right: 20px;
    transition: background 0.3s;
}

.new-book-card .add-to-cart-btn-new:hover {
    background: #c40000;
}

/* СТИЛОВЕ ЗА ДЕЙСТВИЯТА С КНИГА */
.book-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.add-to-wishlist-btn {
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
}

.add-to-wishlist-btn:hover {
    background: #e60000;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(230, 0, 0, 0.2);
}

.add-to-wishlist-btn.added {
    background: #e60000;
    color: white;
    border-color: #e60000;
}

.add-to-wishlist-btn.added i {
    content: "\f004" !important; /* Пълно сърце */
}

/* За картите в слайдера */
.new-book-card .book-actions {
    position: absolute;
    bottom: 20px;
    left: 20px;
    right: 20px;
    display: flex;
    gap: 10px;
}

.new-book-card .add-to-cart-btn-new {
    flex: 1;
    margin-top: 0;
    position: static;
}

.new-book-card .add-to-wishlist-btn {
    margin-top: 0;
    position: static;
    width: 44px;
}

/* За обикновените карти */
.book-card .book-actions {
    display: flex;
    gap: 10px;
    margin-top: 10px;
}

.book-card .add-to-cart-btn-new {
    flex: 1;
    margin-top: 0;
}

.book-card .add-to-wishlist-btn {
    margin-top: 0;
    width: 44px;
}

/* Индикатор, че не си влязъл в профила */
.wishlist-login-required {
    position: absolute;
    bottom: 65px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 10;
    opacity: 0;
    transition: opacity 0.3s;
    pointer-events: none;
}

.book-card:hover .wishlist-login-required,
.new-book-card:hover .wishlist-login-required {
    opacity: 1;
}

</style>


<div class="page-content">
    <!-- Hero секция -->
    <div class="hero-section">
        <h1>Добре дошли в BookStore</h1>
        <p>
            Най-голямата онлайн книжарница с над 100,000 заглавия на български и чужди езици
        </p>
        <a href="books.php" class="hero-btn">Разгледайте книгите</a>
    </div>
    
    <!-- Най-продавани книги -->
<!-- Най-продавани книги -->
<div class="books-section">
    <h2>Най-продавани книги</h2>
    <div class="books-grid">
        <?php
        $stmt = $conn->prepare("SELECT * FROM books ORDER BY sales DESC LIMIT 8");
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($book = $result->fetch_assoc()):
        ?>
        <div class="book-card">
            <div class="book-image">
                <img src="<?= htmlspecialchars($book['image']) ?>" 
                     alt="<?= htmlspecialchars($book['title']) ?>"
                     onerror="this.src='https://via.placeholder.com/170x220/DDD/333?text=Няма+снимка'">
            </div>
            <div class="book-info">
                <h3>
                    <a href="book.php?id=<?= $book['id'] ?>">
                        <?= htmlspecialchars(mb_substr($book['title'], 0, 50, 'UTF-8')) . (mb_strlen($book['title'], 'UTF-8') > 50 ? '...' : '') ?>
                    </a>
                </h3>
                <p class="author">
                    <a href="author_books.php?author=<?= urlencode($book['author']) ?>" 
                       class="author-link-index">
                        <?= htmlspecialchars($book['author']) ?>
                    </a>
                </p>
                <div class="price-sales">
                    <span class="price"><?= number_format($book['price'], 2) ?> лв.</span>
                    <span class="sales"><?= $book['sales'] ?> продажби</span>
                </div>
                
                <!-- ТУК Е ПОПРАВКА: ЕДИН БЛОК book-actions -->
                <div class="book-actions">
                    <button class="add-to-cart-btn-new" 
                            onclick="addToCartNew(<?= $book['id'] ?>, this)">
                        Добави в кошницата
                    </button>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <button class="add-to-wishlist-btn" 
                            onclick="addToWishlist(<?= $book['id'] ?>, this)">
                        <i class="far fa-heart"></i>
                    </button>
                    <?php endif; ?>
                </div>
                <!-- КРАЙ НА book-actions -->
            </div>
        </div>
        <?php endwhile; ?>
        <?php $stmt->close(); ?>
    </div>
</div>

<!-- Най-нови книги -->
<!-- Най-нови книги -->
<div class="books-section new-books-section">
    <div class="section-header">
        <h2>Най-нови</h2>
        <div class="nav-buttons">
            <button id="new-prev-btn" class="nav-btn">‹</button>
            <button id="new-next-btn" class="nav-btn">›</button>
        </div>
    </div>
    <div class="view-all-link">
        <a href="new_books.php" class="view-all">Виж всички</a>
    </div>
    <div class="new-books-container">
        <div class="new-books-slider" id="new-books-slider">
            <?php
            $stmt = $conn->prepare("SELECT * FROM books WHERE is_new = 1 ORDER BY id DESC LIMIT 8");
            $stmt->execute();
            $new_books_result = $stmt->get_result();
            
            if ($new_books_result->num_rows > 0):
                while ($new_book = $new_books_result->fetch_assoc()):
            ?>
            <div class="new-book-card">
                <div class="book-image">
                    <img src="<?= htmlspecialchars($new_book['image']) ?>" 
                         alt="<?= htmlspecialchars($new_book['title']) ?>"
                         onerror="this.src='https://via.placeholder.com/140x180/DDD/333?text=Няма+снимка'">
                </div>
                <div class="book-info">
                    <h3>
                        <a href="book.php?id=<?= $new_book['id'] ?>" 
                           class="book-title-link-index">
                            <?= htmlspecialchars(mb_substr($new_book['title'], 0, 50, 'UTF-8')) . (mb_strlen($new_book['title'], 'UTF-8') > 50 ? '...' : '') ?>
                        </a>
                    </h3>
                    <p class="author">
                        <a href="author_books.php?author=<?= urlencode($new_book['author']) ?>" 
                           class="author-link-index">
                            <?= htmlspecialchars($new_book['author']) ?>
                        </a>
                    </p>
                    
                    
                    <!-- ТУК Е ПОПРАВКА: ЕДИН БЛОК book-actions -->
                    <div class="book-actions">
                        <button class="add-to-cart-btn-new" 
                                onclick="addToCartNew(<?= $new_book['id'] ?>, this)">
                            Добави в кошницата
                        </button>
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <button class="add-to-wishlist-btn" 
                                onclick="addToWishlist(<?= $new_book['id'] ?>, this)"
                               >
                            <i class="far fa-heart"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                    <!-- КРАЙ НА book-actions -->
                </div>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <div class="no-new-books">
                <p>Няма нови книги в момента.</p>
            </div>
            <?php endif; ?>
            <?php $stmt->close(); ?>
        </div>
    </div>
</div>

    <script>
    // СЛАЙДЕР ЗА НОВИ КНИГИ
    document.addEventListener('DOMContentLoaded', function() {
        const slider = document.getElementById('new-books-slider');
        const prevBtn = document.getElementById('new-prev-btn');
        const nextBtn = document.getElementById('new-next-btn');
        
        if (!slider || !prevBtn || !nextBtn) return;
        
        const newBookCards = document.querySelectorAll('.new-book-card');
        if (newBookCards.length === 0) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
            return;
        }
        
        let currentPosition = 0;
        const cardWidth = newBookCards[0].offsetWidth + 30;
        const maxScroll = Math.max(0, (newBookCards.length - 4) * cardWidth);
        
        if (maxScroll <= 0) {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
            return;
        }
        
        function moveSlider(direction) {
            if (direction === 'next') {
                currentPosition += cardWidth;
                if (currentPosition > maxScroll) {
                    currentPosition = maxScroll;
                }
            } else if (direction === 'prev') {
                currentPosition -= cardWidth;
                if (currentPosition < 0) {
                    currentPosition = 0;
                }
            }
            
            slider.style.transform = `translateX(-${currentPosition}px)`;
        }
        
        nextBtn.addEventListener('click', () => moveSlider('next'));
        prevBtn.addEventListener('click', () => moveSlider('prev'));
        
        slider.style.transition = 'transform 0.3s ease';
    });

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

// ФУНКЦИЯ ЗА ДОБАВЯНЕ В ЛЮБИМИ
function addToWishlist(bookId, button) {
    if (!button) return;
    
    // Проверка дали потребителят е влязъл
    <?php if (!isset($_SESSION['user_id'])): ?>
        showSimpleMessage('Моля, влезте в профила си за да добавите в любими!', 'info');
        return;
    <?php endif; ?>
    
    const originalIcon = button.innerHTML;
    const isAdded = button.classList.contains('added');
    
    if (isAdded) {
        // Премахване от любими
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        removeFromWishlist(bookId, button, originalIcon);
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
                button.title = 'Премахни от любими';
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

// ФУНКЦИЯ ЗА ПРЕМАХВАНЕ ОТ ЛЮБИМИ
function removeFromWishlist(bookId, button, originalIcon) {
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
            button.title = 'Добави в любими';
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

// ФУНКЦИЯ ЗА ПРОВЕРКА ДАЛИ КНИГА Е В ЛЮБИМИ (при зареждане на страницата)
function checkWishlistStatus() {
    <?php if (isset($_SESSION['user_id'])): ?>
    const userId = <?= $_SESSION['user_id'] ?>;
    
    // Взимаме всички ID-та на книги на страницата
    const bookIds = [];
    document.querySelectorAll('.add-to-wishlist-btn').forEach(btn => {
        const match = btn.getAttribute('onclick')?.match(/addToWishlist\((\d+)/);
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
                    const btn = document.querySelector(`[onclick*="addToWishlist(${bookId}"]`);
                    if (btn) {
                        btn.classList.add('added');
                        btn.innerHTML = '<i class="fas fa-heart"></i>';
                        btn.title = 'Премахни от любими';
                    }
                });
            }
        })
        .catch(error => console.error('Грешка при проверка:', error));
    }
    <?php endif; ?>
}

// Промяна на функцията за съобщения за да поддържа и 'info' тип
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

    </script>
    
    <!-- ИЗБРАНИ АВТОРИ -->
    <div class="featured-authors-section">
        <div class="featured-authors-header">
            <h2>Избрани автори</h2>
            <a href="authors.php" class="view-all-authors">Виж всички</a>
        </div>
        
        <div class="authors-grid">
            <?php
            $featured_stmt = $conn->prepare("
                SELECT a.*, COUNT(b.id) as book_count
                FROM authors a
                LEFT JOIN books b ON a.name = b.author
                WHERE a.is_featured = 1
                GROUP BY a.id
                ORDER BY a.name ASC
                LIMIT 8
            ");
            $featured_stmt->execute();
            $featured_result = $featured_stmt->get_result();
            
            if ($featured_result->num_rows > 0):
                while ($author = $featured_result->fetch_assoc()):
                    $author_name = $author['name'];
                    $book_count = $author['book_count'] ?: 0;
                    
                    if (!empty($author['image'])) {
                        $author_image = $author['image'];
                    } else {
                        $first_letter = mb_substr($author_name, 0, 1, 'UTF-8');
                        $author_image = 'https://ui-avatars.com/api/?name=' . urlencode($author_name) . 
                                       '&background=e60000&color=fff&size=100&bold=true&font-size=0.5';
                    }
            ?>
            <div class="author-card" onclick="window.location.href='author_books.php?author=<?= urlencode($author_name) ?>'">
                <img src="<?= htmlspecialchars($author_image) ?>" 
                     alt="<?= htmlspecialchars($author_name) ?>" 
                     class="author-image"
                     onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($author_name) ?>&background=e60000&color=fff&size=100'">
                <h3 class="author-name"><?= htmlspecialchars($author_name) ?></h3>
                <p class="author-books-count">
                    <?php if ($book_count > 0): ?>
                        <?= $book_count ?> <?= $book_count == 1 ? 'книга' : 'книги' ?>
                    <?php else: ?>
                        Очаквайте скоро
                    <?php endif; ?>
                </p>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <div class="no-authors">
                <p>Няма избрани автори в момента.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>