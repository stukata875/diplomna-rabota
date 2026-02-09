<?php
// Стартирай сесията ако не е стартирана
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Инициализирай количката ако не съществува
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Изчисли брояча (това ще бъде заместено от JavaScript)
$cartCount = array_sum($_SESSION['cart']);
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'BookStore'; ?></title>
    
    <!-- CSS файлове -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/footer.css">
   
    
    <!-- Динамично зареждане на CSS според страницата -->
    <?php if ($page_title == 'Начало'): ?>
        <link rel="stylesheet" href="assets/css/index.css">
    <?php elseif (basename($_SERVER['PHP_SELF']) == 'book.php'): ?>
        <link rel="stylesheet" href="assets/css/book.css">
    <?php endif; ?>
    
    <!-- Font Awesome за икони -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
    /* СТИЛОВЕ ЗА ТЪРСАЧКАТА В HEADER */
    .search-container {
        position: relative;
        flex: 1;
        max-width: 500px;
        margin: 0 30px;
    }
    
    .search-box {
        display: flex;
        width: 100%;
        position: relative;
    }
    
    .search-box input {
        flex: 1;
        padding: 12px 20px;
        padding-right: 50px;
        border: 2px solid #ddd;
        border-radius: 25px;
        font-size: 16px;
        outline: none;
        transition: all 0.3s;
    }
    
    .search-box input:focus {
        border-color: #e60000;
        box-shadow: 0 0 0 3px rgba(230, 0, 0, 0.1);
    }
    
    .search-box button {
        position: absolute;
        right: 5px;
        top: 5px;
        bottom: 5px;
        background: #e60000;
        color: white;
        border: none;
        border-radius: 20px;
        padding: 0 20px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .search-box button:hover {
        background: #c40000;
        transform: scale(1.05);
    }
    
    /* ПРЕДЛОЖЕНИЯ ЗА ТЪРСЕНЕ */
    .search-suggestions {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        z-index: 1000;
        max-height: 400px;
        overflow-y: auto;
        display: none;
    }
    
    .search-suggestions.active {
        display: block;
    }
    
    .suggestion-item {
        display: flex;
        align-items: center;
        padding: 10px 15px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background 0.2s;
    }
    
    .suggestion-item:hover {
        background: #f8f9fa;
    }
    
    .suggestion-item:last-child {
        border-bottom: none;
    }
    
    .suggestion-image {
        width: 40px;
        height: 55px;
        margin-right: 15px;
        border-radius: 4px;
        overflow: hidden;
        flex-shrink: 0;
    }
    
    .suggestion-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .suggestion-info {
        flex: 1;
        min-width: 0;
    }
    
    .suggestion-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 3px;
        font-size: 14px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .suggestion-author {
        font-size: 12px;
        color: #666;
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .suggestion-price {
        font-size: 14px;
        color: #e60000;
        font-weight: bold;
    }
    
    .no-suggestions {
        padding: 20px;
        text-align: center;
        color: #666;
        font-size: 14px;
    }
    
    /* МОБИЛЕН ВИД ЗА ТЪРСАЧКА */
    @media (max-width: 768px) {
        .search-container {
            order: 3;
            max-width: 100%;
            margin: 10px 0;
        }
        
        .header-top {
            flex-wrap: wrap;
        }
    }

    /* Анимации за съобщенията */
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }

    /* Анимация за брояча */
    @keyframes bounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.5); }
    }

    .ciela-header {
        width: 100%;
        background: #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        font-family: Arial, sans-serif;
    }

    .header-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 40px;
        border-bottom: 1px solid #eee;
    }

    .logo h1 {
        margin: 0;
        font-size: 28px;
        color: #e60000;
        border-bottom: none;
        padding: 0;
    }

    .logo a {
        text-decoration: none;
        color: inherit;
    }

    .search-container {
        flex: 1;
        margin: 0 40px;
        max-width: 600px;
        position: relative;
    }

    .search-box {
        display: flex;
        width: 100%;
    }

    .search-box input {
        flex: 1;
        padding: 10px 15px;
        border: 2px solid #ddd;
        border-radius: 4px 0 0 4px;
        font-size: 16px;
    }

    .search-box input:focus {
        outline: none;
        border-color: #e60000;
    }

    .search-box button {
        padding: 10px 25px;
        background: #e60000;
        color: white;
        border: none;
        border-radius: 0 4px 4px 0;
        cursor: pointer;
        font-weight: bold;
    }

    .search-box button:hover {
        background: #c40000;
    }

    /* ПОТРЕБИТЕЛСКИ ВРЪЗКИ */
    .user-actions {
        display: flex;
        align-items: center;
        gap: 25px;
    }

    .user-actions a {
        text-decoration: none;
        color: #333;
        font-weight: bold;
        padding: 8px 0;
        position: relative;
        transition: color 0.2s;
    }

    .user-actions a:hover {
        color: #e60000;
    }

    .cart-link {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .cart-count {
        background: #e60000;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    /* ГЛАВНО МЕНЮ */
    .main-nav {
        background: white;
        border-top: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
        position: relative;
    }

    .main-nav ul {
        display: flex;
        justify-content: center;
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .main-nav li {
        margin: 0;
        padding: 0;
        position: relative;
    }

    .main-nav a {
        display: block;
        padding: 15px 20px;
        text-decoration: none;
        color: black;
        font-weight: bold;
        font-size: 14px;
        text-transform: uppercase;
        border-right: 1px solid #eee;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .main-nav li:last-child a {
        border-right: none;
    }

    .main-nav a:hover {
        color: #e60000;
        background: #f9f9f9;
    }

    .main-nav a.promo {
        color: red;
        font-weight: bold;
    }

    /* МЕГАМЕНЮ ЗА УЧЕБНИЦИ - 4 КОЛОНИ */
    .nav-item-textbooks {
        position: static !important;
    }
    
    .dropdown-megamenu {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: white;
        border: 1px solid #ddd;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        z-index: 1000;
        display: none;
        padding: 25px 40px;
        border-radius: 0 0 8px 8px;
        box-sizing: border-box;
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        border-top: 3px solid #e60000;
    }

    .dropdown-megamenu.show {
        display: block;
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    /* БУФЕРНА ЗОНА - важен за да не се затваря бързо */
    .dropdown-megamenu::before {
        content: '';
        position: absolute;
        top: -20px;
        left: 0;
        right: 0;
        height: 20px;
        background: transparent;
    }

    /* ГРИД С 4 КОЛОНИ */
    .megamenu-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
    }

    .megamenu-column {
        padding-right: 20px;
    }

    .megamenu-column:not(:last-child) {
        border-right: 1px solid #eee;
    }

    .megamenu-column h3 {
        color: #e60000;
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 16px;
        font-weight: bold;
        text-transform: uppercase;
        padding-bottom: 10px;
        border-bottom: 2px solid #e60000;
    }

    .megamenu-column ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: block;
    }

    .megamenu-column li {
        margin: 0 0 12px 0;
        padding: 0;
    }

    .megamenu-column a {
        display: block;
        padding: 0;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        border-right: none;
        text-transform: none;
        font-weight: normal;
        transition: color 0.2s;
        line-height: 1.4;
    }

    .megamenu-column a:hover {
        color: #e60000;
        background: transparent;
        text-decoration: underline;
       
    }

    /* Специални стилове за подкатегории */
    .subcategory-title {
        font-weight: bold;
        color: #333;
        margin: 20px 0 10px 0;
        font-size: 14px;
        padding-bottom: 5px;
        border-bottom: 1px solid #eee;
    }

    .class-list {
        margin-left: 15px;
    }

    .class-list li {
        margin-bottom: 8px;
        position: relative;
    }

    .class-list a {
        font-size: 13px;
        color: #555;
        padding-left: 15px;
    }

    .class-list a:before {
        content: "•";
        color: #e60000;
        position: absolute;
        left: 0;
    }

    /* АКТИВЕН СТАТУС ЗА УЧЕБНИЦИ */
    .nav-item-textbooks:hover > a {
        color: #e60000;
        background: #f9f9f9;
    }

    /* Мобилна версия */
    .mobile-menu-btn {
        display: none;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        padding: 10px;
        color: #333;
    }

    /* АДАПТИВНИ СТИЛОВЕ */
    @media (max-width: 1200px) {
        .megamenu-wrapper {
            max-width: 1000px;
        }
    }

    @media (max-width: 1100px) {
        .header-top {
            padding: 15px 20px;
        }
        
        .main-nav a {
            padding: 15px 15px;
            font-size: 13px;
        }
        
        .dropdown-megamenu {
            padding: 20px;
        }
        
        .megamenu-wrapper {
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }
        
        .megamenu-column:not(:last-child) {
            border-right: none;
        }
        
        .megamenu-column:nth-child(odd) {
            border-right: 1px solid #eee;
            padding-right: 20px;
        }
    }

    @media (max-width: 900px) {
        .main-nav a {
            padding: 15px 12px;
            font-size: 12px;
        }
    }

    @media (max-width: 768px) {
        .header-top {
            flex-wrap: wrap;
            padding: 15px;
            gap: 15px;
        }
        
        .logo {
            order: 1;
        }
        
        .mobile-menu-btn {
            display: block;
            order: 2;
        }
        
        .search-container {
            order: 3;
            margin: 10px 0;
            width: 100%;
            max-width: 100%;
        }
        
        .user-actions {
            order: 4;
            width: 100%;
            justify-content: center;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        .main-nav {
            display: none;
        }
        
        .main-nav.active {
            display: block;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: white;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .main-nav ul {
            flex-direction: column;
        }
        
        .main-nav a {
            padding: 15px 20px;
            border-right: none;
            border-bottom: 1px solid #eee;
        }
        
        .dropdown-megamenu {
            position: static;
            width: 100%;
            box-shadow: none;
            border: none;
            padding: 15px;
            display: none;
            opacity: 1;
            visibility: visible;
            transform: none;
            transition: none;
            border-top: none;
        }
        
        .dropdown-megamenu.show {
            display: block;
        }
        
        .megamenu-wrapper {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .megamenu-column:not(:last-child) {
            border-right: none;
            border-bottom: 1px solid #eee;
            padding-bottom: 20px;
        }
        
        .megamenu-column:nth-child(odd) {
            border-right: none;
        }
        
        /* Скриваме буферната зона на мобилни */
        .dropdown-megamenu::before {
            display: none;
        }
    }
    /* СТИЛОВЕ ЗА ПРОФИЛНОТО МЕНЮ */
.profile-menu {
    position: relative;
}

.user-profile-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 15px;
    background: #f8f9fa;
    border-radius: 25px;
    transition: all 0.3s ease;
    text-decoration: none;
    color: #333;
}

.user-profile-link:hover {
    background: #e60000;
    color: white;
    text-decoration: none;
}

.profile-avatar {
    width: 32px;
    height: 32px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 18px;
}

.profile-email {
    font-size: 14px;
    font-weight: 500;
    max-width: 120px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.dropdown-arrow {
    font-size: 12px;
    transition: transform 0.3s ease;
}

.profile-menu:hover .dropdown-arrow {
    transform: rotate(180deg);
}

.profile-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    min-width: 220px;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: all 0.3s ease;
}

.profile-menu:hover .profile-dropdown {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.profile-dropdown a {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 20px;
    color: #333;
    text-decoration: none;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f0f0f0;
}

.profile-dropdown a:last-child {
    border-bottom: none;
}

.profile-dropdown a:hover {
    background: #f8f9fa;
    color: #e60000;
    padding-left: 25px;
}

.profile-dropdown a i {
    width: 20px;
    text-align: center;
}

.dropdown-divider {
    height: 1px;
    background: #f0f0f0;
    margin: 5px 0;
}

/* За мобилни устройства */
@media (max-width: 768px) {
    .user-profile-link {
        padding: 8px 10px;
    }
    
    .profile-email {
        display: none;
    }
    
    .profile-dropdown {
        position: fixed;
        top: auto;
        bottom: 0;
        left: 0;
        right: 0;
        width: 100%;
        border-radius: 20px 20px 0 0;
        transform: translateY(100%);
    }
    
    .profile-menu:hover .profile-dropdown {
        transform: translateY(0);
    }
    
    .profile-dropdown a {
        padding: 15px 20px;
        font-size: 16px;
    }
}
    </style>
    <script src="cart.js"></script>
</head>
<body>
    <?php if (isset($_SESSION['cart_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showNotification('<?= $_SESSION['cart_message'] ?>');
            <?php unset($_SESSION['cart_message']); ?>
        });
    </script>
    <?php endif; ?>
    
    <div class="ciela-header">
        <?php if (!empty($_SESSION['success_message'])): ?>
        <div id="toast">
            <?= $_SESSION['success_message'] ?>
        </div>
        <?php unset($_SESSION['success_message']); endif; ?>
        
        <div class="header-top">
            <!-- Logo -->
            <div class="logo">
                <a href="index.php">
                    <h1>BookStore</h1>
                </a>
            </div>
            
            <!-- Търсене -->
            <div class="search-container">
                <form id="search-form" class="search-box">
                    <input 
                        type="text" 
                        id="search-input" 
                        name="q" 
                        placeholder="Търсене на книги, автори, издателства" 
                        autocomplete="off"
                    >
                    <button type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <div id="search-suggestions" class="search-suggestions"></div>
            </div>
            
            <!-- Потребителски действия -->
        <!-- Потребителски действия -->
<div class="user-actions">
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Ако потребителят е влязъл -->
        <div class="profile-menu">
            <a href="profile.php" class="user-profile-link">
                <div class="profile-avatar"></div>
                <span class="profile-email">
                    <?php
                    if (isset($_SESSION['user_email'])) {
                        $email = $_SESSION['user_email'];
                        $username = explode('@', $email)[0];
                        echo htmlspecialchars($username);
                    }
                    ?>
                </span>
            </a>
            <div class="profile-dropdown">
                <a href="profile.php">Моят профил</a>
                <a href="orders.php">Моите поръчки</a>
                <a href="wishlist.php">Любими книги</a>
                <div class="dropdown-divider"></div>
                <a href="settings.php">Настройки</a>
                <a href="logout.php">Изход</a>
            </div>
        </div>
    <?php else: ?>
        <!-- Ако потребителят НЕ е влязъл -->
        <a href="login.php">Вход</a>
        <a href="register.php">Регистрация</a>
    <?php endif; ?>
    
    <!-- Кошница -->
    <a href="cart.php" class="cart-link">
        Кошница
        <span id="cart-count" class="cart-count">
            <?php
            $cart_count = 0;
            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    if (is_array($item) && isset($item['quantity'])) {
                        $cart_count += $item['quantity'];
                    } else {
                        $cart_count += 1;
                    }
                }
            }
            echo $cart_count > 99 ? '99+' : $cart_count;
            ?>
        </span>
    </a>
    
    <!-- Любими -->
    <a href="wishlist.php" class="wishlist-link">
        Любими
        <span class="wishlist-count" id="wishlist-count">0</span>
    </a>
</div>

        <?php
        // Зареди броя на продуктите от сесията
        $cart_count = 0;
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            // Правилно изчисляване на общия брой продукти
            foreach ($_SESSION['cart'] as $item) {
                // Проверка дали елементът е масив с quantity
                if (is_array($item) && isset($item['quantity'])) {
                    $cart_count += $item['quantity'];
                } else {
                    // Ако не е масив, приемаме, че е 1 продукт
                    $cart_count += 1;
                }
            }
        }
        echo $cart_count;
        ?>
    </span>
</a>
                </a>
            </div>
            
            <!-- Мобилно меню бутон -->
            <button class="mobile-menu-btn" onclick="toggleMenu()">☰</button>
        </div>
        
        <!-- Главно меню -->
<nav class="main-nav" id="mainNav">
    <ul>
        <!-- КНИГИ - с мегаменю -->
        <li class="nav-item-textbooks">
            <a href="books.php" class="has-dropdown">КНИГИ</a>
            <div class="dropdown-megamenu" id="booksDropdown">
                <div class="megamenu-wrapper">
                    <!-- КОЛОНА 1: Специални категории -->
                    <div class="megamenu-column">
                       
                        <ul class="class-list">
                           <li class="nav-item">
   <a href="upcoming-books.php">Предстоящи</a>
</li>
                            <li><a href="top_books.php">Топ заглавия</a></li>
                            <li><a href="new_books.php">Най-нови</a></li>
                            <li><a href="promo_books.php">Промо книги</a></li>
                            <li><a href="mandatory-reading.php">Задължително ученическо четене</a></li>
                        </ul>
                    </div>
                    
                    <!-- КОЛОНА 2: Образователна литература -->
                    <div class="megamenu-column">
                       
                        <ul class="class-list">
                            <li><a href="children_books.php">Детска литература</a></li>
                            <li><a href="ezoteric.php">Езотерика и духовни учения</a></li>
                            <li><a href="encyclopedias.php">Енциклопедии</a></li>
                            <li><a href="art.php">Изкуство</a></li>
                            <li><a href="economics.php">Икономика и бизнес</a></li>
                        </ul>
                    </div>
                    
                    <!-- КОЛОНА 3: Хуманитарна литература -->
                    <div class="megamenu-column">
                        
                        <ul class="class-list">
                            <li><a href="history.php">История и политика</a></li>
                            <li><a href="teen.php">Литература за тийнейджъри</a></li>
                            <li><a href="science.php">Научна литература</a></li>
                            <li><a href="psihology.php">Психология и философия</a></li>
                            <li><a href="dictionaries.php">Речници и разговорници</a></li>
                        </ul>
                    </div>
                    
                    <!-- КОЛОНА 4: Специализирана литература -->
                    <div class="megamenu-column">
                        
                        <ul class="class-list">
                            <li><a href="reference.php">Справочници</a></li>
                            <li><a href="tourism.php">Туризъм</a></li>
                            <li><a href="fiction.php">Художествена литература</a></li>
                            <li><a href="books.php?category=foreign">Чуждоезикова литература</a></li>
                            <li><a href="books.php?category=legal">Юридическа литература</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </li>
        
        <!-- УЧЕБНИЦИ - с пълно мегаменю с 4 колони -->
        <li class="nav-item-textbooks">
            <a href="textbooks.php" class="has-dropdown">УЧЕБНИЦИ</a>
            <div class="dropdown-megamenu" id="textbooksDropdown">
                <div class="megamenu-wrapper">
                    <!-- КОЛОНА 1: Детски градини и начална степен -->
                    <div class="megamenu-column">
                      
                        <ul class="class-list">
                            <li><a href="textbooks.php?class=preschool">Детски градини и предучилищни</a></li>
                            <li><a href="textbooks.php?class=1">1 клас</a></li>
                            <li><a href="textbooks.php?class=2">2 клас</a></li>
                            <li><a href="textbooks.php?class=3">3 клас</a></li>
                            <li><a href="textbooks.php?class=4">4 клас</a></li>
                        </ul>
                    </div>
                    
                    <!-- КОЛОНА 2: Средна степен -->
                    <div class="megamenu-column">
                       
                        <ul class="class-list">
                            <li><a href="textbooks.php?class=5">5 клас</a></li>
                            <li><a href="textbooks.php?class=7">7 клас</a></li>
                            <li><a href="textbooks.php?class=6">6 клас</a></li>
                            <li><a href="textbooks.php?class=8">8 клас</a></li>
                            <li><a href="textbooks.php?class=9">9 клас</a></li>
                        </ul>
                    </div>
                    
                    <!-- КОЛОНА 3: Горна степен -->
                    <div class="megamenu-column">
                       
                        <ul class="class-list">
                            <li><a href="textbooks.php?class=10">10 клас</a></li>
                            <li><a href="textbooks.php?class=11">11 клас</a></li>
                            <li><a href="textbooks.php?class=12">12 клас</a></li>
                        </ul>
                        
                        
                        <ul class="class-list">
                            <li><a href="textbooks.php?category=language">Чуждоезикови курсове</a></li>
                            <li><a href="textbooks.php?category=specialized">За профилирани училища</a></li>
                        </ul>
                    </div>
                    
                    <!-- КОЛОНА 4: Допълнителни материали -->
                    <div class="megamenu-column">
                       
                        <ul class="class-list">
                            <li><a href="textbooks.php?category=university">Учебници за ВУЗ</a></li>
                            <li><a href="textbooks.php?category=help">Помагала за училище</a></li>
                            <li><a href="textbooks.php?category=teacher">Книги за учителя</a></li>
                            <li><a href="textbooks.php?exam=maturity">Всичко за матурите от Просвета</a></li>
                            <li><a href="textbooks.php?exam=nvo">Отлична подготовка за НВО с Просвета - 4., 7., 10. клас</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </li>

        <!-- МУЗИКА - с мегаменю -->
<li class="nav-item-textbooks">
    <a href="music.php" class="has-dropdown">МУЗИКА</a>
    <div class="dropdown-megamenu" id="musicDropdown">
        <div class="megamenu-wrapper">
            <!-- КОЛОНА 1: Българска музика -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="music.php?category=bg-music">БГ музика</a></li>
                    <li><a href="music.php?category=bg-artists">БГ изпълнители</a></li>
                    <li><a href="music.php?category=children-songs">Детски приказки и песни</a></li>
                    <li><a href="music.php?category=jazz">Джаз</a></li>
                </ul>
            </div>
            
            <!-- КОЛОНА 2: Световни жанрове -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="music.php?category=disco">Диско</a></li>
                    <li><a href="music.php?category=electronic">Електронна</a></li>
                    <li><a href="music.php?category=classical">Класика</a></li>
                    <li><a href="music.php?category=metal">Метъл</a></li>
                    <li><a href="music.php?category=opera">Опера</a></li>
                </ul>
            </div>
            
            <!-- КОЛОНА 3: Популярни жанрове -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="music.php?category=pop">Поп</a></li>
                    <li><a href="music.php?category=pop-folk">Поп-фолк</a></li>
                    <li><a href="music.php?category=rap">Рап</a></li>
                    <li><a href="music.php?category=rock">Рок</a></li>
                    <li><a href="music.php?category=serbian">Сръбска</a></li>
                </ul>
            </div>
            
            <!-- КОЛОНА 4: Специални категории -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="music.php?category=techno">Техно</a></li>
                    <li><a href="music.php?category=film-music">Филмова музика</a></li>
                    <li><a href="music.php?category=folk">Фолк</a></li>
                    <li><a href="music.php?category=world-music">World Music</a></li>
                    <li><a href="music.php?category=vinyl">Грамофонни плочи - LP</a></li>
                </ul>
            </div>
        </div>
    </div>
</li>
<!-- ФИЛМИ - с мегаменю -->
<li class="nav-item-textbooks">
    <a href="movies.php" class="has-dropdown">ФИЛМИ</a>
    <div class="dropdown-megamenu" id="moviesDropdown">
        <div class="megamenu-wrapper">
            <!-- КОЛОНА 1: Категории -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="movies.php?category=new">Най-нови</a></li>
                    <li><a href="movies.php?category=bulgarian">Български филми</a></li>
                </ul>
            </div>
            
            <!-- КОЛОНА 2: Жанрове -->
            <div class="megamenu-column">
               
                <ul class="class-list">
                    <li><a href="movies.php?category=children">Детски филми</a></li>
                    <li><a href="movies.php?category=romantic">Романтични филми</a></li>
                </ul>
            </div>
            
            <!-- КОЛОНА 3: Формати -->
            <div class="megamenu-column">
               
                <ul class="class-list">
                    <li><a href="movies.php?format=bluray">Blu-Ray</a></li>
                    <li><a href="movies.php?format=dvd">DVD</a></li>
                </ul>
            </div>
        </div>
    </div>
</li>
        
       <!-- Е-КНИГИ - с мегаменю -->
<li class="nav-item-textbooks">
    <a href="ebooks.php" class="has-dropdown">Е-КНИГИ</a>
    <div class="dropdown-megamenu" id="ebooksDropdown">
        <div class="megamenu-wrapper">
            <!-- КОЛОНА 1: Общи категории -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="ebooks.php?category=new">Най-нови</a></li>
                    <li><a href="ebooks.php?category=children">Детска и юношеска литература</a></li>
                    <li><a href="ebooks.php?category=esoteric">Езотерика и духовни учения</a></li>
                    <li><a href="ebooks.php?category=encyclopedias">Енциклопедии</a></li>
                    <li><a href="ebooks.php?category=reference">Справочници и речници</a></li>
                    <li><a href="ebooks.php?category=art">Изкуство и култура</a></li>
                </ul>
            </div>
            
            <!-- КОЛОНА 2: Образователни и хоби -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="ebooks.php?category=business">Икономика и бизнес</a></li>
                    <li><a href="ebooks.php?category=interests">Интереси, хоби, забавления</a></li>
                    <li><a href="ebooks.php?category=it">Компютри и ИТ</a></li>
                    <li><a href="ebooks.php?category=science">Наука</a></li>
                    <li><a href="ebooks.php?category=selfimprovement">Самоусъвършенстване</a></li>
                </ul>
            </div>
            
            <!-- КОЛОНА 3: Специализирани -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="ebooks.php?category=textbooks">Учебници и помагала</a></li>
                    <li><a href="ebooks.php?category=fiction">Художествена и преводна литература</a></li>
                    <li><a href="ebooks.php?category=law">Юристика и право</a></li>
                </ul>
            </div>
        </div>
    </div>
</li>
        
        <!-- ПОДАРЪЦИ - с мегаменю -->
<li class="nav-item-textbooks">
    <a href="gifts.php" class="has-dropdown">ПОДАРЪЦИ</a>
    <div class="dropdown-megamenu" id="giftsDropdown">
        <div class="megamenu-wrapper">
            <!-- КОЛОНА 1: Аксесоари и ваучери -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="gifts.php?category=book-accessories">Аксесоари за книги</a></li>
                    <li><a href="gifts.php?category=gift-voucher">Ваучер за подарък</a></li>
                    <li><a href="gifts.php?category=greeting-cards">Картички</a></li>
                    <li><a href="gifts.php?category=keychains">Ключодържатели</a></li>
                    <li><a href="gifts.php?category=money-envelopes">Пликчета за пари и ваучери</a></li>
                </ul>
            </div>
            
            <!-- КОЛОНА 2: Подаръчни продукти -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="gifts.php?category=gift-bags">Подаръчни торби</a></li>
                    <li><a href="gifts.php?category=posters">Постери и плакати</a></li>
                    <li><a href="gifts.php?category=scratch-cards">Скреч карти</a></li>
                    <li><a href="gifts.php?category=cup">Чаши</a></li>
                    <li><a href="gifts.php?category=games">Шах и табла</a></li>
                </ul>
            </div>
        </div>
    </div>
</li>
<!-- ИГРИ И ИГРАЧКИ - с мегаменю -->
<li class="nav-item-textbooks">
    <a href="games.php" class="has-dropdown">ИГРИ И ИГРАЧКИ</a>
    <div class="dropdown-megamenu" id="gamesDropdown">
        <div class="megamenu-wrapper">
            <!-- КОЛОНА 1 -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="games.php?category=cards">Карти за игра</a></li>
                    <li><a href="games.php?category=constructors">Конструктори</a></li>
                    <li><a href="games.php?category=board-games">Настолни игри</a></li>
                </ul>
            </div>
            
            <!-- КОЛОНА 2 -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="games.php?category=plush">Плюшени играчки</a></li>
                    <li><a href="games.php?category=puzzles">Пъзели</a></li>
                </ul>
            </div>
        </div>
    </div>
</li>
        
<!-- ЗА УЧЕНИКА - с мегаменю -->
<li class="nav-item-textbooks">
    <a href="for-students.php" class="has-dropdown">ЗА УЧЕНИКА</a>
    <div class="dropdown-megamenu" id="studentsDropdown">
        <div class="megamenu-wrapper">
            <!-- КОЛОНА 1: Храни и чанти -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="for-students.php?category=bottles">Бутилки</a></li>
                    <li><a href="for-students.php?category=food-boxes">Кутии за храна</a></li>
                    <li><a href="for-students.php?category=pencil-cases">Несесери и портмонета</a></li>
                    <li><a href="for-students.php?category=backpacks">Раници и чанти</a></li>
                </ul>
            </div>
            
            <!-- КОЛОНА 2: Канцеларски материали -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="for-students.php?category=drawing">Рисуване</a></li>
                    <li><a href="for-students.php?category=notebooks">Тетрадки и класъри</a></li>
                    <li><a href="for-students.php?category=drawing-tools">Чертожни инструменти</a></li>
                </ul>
            </div>
        </div>
    </div>
</li>
       <!-- ЛАЙФСТАЙЛ И IT - с мегаменю -->
<li class="nav-item-textbooks">
    <a href="lifestyle.php" class="has-dropdown">ЛАЙФСТАЙЛ И IT</a>
    <div class="dropdown-megamenu" id="lifestyleDropdown">
        <div class="megamenu-wrapper">
            <!-- КОЛОНА 1: Технологии и аксесоари -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="lifestyle.php?category=cable-accessories">Аксесоари за кабели</a></li>
                    <li><a href="lifestyle.php?category=wireless-speakers">Безжични колонки</a></li>
                    <li><a href="lifestyle.php?category=power-banks">Външни батерии</a></li>
                    <li><a href="lifestyle.php?category=calendars">Календари</a></li>
                </ul>
            </div>
            
            <!-- КОЛОНА 2: Офис и персонални устройства -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="lifestyle.php?category=office-supplies">Канцеларски и офис принадлежности</a></li>
                    <li><a href="lifestyle.php?category=headphones">Слушалки</a></li>
                    <li><a href="lifestyle.php?category=usb-drives">USB памети</a></li>
                </ul>
            </div>
        </div>
    </div>
</li>
       <!-- парти аксесоари- с мегаменю -->
<li class="nav-item-textbooks">
    <a href="software.php" class="has-dropdown">парти аксесоари</a>
    <div class="dropdown-megamenu" id="softwareDropdown">
        <div class="megamenu-wrapper">
            <!-- КОЛОНА 1: Балони -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="software.php?category=balloons">Балони</a></li>
                    <li><a href="software.php?category=foil-balloons">Фолиеви балони</a></li>
                    <li><a href="software.php?category=latex-balloons">Латексови балони</a></li>
                    <li><a href="software.php?category=number-balloons">Балони цифри</a></li>
                    <li><a href="software.php?category=balloon-sets">Специални сетове</a></li>
                    <li><a href="software.php?category=balloon-accessories">Аксесоари за балони</a></li>
                </ul>
            </div>
            
            <!-- КОЛОНА 2: Свещи -->
            <div class="megamenu-column">
               
                <ul class="class-list">
                    <li><a href="software.php?category=candles">Свещички</a></li>
                    <li><a href="software.php?category=number-candles">Свещи цифри</a></li>
                    <li><a href="software.php?category=standard-candles">Стандартни свещи</a></li>
                    <li><a href="software.php?category=special-candles">Специални свещи</a></li>
                </ul>
            </div>
            
            <!-- КОЛОНА 3: Декорация за маса -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="software.php?category=cups">Чашки</a></li>
                    <li><a href="software.php?category=plates">Чинийки</a></li>
                    <li><a href="software.php?category=napkins">Салфетки</a></li>
                    <li><a href="software.php?category=cutlery">Прибори и сламки</a></li>
                    <li><a href="software.php?category=tablecloths">Покривки</a></li>
                    <li><a href="software.php?category=other-decorations">Други декорации</a></li>
                </ul>
            </div>
            
            <!-- КОЛОНА 4: Украса и забавление -->
            <div class="megamenu-column">
                
                <ul class="class-list">
                    <li><a href="software.php?category=garlands">Гирлянди</a></li>
                    <li><a href="software.php?category=banners">Надписи</a></li>
                    <li><a href="software.php?category=hanging-decorations">Висящи декорации</a></li>
                    <li><a href="software.php?category=party-fun">Забавление за парти</a></li>
                    <li><a href="software.php?category=piñatas">Пиняти</a></li>
                    <li><a href="software.php?category=guest-books">Книги за гости и пожелания</a></li>
                </ul>
            </div>
        </div>
    </div>
</li>
        
        <li><a href="promo.php" class="promo">Класации</a></li>
    </ul>
</nav>
    
<script>
// JavaScript за търсачката с предложения
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');
    const searchSuggestions = document.getElementById('search-suggestions');
    const searchContainer = document.querySelector('.search-container');
    let debounceTimer;
    
    // При показване на предложения
    function showSuggestions(suggestions) {
        if (suggestions.length === 0) {
            searchSuggestions.innerHTML = '<div class="no-suggestions">Няма намерени книги</div>';
            searchSuggestions.classList.add('active');
            return;
        }
        
        let html = '';
        suggestions.forEach(book => {
            // ДОБАВИ data-url вместо data-id
            html += `
                <div class="suggestion-item" data-url="${book.url}">
                    <div class="suggestion-image">
                        <img src="${book.image}" alt="${book.title}" onerror="this.src='https://via.placeholder.com/40x55?text=Book'">
                    </div>
                    <div class="suggestion-info">
                        <div class="suggestion-title">${book.title}</div>
                        <div class="suggestion-author">${book.author}</div>
                        <div class="suggestion-price">${book.price}</div>
                    </div>
                </div>
            `;
        });
        
        searchSuggestions.innerHTML = html;
        searchSuggestions.classList.add('active');
        
        // Добавяне на клик евенти за предложенията
        document.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', function() {
                const bookUrl = this.getAttribute('data-url');
                // Директно пренасочване към пълния URL
                window.location.href = bookUrl;
            });
        });
    }
    
    // Фетч на предложенията от сървъра
    function fetchSuggestions(query) {
        if (query.length < 2) {
            searchSuggestions.classList.remove('active');
            return;
        }
        
        fetch(`search_suggestions.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                showSuggestions(data);
            })
            .catch(error => {
                console.error('Грешка при търсене:', error);
                searchSuggestions.innerHTML = '<div class="no-suggestions">Грешка при зареждане</div>';
            });
    }
    
    // Дебаунс за търсене (изчаква 300ms след последното писане)
    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();
        
        debounceTimer = setTimeout(() => {
            fetchSuggestions(query);
        }, 100);
    });
    
    // Скриване на предложенията при клик извън тях
    document.addEventListener('click', function(e) {
        if (!searchContainer.contains(e.target)) {
            searchSuggestions.classList.remove('active');
        }
    });
    
    // Фокусиране върху полето за търсене показва предложения
    searchInput.addEventListener('focus', function() {
        const query = this.value.trim();
        if (query.length >= 2) {
            fetchSuggestions(query);
        }
    });
    
    // Навигация с клавиатура в предложенията
    let selectedIndex = -1;
    searchInput.addEventListener('keydown', function(e) {
        const suggestions = searchSuggestions.querySelectorAll('.suggestion-item');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, suggestions.length - 1);
            updateSelected(suggestions);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelected(suggestions);
        } else if (e.key === 'Enter' && selectedIndex >= 0) {
            e.preventDefault();
            suggestions[selectedIndex].click();
        } else if (e.key === 'Escape') {
            searchSuggestions.classList.remove('active');
        }
    });
    
    function updateSelected(suggestions) {
        suggestions.forEach((item, index) => {
            item.style.background = index === selectedIndex ? '#f0f0f0' : '';
        });
    }
    
    // Submit на формата за търсене
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const query = searchInput.value.trim();
        
        if (query.length > 0) {
            window.location.href = `search.php?q=${encodeURIComponent(query)}`;
        }
    });
});

// УПРАВЛЕНИЕ НА МЕГАМЕНЮТО ЗА КНИГИ
const booksMenuItem = document.querySelector('.nav-item-textbooks:first-child');
const booksDropdown = document.getElementById('booksDropdown');

let booksDropdownTimeout;
let isBooksDropdownOpen = false;
const booksDropdownCloseDelay = 500;

if (booksMenuItem && booksDropdown) {
    // При ховър върху "КНИГИ"
    booksMenuItem.addEventListener('mouseenter', function() {
        clearTimeout(booksDropdownTimeout);
        isBooksDropdownOpen = true;
        booksDropdown.classList.add('show');
    });
    
    // При ховър върху мегаменюто
    booksDropdown.addEventListener('mouseenter', function() {
        clearTimeout(booksDropdownTimeout);
        isBooksDropdownOpen = true;
    });
    
    // При напускане на "КНИГИ"
    booksMenuItem.addEventListener('mouseleave', function() {
        if (!isBooksDropdownOpen) {
            booksDropdownTimeout = setTimeout(() => {
                booksDropdown.classList.remove('show');
            }, booksDropdownCloseDelay);
        }
    });
    
    // При напускане на мегаменюто
    booksDropdown.addEventListener('mouseleave', function(e) {
        const relatedTarget = e.relatedTarget;
        if (!booksDropdown.contains(relatedTarget) && 
            !booksMenuItem.contains(relatedTarget)) {
            
            isBooksDropdownOpen = false;
            booksDropdownTimeout = setTimeout(() => {
                booksDropdown.classList.remove('show');
            }, booksDropdownCloseDelay);
        }
    });
    
    // За мобилни устройства - клик за отваряне/затваряне
    const booksLink = booksMenuItem.querySelector('a');
    booksLink.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            e.preventDefault();
            booksDropdown.classList.toggle('show');
        }
    });
    
    // Затваряне на мегаменюто при клик извън него
    document.addEventListener('click', function(e) {
        if (!booksMenuItem.contains(e.target) && 
            !booksDropdown.contains(e.target)) {
            booksDropdown.classList.remove('show');
            isBooksDropdownOpen = false;
        }
    });
    
    // Затваряне при скрол
    window.addEventListener('scroll', function() {
        booksDropdown.classList.remove('show');
        isBooksDropdownOpen = false;
    });
    
    // Допълнителна логика за следене на мишката
    document.addEventListener('mousemove', function(e) {
        const isInMenuItem = booksMenuItem.contains(e.target);
        const isInDropdown = booksDropdown.contains(e.target);
        
        if (isInMenuItem || isInDropdown) {
            clearTimeout(booksDropdownTimeout);
            isBooksDropdownOpen = true;
        } else if (isBooksDropdownOpen) {
            booksDropdownTimeout = setTimeout(() => {
                booksDropdown.classList.remove('show');
                isBooksDropdownOpen = false;
            }, booksDropdownCloseDelay);
        }
    });
}

// УПРАВЛЕНИЕ НА МЕГАМЕНЮТО ЗА УЧЕБНИЦИ
const textbooksMenuItem = document.querySelector('.nav-item-textbooks:nth-child(2)');
const textbooksDropdown = document.getElementById('textbooksDropdown');

let textbooksDropdownTimeout;
let isTextbooksDropdownOpen = false;
const textbooksDropdownCloseDelay = 500;

if (textbooksMenuItem && textbooksDropdown) {
    // При ховър върху "УЧЕБНИЦИ"
    textbooksMenuItem.addEventListener('mouseenter', function() {
        clearTimeout(textbooksDropdownTimeout);
        isTextbooksDropdownOpen = true;
        textbooksDropdown.classList.add('show');
    });
    
    // При ховър върху мегаменюто
    textbooksDropdown.addEventListener('mouseenter', function() {
        clearTimeout(textbooksDropdownTimeout);
        isTextbooksDropdownOpen = true;
    });
    
    // При напускане на "УЧЕБНИЦИ"
    textbooksMenuItem.addEventListener('mouseleave', function() {
        if (!isTextbooksDropdownOpen) {
            textbooksDropdownTimeout = setTimeout(() => {
                textbooksDropdown.classList.remove('show');
            }, textbooksDropdownCloseDelay);
        }
    });
    
    // При напускане на мегаменюто
    textbooksDropdown.addEventListener('mouseleave', function(e) {
        const relatedTarget = e.relatedTarget;
        if (!textbooksDropdown.contains(relatedTarget) && 
            !textbooksMenuItem.contains(relatedTarget)) {
            
            isTextbooksDropdownOpen = false;
            textbooksDropdownTimeout = setTimeout(() => {
                textbooksDropdown.classList.remove('show');
            }, textbooksDropdownCloseDelay);
        }
    });
    
    // За мобилни устройства - клик за отваряне/затваряне
    const textbooksLink = textbooksMenuItem.querySelector('a');
    textbooksLink.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            e.preventDefault();
            textbooksDropdown.classList.toggle('show');
        }
    });
    
    // Затваряне на мегаменюто при клик извън него
    document.addEventListener('click', function(e) {
        if (!textbooksMenuItem.contains(e.target) && 
            !textbooksDropdown.contains(e.target)) {
            textbooksDropdown.classList.remove('show');
            isTextbooksDropdownOpen = false;
        }
    });
    
    // Затваряне при скрол
    window.addEventListener('scroll', function() {
        textbooksDropdown.classList.remove('show');
        isTextbooksDropdownOpen = false;
    });
    
    // Допълнителна логика за следене на мишката
    document.addEventListener('mousemove', function(e) {
        const isInMenuItem = textbooksMenuItem.contains(e.target);
        const isInDropdown = textbooksDropdown.contains(e.target);
        
        if (isInMenuItem || isInDropdown) {
            clearTimeout(textbooksDropdownTimeout);
            isTextbooksDropdownOpen = true;
        } else if (isTextbooksDropdownOpen) {
            textbooksDropdownTimeout = setTimeout(() => {
                textbooksDropdown.classList.remove('show');
                isTextbooksDropdownOpen = false;
            }, textbooksDropdownCloseDelay);
        }
    });
}

// Функция за мобилно меню
function toggleMenu() {
    const nav = document.getElementById('mainNav');
    nav.classList.toggle('active');
    
    // Затваряне на всички мегаменюта при отваряне на мобилното меню
    if (nav.classList.contains('active')) {
        if (booksDropdown) booksDropdown.classList.remove('show');
        if (textbooksDropdown) textbooksDropdown.classList.remove('show');
    }
}

// Функции за количката (остават същите)
function updateCartCount() {
    fetch('get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = data.cart_count;
            }
        })
        .catch(error => console.error('Грешка при обновяване на кошницата:', error));
}

function addToCart(productId, button = null) {
    if (button) {
        const originalText = button.innerHTML;
        button.disabled = true;
        button.innerHTML = 'Добавя се...';
    }
    
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'id=' + productId
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Мрежова грешка');
        }
        return response.json();
    })
    .then(data => {
        if (button) {
            button.disabled = false;
            button.innerHTML = originalText;
        }
        
        if (data.success) {
            updateCartCount();
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        if (button) {
            button.disabled = false;
            button.innerHTML = originalText;
        }
        showNotification('Възникна грешка при добавянето', 'error');
    });
    
    return false;
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4CAF50' : '#f44336'};
        color: white;
        border-radius: 4px;
        z-index: 10000;
        animation: slideIn 0.3s ease-out;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Добавяне на CSS анимации
if (!document.querySelector('#notification-animations')) {
    const style = document.createElement('style');
    style.id = 'notification-animations';
    style.innerHTML = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    `;
    document.head.appendChild(style);
}

// Инициализация при зареждане
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
});

