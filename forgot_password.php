<?php
include 'config.php';

$page_title = 'Забравена парола';
include 'header.php';

$error_message = '';
$success_message = '';

// Обработка на формата
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error_message = 'Моля, въведете вашия email адрес';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Моля, въведете валиден email адрес';
    } else {
        // Проверка дали имейлът съществува
        $stmt = $conn->prepare("SELECT id, email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $error_message = 'Този email адрес не е регистриран в нашата система';
        } else {
            // Генериране на код за нулиране
            $reset_code = bin2hex(random_bytes(16));
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Запазване на кода в базата данни
            $user = $result->fetch_assoc();
            $stmt = $conn->prepare("INSERT INTO password_resets (user_id, reset_code, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user['id'], $reset_code, $expires_at);
            
            if ($stmt->execute()) {
                // В реален проект тук бихме изпратили email
                $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/diplomna/reset_password.php?code=" . $reset_code;
                
                $success_message = '
                    <div class="reset-link">
                        <p>Инструкции за нулиране на паролата са изпратени на вашия email.</p>
                        <p><strong>За тестване:</strong></p>
                        <p><a href="' . $reset_link . '">Кликнете тук за да нулирате паролата</a></p>
                        <small>Този линк ще бъде активен за 1 час.</small>
                    </div>
                ';
            } else {
                $error_message = 'Грешка при обработката на заявката. Моля, опитайте отново.';
            }
        }
        $stmt->close();
    }
}
?>

<link rel="stylesheet" href="assets/css/forgot_password.css">

<div class="forgot-password-page">
    <div class="container">
        <div class="auth-form-container">
            <h1><i class="fas fa-key"></i> Забравена парола</h1>
            
            <p class="instructions">
                Въведете вашия email адрес и ще ви изпратим линк за нулиране на паролата.
            </p>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?= $success_message ?>
                </div>
            <?php endif; ?>
            
            <?php if (!$success_message): ?>
                <form method="post" action="" class="auth-form">
                    <div class="form-group">
                        <label for="email">Email адрес</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
                               required 
                               placeholder="вашият@email.com">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Изпрати линк за нулиране
                    </button>
                </form>
            <?php endif; ?>
            
            <div class="auth-links">
                <p><a href="login.php"><i class="fas fa-arrow-left"></i> Върни се към входа</a></p>
            </div>
        </div>
        
        <div class="password-tips">
            <h3>Съвети за сигурна парола:</h3>
            <ul>
                <li><i class="fas fa-check-circle"></i> Използвайте поне 8 символа</li>
                <li><i class="fas fa-check-circle"></i> Комбинирайте букви, цифри и символи</li>
                <li><i class="fas fa-check-circle"></i> Не използвайте лична информация</li>
                <li><i class="fas fa-check-circle"></i> Променяйте паролата редовно</li>
                <li><i class="fas fa-check-circle"></i> Не използвайте същата парола навсякъде</li>
            </ul>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>