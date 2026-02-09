<?php
include 'config.php';

$page_title = 'Търсене';
include 'header.php';

// Получаване на ключовата дума
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (!empty($query)) {
    $search_query = "%" . $query . "%";
    
    // ТЪРСЕНЕ В ДВЕТЕ ТАБЛИЦИ С ВЗЕМАНЕ НА PROMO КНИГИ ОТ books
    $stmt = $conn->prepare("
        SELECT 
            id, 
            title, 
            author, 
            price, 
            image, 
            sales, 
            description,
            'book' as type,
            is_new,
            is_promo
        FROM books 
        WHERE (title LIKE ? OR author LIKE ?)
        
        UNION ALL
        
        SELECT 
            id, 
            title, 
            author, 
            price, 
            cover_image as image, 
            0 as sales, 
            description,
            'upcoming' as type,
            'upcoming' as is_new,
            0 as is_promo
        FROM upcoming_books 
        WHERE (title LIKE ? OR author LIKE ?) AND status = 'upcoming'
        
        ORDER BY 
            CASE 
                WHEN title LIKE ? THEN 1
                WHEN author LIKE ? THEN 2
                ELSE 3
            END,
            CASE 
                WHEN is_promo = 1 THEN 0  -- Промо книги първи
                ELSE 1
            END,
            type DESC,
            sales DESC
        LIMIT 100
    ");
    
    $stmt->bind_param("ssssss", 
        $search_query,  // books title
        $search_query,  // books author
        $search_query,  // upcoming_books title
        $search_query,  // upcoming_books author
        $search_query,  // ORDER BY title
        $search_query   // ORDER BY author
    );
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    $total_results = count($books);
    $stmt->close();
}
// Функция за подсветка на съвпаденията
function highlightMatch($text, $query) {
    if (empty($query) || empty($text)) {
        return $text;
    }
    
    $escapedQuery = preg_quote($query, '/');
    $pattern = "/($escapedQuery)/iu";
    
    return preg_replace($pattern, '<span class="highlight">$1</span>', $text);
}
?>

<style>
/* СТИЛОВЕ ЗА СТРАНИЦАТА С РЕЗУЛТАТИ ОТ ТЪРСЕНЕ */
.search-page {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
    min-height: 70vh;
}

.search-page h1 {
    font-size: 32px;
    color: #333;
    margin-bottom: 30px;
    text-align: center;
    font-weight: 600;
    border-bottom: 3px solid #e60000;
    padding-bottom: 15px;
}

.search-form-large {
    margin-bottom: 40px;
    max-width: 700px;
    margin-left: auto;
    margin-right: auto;
}

.search-box-large {
    display: flex;
    border: 2px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s;
}

.search-box-large:focus-within {
    border-color: #e60000;
    box-shadow: 0 0 0 3px rgba(230, 0, 0, 0.1);
}

.search-box-large input {
    flex: 1;
    padding: 15px 20px;
    border: none;
    font-size: 16px;
    outline: none;
    background: #f8f9fa;
}

.search-box-large button {
    background: #e60000;
    color: white;
    border: none;
    padding: 0 30px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
}

.search-box-large button:hover {
    background: #c40000;
}

.search-results-info {
    background: #f5f5f5;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
    font-size: 16px;
    border-left: 4px solid #e60000;
}

.search-results-info p {
    margin: 0;
    color: #333;
}

.search-results-info strong {
    color: #e60000;
}

/* РЕЗУЛТАТИ ОТ ТЪРСЕНЕТО */
.search-results {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.book-result-card {
    display: flex;
    background: white;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    transition: box-shadow 0.3s;
    position: relative;
}

.book-result-card:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    border-color: #e60000;
}

/* БАДЖ ЗА ПРЕДСТОЯЩИ КНИГИ */
.upcoming-badge-search {
    position: absolute;
    top: 15px;
    right: 15px;
    background: #ff9800;
    color: white;
    padding: 5px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: bold;
    z-index: 1;
}

.result-image-container {
    flex: 0 0 180px;
    margin-right: 25px;
    height: 240px;
    overflow: hidden;
    border-radius: 6px;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
}

.result-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s;
}

.book-result-card:hover .result-image-container img {
    transform: scale(1.05);
}