// УПРАВЛЕНИЕ НА МЕГАМЕНЮТО ЗА МУЗИКА
const musicMenuItem = document.querySelector('.nav-item-textbooks:nth-child(3)');
const musicDropdown = document.getElementById('musicDropdown');

let musicDropdownTimeout;
let isMusicDropdownOpen = false;
const musicDropdownCloseDelay = 500;

if (musicMenuItem && musicDropdown) {
    // При ховър върху "МУЗИКА"
    musicMenuItem.addEventListener('mouseenter', function() {
        clearTimeout(musicDropdownTimeout);
        isMusicDropdownOpen = true;
        musicDropdown.classList.add('show');
    });
    
    // При ховър върху мегаменюто
    musicDropdown.addEventListener('mouseenter', function() {
        clearTimeout(musicDropdownTimeout);
        isMusicDropdownOpen = true;
    });
    
    // При напускане на "МУЗИКА"
    musicMenuItem.addEventListener('mouseleave', function() {
        if (!isMusicDropdownOpen) {
            musicDropdownTimeout = setTimeout(() => {
                musicDropdown.classList.remove('show');
            }, musicDropdownCloseDelay);
        }
    });
    
    // При напускане на мегаменюто
    musicDropdown.addEventListener('mouseleave', function(e) {
        const relatedTarget = e.relatedTarget;
        if (!musicDropdown.contains(relatedTarget) && 
            !musicMenuItem.contains(relatedTarget)) {
            
            isMusicDropdownOpen = false;
            musicDropdownTimeout = setTimeout(() => {
                musicDropdown.classList.remove('show');
            }, musicDropdownCloseDelay);
        }
    });
    
    // За мобилни устройства - клик за отваряне/затваряне
    const musicLink = musicMenuItem.querySelector('a');
    musicLink.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            e.preventDefault();
            musicDropdown.classList.toggle('show');
        }
    });
    
    // Затваряне на мегаменюто при клик извън него
    document.addEventListener('click', function(e) {
        if (!musicMenuItem.contains(e.target) && 
            !musicDropdown.contains(e.target)) {
            musicDropdown.classList.remove('show');
            isMusicDropdownOpen = false;
        }
    });
    
    // Затваряне при скрол
    window.addEventListener('scroll', function() {
        musicDropdown.classList.remove('show');
        isMusicDropdownOpen = false;
    });
    
    // Допълнителна логика за следене на мишката
    document.addEventListener('mousemove', function(e) {
        const isInMenuItem = musicMenuItem.contains(e.target);
        const isInDropdown = musicDropdown.contains(e.target);
        
        if (isInMenuItem || isInDropdown) {
            clearTimeout(musicDropdownTimeout);
            isMusicDropdownOpen = true;
        } else if (isMusicDropdownOpen) {
            musicDropdownTimeout = setTimeout(() => {
                musicDropdown.classList.remove('show');
                isMusicDropdownOpen = false;
            }, musicDropdownCloseDelay);
        }
    });
}

