<?php
include 'config.php';

$error_message = '';
$success_message = '';

// Проверка дали потребителят вече е влязъл
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Обработка на формата за вход
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Валидация
    if (empty($email) || empty($password)) {
        $error_message = 'Моля, попълнете всички полета';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Моля, въведете валиден email адрес';
    } else {
        // Търсене на потребителя
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $error_message = 'Грешен email или парола';
        } else {
            $user = $result->fetch_assoc();
            
            // Проверка на паролата
            if (password_verify($password, $user['password'])) {
                // Успешен вход
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                
                // Пренасочване
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                header('Location: ' . $redirect);
                exit();
            } else {
                $error_message = 'Грешен email или парола';
            }
        }
        $stmt->close();
    }
}

$page_title = 'Вход';
include 'header.php';
?>

<link rel="stylesheet" href="assets/css/login.css">

<div class="login-page">
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-sign-in-alt"></i> Вход в акаунта</h1>
        </div>
        
        <div class="login-form-container">
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['registered']) && $_GET['registered'] == 'success'): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Регистрацията е успешна! Моля, влезте в акаунта си.
                </div>
            <?php endif; ?>
            
            <form method="post" action="" class="login-form">
                <div class="form-group">
                    <label for="email">Email адрес</label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           class="form-control"
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
                           required 
                           placeholder="вашият@email.com">
                </div>
                
                <div class="form-group">
                    <label for="password">Парола</label>
                    <div class="password-wrapper">
                        <input type="password" 
                               id="password" 
                               name="password" 
                               class="form-control"
                               required 
                               placeholder="Въведете паролата">
                        <button type="button" class="toggle-password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Вход
                </button>
            </form>
            
            <div class="login-links">
                <p>Нямате акаунт? <a href="register.php">Регистрирайте се</a></p>
                <p><a href="forgot_password.php"><i class="fas fa-key"></i> Забравена парола?</a></p>
            </div>
        </div>
        
        <div class="login-benefits">
            <h3><i class="fas fa-star"></i> Предимства на регистрацията</h3>
            <ul class="benefits-list">
                <li><i class="fas fa-shopping-cart"></i> Бързи поръчки</li>
                <li><i class="fas fa-heart"></i> Любими книги</li>
                <li><i class="fas fa-history"></i> История на поръчки</li>
                <li><i class="fas fa-tag"></i> Специални промоции</li>
                <li><i class="fas fa-shipping-fast"></i> Следимост</li>
                <li><i class="fas fa-bookmark"></i> Запазване на страници</li>
            </ul>
        </div>
    </div>
</div>

<script src="assets/js/login.js"></script>

<?php include 'footer.php'; ?>