.result-info {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.result-header {
    margin-bottom: 15px;
}

.result-header h3 {
    margin: 0 0 10px 0;
    font-size: 22px;
    font-weight: 600;
    line-height: 1.3;
}

.result-header h3 a {
    color: #333;
    text-decoration: none;
}

.result-header h3 a:hover {
    color: #e60000;
    text-decoration: underline;
}

.price-tag {
    font-size: 24px;
    color: #e60000;
    font-weight: bold;
    margin-bottom: 15px;
}

.author-line {
    color: #666;
    margin-bottom: 15px;
    font-size: 16px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.author-line strong {
    color: #333;
}

.sales-info {
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.sales-count {
    background: #f0f0f0;
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 14px;
    color: #666;
}

.book-type-badge {
    background: #e60000;
    color: white;
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: bold;
}

.upcoming-type-badge {
    background: #ff9800;
    color: white;
    padding: 6px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: bold;
}

.action-buttons {
    margin-top: auto;
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.view-details-btn,
.add-btn {
    padding: 12px 30px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s;
    font-size: 15px;
    border: none;
    text-decoration: none;
    text-align: center;
    display: inline-block;
}

.view-details-btn {
    background: #007bff;
    color: white;
    min-width: 150px;
}

.view-details-btn:hover {
    background: #0056b3;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(0, 123, 255, 0.2);
}

.add-btn {
    background: #e60000;
    color: white;
    min-width: 150px;
}

.add-btn:hover {
    background: #c40000;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(230, 0, 0, 0.2);
}

.notify-btn {
    background: #ff9800;
    color: white;
    padding: 12px 30px;
    border-radius: 6px;
    border: none;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    min-width: 150px;
}

.notify-btn:hover {
    background: #f57c00;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(255, 152, 0, 0.2);
}

/* ПОДСВЕТКА НА СЪВПАДЕНИЯ */
.highlight {
    background: #fff0f0;
    color: #e60000;
    font-weight: bold;
    padding: 1px 4px;
    border-radius: 3px;
}

/* КОГАТО НЯМА РЕЗУЛТАТИ */
.no-results-container {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-top: 30px;
}

.no-results-container h3 {
    color: #333;
    margin-bottom: 15px;
    font-size: 22px;
}

.no-results-container p {
    color: #666;
    margin-bottom: 30px;
    font-size: 16px;
}

.suggestions-box {
    max-width: 500px;
    margin: 40px auto 0;
    text-align: left;
    background: white;
    padding: 20px;
    border-radius: 8px;
    border: 1px solid #ddd;
}

.suggestions-box h4 {
    color: #333;
    margin-bottom: 15px;
    font-size: 16px;
    font-weight: 600;
}

.suggestions-box ul {
    list-style: none;
    padding-left: 20px;
    margin: 0;
}

.suggestions-box li {
    margin-bottom: 10px;
    color: #666;
    position: relative;
}

.suggestions-box li:before {
    content: "•";
    color: #e60000;
    position: absolute;
    left: -15px;
}

/* КОГАТО НЯМА ТЪРСЕНА ДУМА */
.empty-search {
    text-align: center;
    padding: 60px 20px;
}

.empty-search h2 {
    color: #333;
    margin-bottom: 20px;
    font-size: 28px;
}

.empty-search p {
    color: #666;
    font-size: 16px;
    max-width: 600px;
    margin: 0 auto 40px;
}

/* ПРЕМАХНАТИ СТИЛОВЕ ЗА СТАТИСТИКА */
/* .search-stats, .stat-item-search, .stat-count-search, .stat-label-search - ПРЕМАХНАТИ */

/* АДАПТИВНОСТ */
@media (max-width: 768px) {
    .search-page {
        padding: 0 15px;
    }
    
    .search-box-large {
        flex-direction: column;
    }
    
    .search-box-large input {
        padding: 15px;
    }
    
    .search-box-large button {
        padding: 15px;
        width: 100%;
    }
    
    .book-result-card {
        flex-direction: column;
        padding: 20px;
    }
    
    .result-image-container {
        width: 150px;
        height: 200px;
        margin: 0 auto 20px auto;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .view-details-btn,
    .add-btn,
    .notify-btn {
        width: 100%;
    }
}

@media (max-width: 480px) {
    .search-page h1 {
        font-size: 24px;
    }
    
    .result-header h3 {
        font-size: 18px;
    }
    
    .price-tag {
        font-size: 20px;
    }
}

.add-to-cart-btn-new {
    background: #e60000;
    color: white;
    padding: 12px 30px;
    border-radius: 6px;
    border: none;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    min-width: 150px;
    text-decoration: none;
    text-align: center;
    display: inline-block;
}

.add-to-cart-btn-new:hover {
    background: #c40000;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(230, 0, 0, 0.2);
}
</style>

<div class="search-page">
    <h1>Търсене</h1>
    
    <?php if (!empty($query)): ?>
        <form method="get" action="search.php" class="search-form-large">
            <div class="search-box-large">
                <input type="text" 
                       name="q" 
                       value="<?= htmlspecialchars($query) ?>" 
                       placeholder="Въведете заглавие или автор..."
                       autocomplete="off"
                       required>
                <button type="submit">Търси</button>
            </div>
        </form>
        
        <?php if ($total_results > 0): ?>
            <!-- ПРЕМАХНАТА СТАТИСТИКА -->
            
            <div class="search-results">
                <?php foreach ($books as $book): 
                    $is_upcoming = ($book['type'] == 'upcoming');
                ?>
                <div class="book-result-card">
                    <!-- Бадж за предстоящи книги -->
                    <?php if ($is_upcoming): ?>
                        <div class="upcoming-badge-search">Предстояща</div>
                    <?php endif; ?>
                    
                    <div class="result-image-container">
                        <a href="<?= $is_upcoming ? 'upcomingbooks-details.php' : 'book.php' ?>?id=<?= $book['id'] ?>">
                            <?php if (!empty($book['image'])): ?>
                                <img src="<?= htmlspecialchars($book['image']) ?>" 
                                     alt="<?= htmlspecialchars($book['title']) ?>"
                                     onerror="this.onerror=null; this.src='https://via.placeholder.com/180x240/DDD/333?text=Няма+снимка'">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/180x240/DDD/333?text=Няма+снимка" 
                                     alt="<?= htmlspecialchars($book['title']) ?>">
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    <div class="result-info">
                        <div class="result-header">
                            <h3>
                                <a href="<?= $is_upcoming ? 'upcomingbooks-details.php' : 'book.php' ?>?id=<?= $book['id'] ?>">
                                    <?= highlightMatch(htmlspecialchars($book['title']), $query) ?>
                                </a>
                            </h3>
                            <div class="price-tag"><?= number_format($book['price'], 2) ?> лв.</div>
                        </div>
                        
                        <p class="author-line">
                            <strong>Автор:</strong> <?= highlightMatch(htmlspecialchars($book['author']), $query) ?>
                        </p>
                        
                        <div class="sales-info">
                            <?php if ($book['sales'] > 0 && !$is_upcoming): ?>
                                <span class="sales-count"><?= $book['sales'] ?> продажби</span>
                            <?php endif; ?>
                            
                            <span class="<?= $is_upcoming ? 'upcoming-type-badge' : 'book-type-badge' ?>">
                                <?= $is_upcoming ? 'Предстояща' : 'Налична' ?>
                            </span>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="<?= $is_upcoming ? 'upcomingbooks-details.php' : 'book.php' ?>?id=<?= $book['id'] ?>" 
                               class="view-details-btn">
                                Виж повече
                            </a>
                            
                            <?php if ($is_upcoming): ?>
                                <button class="notify-btn" 
                                        onclick="notifyAboutBook(<?= $book['id'] ?>)">
                                    Уведоми ме
                                </button>
                            <?php else: ?>
                                <button class="add-to-cart-btn-new" 
                    onclick="addToCartNew(<?= $book['id'] ?>, this)">
                Добави в кошницата
            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-results-container">
                <h3>Няма намерени резултати</h3>
                <p>Не намерихме книги за "<?= htmlspecialchars($query) ?>"</p>
                
                <div class="suggestions-box">
                    <h4>Съвети за търсене:</h4>
                    <ul>
                        <li>Проверете дали сте написали правилно заглавието</li>
                        <li>Опитайте с по-кратка версия на името на автора</li>
                        <li>Използвайте само главни букви за имената</li>
                        <li>Търсенето включва както налични, така и предстоящи книги</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="empty-search">
            <h2>Търсете книги</h2>
            <p>Въведете заглавие или автор в полето по-долу, за да намерите любимите си книги</p>
            
            <form method="get" action="search.php" class="search-form-large">
                <div class="search-box-large">
                    <input type="text" 
                           name="q" 
                           placeholder="Въведете заглавие на книга или име на автор"
                           autocomplete="off"
                           required>
                    <button type="submit">Търси</button>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>// ФУНКЦИЯ ЗА ДОБАВЯНЕ В КОШНИЦАТА
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
</script>
<!-- ПРЕМАХНАТ JavaScript КОД ЗА ТЪРСАЧКАТА В ДОЛУ -->
<?php include 'footer.php'; ?>