// Обновете функцията toggleMenu за да затваря и музикалното меню
function toggleMenu() {
    const nav = document.getElementById('mainNav');
    nav.classList.toggle('active');
    
    // Затваряне на всички мегаменюта при отваряне на мобилното меню
    if (nav.classList.contains('active')) {
        if (booksDropdown) booksDropdown.classList.remove('show');
        if (textbooksDropdown) textbooksDropdown.classList.remove('show');
        if (musicDropdown) musicDropdown.classList.remove('show');
    }
}
// УПРАВЛЕНИЕ НА МЕГАМЕНЮТО ЗА ФИЛМИ
const moviesMenuItem = document.querySelector('.nav-item-textbooks:nth-child(4)');
const moviesDropdown = document.getElementById('moviesDropdown');

let moviesDropdownTimeout;
let isMoviesDropdownOpen = false;
const moviesDropdownCloseDelay = 500;

if (moviesMenuItem && moviesDropdown) {
    // При ховър върху "ФИЛМИ"
    moviesMenuItem.addEventListener('mouseenter', function() {
        clearTimeout(moviesDropdownTimeout);
        isMoviesDropdownOpen = true;
        moviesDropdown.classList.add('show');
    });
    
    // При ховър върху мегаменюто
    moviesDropdown.addEventListener('mouseenter', function() {
        clearTimeout(moviesDropdownTimeout);
        isMoviesDropdownOpen = true;
    });
    
    // При напускане на "ФИЛМИ"
    moviesMenuItem.addEventListener('mouseleave', function() {
        if (!isMoviesDropdownOpen) {
            moviesDropdownTimeout = setTimeout(() => {
                moviesDropdown.classList.remove('show');
            }, moviesDropdownCloseDelay);
        }
    });
    
    // При напускане на мегаменюто
    moviesDropdown.addEventListener('mouseleave', function(e) {
        const relatedTarget = e.relatedTarget;
        if (!moviesDropdown.contains(relatedTarget) && 
            !moviesMenuItem.contains(relatedTarget)) {
            
            isMoviesDropdownOpen = false;
            moviesDropdownTimeout = setTimeout(() => {
                moviesDropdown.classList.remove('show');
            }, moviesDropdownCloseDelay);
        }
    });
    
    // За мобилни устройства - клик за отваряне/затваряне
    const moviesLink = moviesMenuItem.querySelector('a');
    moviesLink.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            e.preventDefault();
            moviesDropdown.classList.toggle('show');
        }
    });
    
    // Затваряне на мегаменюто при клик извън него
    document.addEventListener('click', function(e) {
        if (!moviesMenuItem.contains(e.target) && 
            !moviesDropdown.contains(e.target)) {
            moviesDropdown.classList.remove('show');
            isMoviesDropdownOpen = false;
        }
    });
    
    // Затваряне при скрол
    window.addEventListener('scroll', function() {
        moviesDropdown.classList.remove('show');
        isMoviesDropdownOpen = false;
    });
    
    // Допълнителна логика за следене на мишката
    document.addEventListener('mousemove', function(e) {
        const isInMenuItem = moviesMenuItem.contains(e.target);
        const isInDropdown = moviesDropdown.contains(e.target);
        
        if (isInMenuItem || isInDropdown) {
            clearTimeout(moviesDropdownTimeout);
            isMoviesDropdownOpen = true;
        } else if (isMoviesDropdownOpen) {
            moviesDropdownTimeout = setTimeout(() => {
                moviesDropdown.classList.remove('show');
                isMoviesDropdownOpen = false;
            }, moviesDropdownCloseDelay);
        }
    });
}

// Обновете функцията toggleMenu за да затваря и филмовото меню
function toggleMenu() {
    const nav = document.getElementById('mainNav');
    nav.classList.toggle('active');
    
    // Затваряне на всички мегаменюта при отваряне на мобилното меню
    if (nav.classList.contains('active')) {
        if (booksDropdown) booksDropdown.classList.remove('show');
        if (textbooksDropdown) textbooksDropdown.classList.remove('show');
        if (musicDropdown) musicDropdown.classList.remove('show');
        if (moviesDropdown) moviesDropdown.classList.remove('show');
    }
}
// УПРАВЛЕНИЕ НА МЕГАМЕНЮТО ЗА Е-КНИГИ
const ebooksMenuItem = document.querySelector('.nav-item-textbooks:nth-child(5)');
const ebooksDropdown = document.getElementById('ebooksDropdown');

let ebooksDropdownTimeout;
let isEbooksDropdownOpen = false;
const ebooksDropdownCloseDelay = 500;

if (ebooksMenuItem && ebooksDropdown) {
    // При ховър върху "Е-КНИГИ"
    ebooksMenuItem.addEventListener('mouseenter', function() {
        clearTimeout(ebooksDropdownTimeout);
        isEbooksDropdownOpen = true;
        ebooksDropdown.classList.add('show');
    });
    
    // При ховър върху мегаменюто
    ebooksDropdown.addEventListener('mouseenter', function() {
        clearTimeout(ebooksDropdownTimeout);
        isEbooksDropdownOpen = true;
    });
    
    // При напускане на "Е-КНИГИ"
    ebooksMenuItem.addEventListener('mouseleave', function() {
        if (!isEbooksDropdownOpen) {
            ebooksDropdownTimeout = setTimeout(() => {
                ebooksDropdown.classList.remove('show');
            }, ebooksDropdownCloseDelay);
        }
    });
    
    // При напускане на мегаменюто
    ebooksDropdown.addEventListener('mouseleave', function(e) {
        const relatedTarget = e.relatedTarget;
        if (!ebooksDropdown.contains(relatedTarget) && 
            !ebooksMenuItem.contains(relatedTarget)) {
            
            isEbooksDropdownOpen = false;
            ebooksDropdownTimeout = setTimeout(() => {
                ebooksDropdown.classList.remove('show');
            }, ebooksDropdownCloseDelay);
        }
    });
    
    // За мобилни устройства - клик за отваряне/затваряне
    const ebooksLink = ebooksMenuItem.querySelector('a');
    ebooksLink.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            e.preventDefault();
            ebooksDropdown.classList.toggle('show');
        }
    });
    
    // Затваряне на мегаменюто при клик извън него
    document.addEventListener('click', function(e) {
        if (!ebooksMenuItem.contains(e.target) && 
            !ebooksDropdown.contains(e.target)) {
            ebooksDropdown.classList.remove('show');
            isEbooksDropdownOpen = false;
        }
    });
    
    // Затваряне при скрол
    window.addEventListener('scroll', function() {
        ebooksDropdown.classList.remove('show');
        isEbooksDropdownOpen = false;
    });
    
    // Допълнителна логика за следене на мишката
    document.addEventListener('mousemove', function(e) {
        const isInMenuItem = ebooksMenuItem.contains(e.target);
        const isInDropdown = ebooksDropdown.contains(e.target);
        
        if (isInMenuItem || isInDropdown) {
            clearTimeout(ebooksDropdownTimeout);
            isEbooksDropdownOpen = true;
        } else if (isEbooksDropdownOpen) {
            ebooksDropdownTimeout = setTimeout(() => {
                ebooksDropdown.classList.remove('show');
                isEbooksDropdownOpen = false;
            }, ebooksDropdownCloseDelay);
        }
    });
}

// Обновете функцията toggleMenu за да затваря и е-книги менюто
function toggleMenu() {
    const nav = document.getElementById('mainNav');
    nav.classList.toggle('active');
    
    // Затваряне на всички мегаменюта при отваряне на мобилното меню
    if (nav.classList.contains('active')) {
        if (booksDropdown) booksDropdown.classList.remove('show');
        if (textbooksDropdown) textbooksDropdown.classList.remove('show');
        if (musicDropdown) musicDropdown.classList.remove('show');
        if (moviesDropdown) moviesDropdown.classList.remove('show');
        if (ebooksDropdown) ebooksDropdown.classList.remove('show');
    }
}
// УПРАВЛЕНИЕ НА МЕГАМЕНЮТО ЗА ПОДАРЪЦИ
const giftsMenuItem = document.querySelector('.nav-item-textbooks:nth-child(6)');
const giftsDropdown = document.getElementById('giftsDropdown');

let giftsDropdownTimeout;
let isGiftsDropdownOpen = false;
const giftsDropdownCloseDelay = 500;

if (giftsMenuItem && giftsDropdown) {
    // При ховър върху "ПОДАРЪЦИ"
    giftsMenuItem.addEventListener('mouseenter', function() {
        clearTimeout(giftsDropdownTimeout);
        isGiftsDropdownOpen = true;
        giftsDropdown.classList.add('show');
    });
    
    // При ховър върху мегаменюто
    giftsDropdown.addEventListener('mouseenter', function() {
        clearTimeout(giftsDropdownTimeout);
        isGiftsDropdownOpen = true;
    });
    
    // При напускане на "ПОДАРЪЦИ"
    giftsMenuItem.addEventListener('mouseleave', function() {
        if (!isGiftsDropdownOpen) {
            giftsDropdownTimeout = setTimeout(() => {
                giftsDropdown.classList.remove('show');
            }, giftsDropdownCloseDelay);
        }
    });
    
    // При напускане на мегаменюто
    giftsDropdown.addEventListener('mouseleave', function(e) {
        const relatedTarget = e.relatedTarget;
        if (!giftsDropdown.contains(relatedTarget) && 
            !giftsMenuItem.contains(relatedTarget)) {
            
            isGiftsDropdownOpen = false;
            giftsDropdownTimeout = setTimeout(() => {
                giftsDropdown.classList.remove('show');
            }, giftsDropdownCloseDelay);
        }
    });
    
    // За мобилни устройства - клик за отваряне/затваряне
    const giftsLink = giftsMenuItem.querySelector('a');
    giftsLink.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            e.preventDefault();
            giftsDropdown.classList.toggle('show');
        }
    });
    
    // Затваряне на мегаменюто при клик извън него
    document.addEventListener('click', function(e) {
        if (!giftsMenuItem.contains(e.target) && 
            !giftsDropdown.contains(e.target)) {
            giftsDropdown.classList.remove('show');
            isGiftsDropdownOpen = false;
        }
    });
    
    // Затваряне при скрол
    window.addEventListener('scroll', function() {
        giftsDropdown.classList.remove('show');
        isGiftsDropdownOpen = false;
    });
    
    // Допълнителна логика за следене на мишката
    document.addEventListener('mousemove', function(e) {
        const isInMenuItem = giftsMenuItem.contains(e.target);
        const isInDropdown = giftsDropdown.contains(e.target);
        
        if (isInMenuItem || isInDropdown) {
            clearTimeout(giftsDropdownTimeout);
            isGiftsDropdownOpen = true;
        } else if (isGiftsDropdownOpen) {
            giftsDropdownTimeout = setTimeout(() => {
                giftsDropdown.classList.remove('show');
                isGiftsDropdownOpen = false;
            }, giftsDropdownCloseDelay);
        }
    });
}

// Обновете функцията toggleMenu за да затваря и подаръци менюто
function toggleMenu() {
    const nav = document.getElementById('mainNav');
    nav.classList.toggle('active');
    
    // Затваряне на всички мегаменюта при отваряне на мобилното меню
    if (nav.classList.contains('active')) {
        if (booksDropdown) booksDropdown.classList.remove('show');
        if (textbooksDropdown) textbooksDropdown.classList.remove('show');
        if (musicDropdown) musicDropdown.classList.remove('show');
        if (moviesDropdown) moviesDropdown.classList.remove('show');
        if (ebooksDropdown) ebooksDropdown.classList.remove('show');
        if (giftsDropdown) giftsDropdown.classList.remove('show');
    }
}
// УПРАВЛЕНИЕ НА МЕГАМЕНЮТО ЗА ИГРИ И ИГРАЧКИ
const gamesMenuItem = document.querySelector('.nav-item-textbooks:nth-child(7)');
const gamesDropdown = document.getElementById('gamesDropdown');

let gamesDropdownTimeout;
let isGamesDropdownOpen = false;
const gamesDropdownCloseDelay = 500;

if (gamesMenuItem && gamesDropdown) {
    // При ховър върху "ИГРИ И ИГРАЧКИ"
    gamesMenuItem.addEventListener('mouseenter', function() {
        clearTimeout(gamesDropdownTimeout);
        isGamesDropdownOpen = true;
        gamesDropdown.classList.add('show');
    });
    
    // При ховър върху мегаменюто
    gamesDropdown.addEventListener('mouseenter', function() {
        clearTimeout(gamesDropdownTimeout);
        isGamesDropdownOpen = true;
    });
    
    // При напускане на "ИГРИ И ИГРАЧКИ"
    gamesMenuItem.addEventListener('mouseleave', function() {
        if (!isGamesDropdownOpen) {
            gamesDropdownTimeout = setTimeout(() => {
                gamesDropdown.classList.remove('show');
            }, gamesDropdownCloseDelay);
        }
    });
    
    // При напускане на мегаменюто
    gamesDropdown.addEventListener('mouseleave', function(e) {
        const relatedTarget = e.relatedTarget;
        if (!gamesDropdown.contains(relatedTarget) && 
            !gamesMenuItem.contains(relatedTarget)) {
            
            isGamesDropdownOpen = false;
            gamesDropdownTimeout = setTimeout(() => {
                gamesDropdown.classList.remove('show');
            }, gamesDropdownCloseDelay);
        }
    });
    
    // За мобилни устройства - клик за отваряне/затваряне
    const gamesLink = gamesMenuItem.querySelector('a');
    gamesLink.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            e.preventDefault();
            gamesDropdown.classList.toggle('show');
        }
    });
    
    // Затваряне на мегаменюто при клик извън него
    document.addEventListener('click', function(e) {
        if (!gamesMenuItem.contains(e.target) && 
            !gamesDropdown.contains(e.target)) {
            gamesDropdown.classList.remove('show');
            isGamesDropdownOpen = false;
        }
    });
    
    // Затваряне при скрол
    window.addEventListener('scroll', function() {
        gamesDropdown.classList.remove('show');
        isGamesDropdownOpen = false;
    });
    
    // Допълнителна логика за следене на мишката
    document.addEventListener('mousemove', function(e) {
        const isInMenuItem = gamesMenuItem.contains(e.target);
        const isInDropdown = gamesDropdown.contains(e.target);
        
        if (isInMenuItem || isInDropdown) {
            clearTimeout(gamesDropdownTimeout);
            isGamesDropdownOpen = true;
        } else if (isGamesDropdownOpen) {
            gamesDropdownTimeout = setTimeout(() => {
                gamesDropdown.classList.remove('show');
                isGamesDropdownOpen = false;
            }, gamesDropdownCloseDelay);
        }
    });
}

// Обновете функцията toggleMenu за да затваря и игри менюто
function toggleMenu() {
    const nav = document.getElementById('mainNav');
    nav.classList.toggle('active');
    
    // Затваряне на всички мегаменюта при отваряне на мобилното меню
    if (nav.classList.contains('active')) {
        if (booksDropdown) booksDropdown.classList.remove('show');
        if (textbooksDropdown) textbooksDropdown.classList.remove('show');
        if (musicDropdown) musicDropdown.classList.remove('show');
        if (moviesDropdown) moviesDropdown.classList.remove('show');
        if (ebooksDropdown) ebooksDropdown.classList.remove('show');
        if (giftsDropdown) giftsDropdown.classList.remove('show');
        if (gamesDropdown) gamesDropdown.classList.remove('show');
    }
}
// УПРАВЛЕНИЕ НА МЕГАМЕНЮТО ЗА ЗА УЧЕНИКА
const studentsMenuItem = document.querySelector('.nav-item-textbooks:nth-child(8)');
const studentsDropdown = document.getElementById('studentsDropdown');

let studentsDropdownTimeout;
let isStudentsDropdownOpen = false;
const studentsDropdownCloseDelay = 500;

if (studentsMenuItem && studentsDropdown) {
    // При ховър върху "ЗА УЧЕНИКА"
    studentsMenuItem.addEventListener('mouseenter', function() {
        clearTimeout(studentsDropdownTimeout);
        isStudentsDropdownOpen = true;
        studentsDropdown.classList.add('show');
    });
    
    // При ховър върху мегаменюто
    studentsDropdown.addEventListener('mouseenter', function() {
        clearTimeout(studentsDropdownTimeout);
        isStudentsDropdownOpen = true;
    });
    
    // При напускане на "ЗА УЧЕНИКА"
    studentsMenuItem.addEventListener('mouseleave', function() {
        if (!isStudentsDropdownOpen) {
            studentsDropdownTimeout = setTimeout(() => {
                studentsDropdown.classList.remove('show');
            }, studentsDropdownCloseDelay);
        }
    });
    
    // При напускане на мегаменюто
    studentsDropdown.addEventListener('mouseleave', function(e) {
        const relatedTarget = e.relatedTarget;
        if (!studentsDropdown.contains(relatedTarget) && 
            !studentsMenuItem.contains(relatedTarget)) {
            
            isStudentsDropdownOpen = false;
            studentsDropdownTimeout = setTimeout(() => {
                studentsDropdown.classList.remove('show');
            }, studentsDropdownCloseDelay);
        }
    });
    
    // За мобилни устройства - клик за отваряне/затваряне
    const studentsLink = studentsMenuItem.querySelector('a');
    studentsLink.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            e.preventDefault();
            studentsDropdown.classList.toggle('show');
        }
    });
    
    // Затваряне на мегаменюто при клик извън него
    document.addEventListener('click', function(e) {
        if (!studentsMenuItem.contains(e.target) && 
            !studentsDropdown.contains(e.target)) {
            studentsDropdown.classList.remove('show');
            isStudentsDropdownOpen = false;
        }
    });
    
    // Затваряне при скрол
    window.addEventListener('scroll', function() {
        studentsDropdown.classList.remove('show');
        isStudentsDropdownOpen = false;
    });
    
    // Допълнителна логика за следене на мишката
    document.addEventListener('mousemove', function(e) {
        const isInMenuItem = studentsMenuItem.contains(e.target);
        const isInDropdown = studentsDropdown.contains(e.target);
        
        if (isInMenuItem || isInDropdown) {
            clearTimeout(studentsDropdownTimeout);
            isStudentsDropdownOpen = true;
        } else if (isStudentsDropdownOpen) {
            studentsDropdownTimeout = setTimeout(() => {
                studentsDropdown.classList.remove('show');
                isStudentsDropdownOpen = false;
            }, studentsDropdownCloseDelay);
        }
    });
}

// Обновете функцията toggleMenu за да затваря и ученика менюто
function toggleMenu() {
    const nav = document.getElementById('mainNav');
    nav.classList.toggle('active');
    
    // Затваряне на всички мегаменюта при отваряне на мобилното меню
    if (nav.classList.contains('active')) {
        if (booksDropdown) booksDropdown.classList.remove('show');
        if (textbooksDropdown) textbooksDropdown.classList.remove('show');
        if (musicDropdown) musicDropdown.classList.remove('show');
        if (moviesDropdown) moviesDropdown.classList.remove('show');
        if (ebooksDropdown) ebooksDropdown.classList.remove('show');
        if (giftsDropdown) giftsDropdown.classList.remove('show');
        if (gamesDropdown) gamesDropdown.classList.remove('show');
        if (studentsDropdown) studentsDropdown.classList.remove('show');
    }
}
// УПРАВЛЕНИЕ НА МЕГАМЕНЮТО ЗА ЛАЙФСТАЙЛ И IT
const lifestyleMenuItem = document.querySelector('.nav-item-textbooks:nth-child(9)');
const lifestyleDropdown = document.getElementById('lifestyleDropdown');

let lifestyleDropdownTimeout;
let isLifestyleDropdownOpen = false;
const lifestyleDropdownCloseDelay = 500;

if (lifestyleMenuItem && lifestyleDropdown) {
    // При ховър върху "ЛАЙФСТАЙЛ И IT"
    lifestyleMenuItem.addEventListener('mouseenter', function() {
        clearTimeout(lifestyleDropdownTimeout);
        isLifestyleDropdownOpen = true;
        lifestyleDropdown.classList.add('show');
    });
    
    // При ховър върху мегаменюто
    lifestyleDropdown.addEventListener('mouseenter', function() {
        clearTimeout(lifestyleDropdownTimeout);
        isLifestyleDropdownOpen = true;
    });
    
    // При напускане на "ЛАЙФСТАЙЛ И IT"
    lifestyleMenuItem.addEventListener('mouseleave', function() {
        if (!isLifestyleDropdownOpen) {
            lifestyleDropdownTimeout = setTimeout(() => {
                lifestyleDropdown.classList.remove('show');
            }, lifestyleDropdownCloseDelay);
        }
    });
    
    // При напускане на мегаменюто
    lifestyleDropdown.addEventListener('mouseleave', function(e) {
        const relatedTarget = e.relatedTarget;
        if (!lifestyleDropdown.contains(relatedTarget) && 
            !lifestyleMenuItem.contains(relatedTarget)) {
            
            isLifestyleDropdownOpen = false;
            lifestyleDropdownTimeout = setTimeout(() => {
                lifestyleDropdown.classList.remove('show');
            }, lifestyleDropdownCloseDelay);
        }
    });
    
    // За мобилни устройства - клик за отваряне/затваряне
    const lifestyleLink = lifestyleMenuItem.querySelector('a');
    lifestyleLink.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            e.preventDefault();
            lifestyleDropdown.classList.toggle('show');
        }
    });
    
    // Затваряне на мегаменюто при клик извън него
    document.addEventListener('click', function(e) {
        if (!lifestyleMenuItem.contains(e.target) && 
            !lifestyleDropdown.contains(e.target)) {
            lifestyleDropdown.classList.remove('show');
            isLifestyleDropdownOpen = false;
        }
    });
    
    // Затваряне при скрол
    window.addEventListener('scroll', function() {
        lifestyleDropdown.classList.remove('show');
        isLifestyleDropdownOpen = false;
    });
    
    // Допълнителна логика за следене на мишката
    document.addEventListener('mousemove', function(e) {
        const isInMenuItem = lifestyleMenuItem.contains(e.target);
        const isInDropdown = lifestyleDropdown.contains(e.target);
        
        if (isInMenuItem || isInDropdown) {
            clearTimeout(lifestyleDropdownTimeout);
            isLifestyleDropdownOpen = true;
        } else if (isLifestyleDropdownOpen) {
            lifestyleDropdownTimeout = setTimeout(() => {
                lifestyleDropdown.classList.remove('show');
                isLifestyleDropdownOpen = false;
            }, lifestyleDropdownCloseDelay);
        }
    });
}

// Обновете функцията toggleMenu за да затваря и лайфстайл менюто
function toggleMenu() {
    const nav = document.getElementById('mainNav');
    nav.classList.toggle('active');
    
    // Затваряне на всички мегаменюта при отваряне на мобилното меню
    if (nav.classList.contains('active')) {
        if (booksDropdown) booksDropdown.classList.remove('show');
        if (textbooksDropdown) textbooksDropdown.classList.remove('show');
        if (musicDropdown) musicDropdown.classList.remove('show');
        if (moviesDropdown) moviesDropdown.classList.remove('show');
        if (ebooksDropdown) ebooksDropdown.classList.remove('show');
        if (giftsDropdown) giftsDropdown.classList.remove('show');
        if (gamesDropdown) gamesDropdown.classList.remove('show');
        if (studentsDropdown) studentsDropdown.classList.remove('show');
        if (lifestyleDropdown) lifestyleDropdown.classList.remove('show');
    }
}
// УПРАВЛЕНИЕ НА МЕГАМЕНЮТО ЗА парти аксесоари
const softwareMenuItem = document.querySelector('.nav-item-textbooks:nth-child(10)');
const softwareDropdown = document.getElementById('softwareDropdown');

let softwareDropdownTimeout;
let isSoftwareDropdownOpen = false;
const softwareDropdownCloseDelay = 500;

if (softwareMenuItem && softwareDropdown) {
    // При ховър върху "СОФТУЕР"
    softwareMenuItem.addEventListener('mouseenter', function() {
        clearTimeout(softwareDropdownTimeout);
        isSoftwareDropdownOpen = true;
        softwareDropdown.classList.add('show');
    });
    
    // При ховър върху мегаменюто
    softwareDropdown.addEventListener('mouseenter', function() {
        clearTimeout(softwareDropdownTimeout);
        isSoftwareDropdownOpen = true;
    });
    
    // При напускане на "СОФТУЕР"
    softwareMenuItem.addEventListener('mouseleave', function() {
        if (!isSoftwareDropdownOpen) {
            softwareDropdownTimeout = setTimeout(() => {
                softwareDropdown.classList.remove('show');
            }, softwareDropdownCloseDelay);
        }
    });
    
    // При напускане на мегаменюто
    softwareDropdown.addEventListener('mouseleave', function(e) {
        const relatedTarget = e.relatedTarget;
        if (!softwareDropdown.contains(relatedTarget) && 
            !softwareMenuItem.contains(relatedTarget)) {
            
            isSoftwareDropdownOpen = false;
            softwareDropdownTimeout = setTimeout(() => {
                softwareDropdown.classList.remove('show');
            }, softwareDropdownCloseDelay);
        }
    });
    
    // За мобилни устройства - клик за отваряне/затваряне
    const softwareLink = softwareMenuItem.querySelector('a');
    softwareLink.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            e.preventDefault();
            softwareDropdown.classList.toggle('show');
        }
    });
    
    // Затваряне на мегаменюто при клик извън него
    document.addEventListener('click', function(e) {
        if (!softwareMenuItem.contains(e.target) && 
            !softwareDropdown.contains(e.target)) {
            softwareDropdown.classList.remove('show');
            isSoftwareDropdownOpen = false;
        }
    });
    
    // Затваряне при скрол
    window.addEventListener('scroll', function() {
        softwareDropdown.classList.remove('show');
        isSoftwareDropdownOpen = false;
    });
    
    // Допълнителна логика за следене на мишката
    document.addEventListener('mousemove', function(e) {
        const isInMenuItem = softwareMenuItem.contains(e.target);
        const isInDropdown = softwareDropdown.contains(e.target);
        
        if (isInMenuItem || isInDropdown) {
            clearTimeout(softwareDropdownTimeout);
            isSoftwareDropdownOpen = true;
        } else if (isSoftwareDropdownOpen) {
            softwareDropdownTimeout = setTimeout(() => {
                softwareDropdown.classList.remove('show');
                isSoftwareDropdownOpen = false;
            }, softwareDropdownCloseDelay);
        }
    });
}

// Обновете функцията toggleMenu за да затваря и софтуер менюто
function toggleMenu() {
    const nav = document.getElementById('mainNav');
    nav.classList.toggle('active');
    
    // Затваряне на всички мегаменюта при отваряне на мобилното меню
    if (nav.classList.contains('active')) {
        if (booksDropdown) booksDropdown.classList.remove('show');
        if (textbooksDropdown) textbooksDropdown.classList.remove('show');
        if (musicDropdown) musicDropdown.classList.remove('show');
        if (moviesDropdown) moviesDropdown.classList.remove('show');
        if (ebooksDropdown) ebooksDropdown.classList.remove('show');
        if (giftsDropdown) giftsDropdown.classList.remove('show');
        if (gamesDropdown) gamesDropdown.classList.remove('show');
        if (studentsDropdown) studentsDropdown.classList.remove('show');
        if (lifestyleDropdown) lifestyleDropdown.classList.remove('show');
        if (softwareDropdown) softwareDropdown.classList.remove('show');
    }
}

// ФУНКЦИИ ЗА КОШНИЦАТА

// 1. Добавяне в кошницата (AJAX)
function addToCart(productId, button = null) {
    if (button) {
        const originalText = button.innerHTML;
        const originalColor = button.style.backgroundColor;
        
        button.innerHTML = 'Добавя се...';
        button.disabled = true;
        button.style.backgroundColor = '#999';
    }
    
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (button) {
            button.innerHTML = 'Добави в кошницата';
            button.disabled = false;
            button.style.backgroundColor = originalColor;
        }
        
        if (data.success) {
            updateCartCount();
            showNotification('✓ Книгата е добавена в кошницата!', 'success');
        } else {
            showNotification('✗ ' + (data.message || 'Грешка при добавяне'), 'error');
        }
    })
    .catch(error => {
        console.error('Грешка:', error);
        if (button) {
            button.innerHTML = 'Добави в кошницата';
            button.disabled = false;
            button.style.backgroundColor = originalColor;
        }
        showNotification('✗ Възникна грешка', 'error');
    });
}

// 2. Обновяване на брояча на кошницата
function updateCartCount() {
    fetch('get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = data.cart_count;
                
                // Анимация на брояча
                cartCountElement.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    cartCountElement.style.transform = 'scale(1)';
                }, 300);
            }
        })
        .catch(error => console.error('Грешка при обновяване на кошницата:', error));
}

// 3. Показване на известия
function showNotification(message, type = 'success') {
    // Проверка за съществуващо известие
    let existingNotification = document.querySelector('.global-notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Създаване на ново известие
    const notification = document.createElement('div');
    notification.className = `global-notification ${type}`;
    notification.innerHTML = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4CAF50' : '#f44336'};
        color: white;
        border-radius: 6px;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideInRight 0.3s ease;
        font-family: Arial, sans-serif;
        max-width: 300px;
    `;
    
    document.body.appendChild(notification);
    
    // Автоматично скриване след 3 секунди
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// 4. Добавяне на CSS анимации ако липсват
if (!document.querySelector('#global-animations')) {
    const style = document.createElement('style');
    style.id = 'global-animations';
    style.textContent = `
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        
        /* Анимация за брояча на кошницата */
        .cart-count {
            transition: transform 0.3s ease;
        }
    `;
    document.head.appendChild(style);
}

// 5. Инициализация при зареждане на страницата
document.addEventListener('DOMContentLoaded', function() {
    // Обновяване на брояча на кошницата
    updateCartCount();
    
    // Добавяне на събития за бутоните "Добави в кошницата" в index.php
    document.querySelectorAll('.add-to-cart-btn[data-id]').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            addToCart(productId, this);
        });
    });
    
    // Добавяне на събития за бутоните в слайдера
    document.querySelectorAll('.new-book-card .add-to-cart-btn[data-id]').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-id');
            addToCart(productId, this);
        });
    });
});

// 6. Глобална функция за добавяне (за използване от onclick в други файлове)
window.addToCartGlobal = function(productId, element) {
    addToCart(productId, element);
};

</script>
</body>
</html>
