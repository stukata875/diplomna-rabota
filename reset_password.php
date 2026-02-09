
<?php
include 'config.php';

$page_title = 'Нулиране на парола';
include 'header.php';

$error_message = '';
$success_message = '';
$code = isset($_GET['code']) ? $_GET['code'] : '';

// Проверка дали кодът съществува
$valid_code = false;
if (!empty($code)) {
    $stmt = $conn->prepare("SELECT pr.*, u.email FROM password_resets pr JOIN users u ON pr.user_id = u.id WHERE pr.reset_code = ? AND pr.used = 0 AND pr.expires_at > NOW()");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $reset_data = $result->fetch_assoc();
        $valid_code = true;
    } else {
        $error_message = 'Невалиден или изтекъл линк за нулиране на паролата.';
    }
    $stmt->close();
}

// Обработка на формата за нова парола
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $code = $_POST['code'];
    
    if (empty($password) || empty($confirm_password)) {
        $error_message = 'Моля, попълнете всички полета';
    } elseif (strlen($password) < 6) {
        $error_message = 'Паролата трябва да е поне 6 символа';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Паролите не съвпадат';
    } else {
        // Проверка дали кодът все още е валиден
        $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE reset_code = ? AND used = 0 AND expires_at > NOW()");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $error_message = 'Невалиден или изтекъл линк за нулиране на паролата.';
        } else {
            $data = $result->fetch_assoc();
            $user_id = $data['user_id'];
            
            // Хеширане на новата парола
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Обновяване на паролата
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            
            if ($stmt->execute()) {
                // Маркиране на кода като използван
                $stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE reset_code = ?");
                $stmt->bind_param("s", $code);
                $stmt->execute();
                
                $success_message = 'Паролата е променена успешно! Можете да влезете в профила си с новата парола.';
            } else {
                $error_message = 'Грешка при промяната на паролата. Моля, опитайте отново.';
            }
        }
        $stmt->close();
    }
}
?>

<link rel="stylesheet" href="assets/css/forgot_password.css">

<div class="reset-password-page">
    <div class="container">
        <div class="auth-form-container">
            <h1><i class="fas fa-redo-alt"></i> Нулиране на парола</h1>
            
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <?= $error_message ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <p><?= $success_message ?></p>
                    <div class="text-center" style="margin-top: 20px;">
                        <a href="login.php" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt"></i> Към страницата за вход
                        </a>
                    </div>
                </div>
            <?php elseif (!$valid_code && empty($code)): ?>
                <div class="alert alert-error">
                    <p>Невалиден линк за нулиране на парола.</p>
                    <div class="text-center" style="margin-top: 15px;">
                        <a href="forgot_password.php" class="btn btn-secondary">
                            <i class="fas fa-key"></i> Забравена парола
                        </a>
                    </div>
                </div>
            <?php elseif ($valid_code): ?>
                <p class="instructions">
                    Здравейте <strong><?= htmlspecialchars($reset_data['email']) ?></strong>,<br>
                    моля, въведете нова парола за вашия акаунт.
                </p>
                
                <form method="post" action="" class="auth-form">
                    <input type="hidden" name="code" value="<?= htmlspecialchars($code) ?>">
                    
                    <div class="form-group">
                        <label for="password">Нова парола</label>
                        <div class="password-input">
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   minlength="6"
                                   placeholder="Поне 6 символа">
                            <button type="button" class="toggle-password" onclick="togglePasswordField('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Потвърдете новата парола</label>
                        <div class="password-input">
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   required 
                                   placeholder="Въведете паролата отново">
                            <button type="button" class="toggle-password" onclick="togglePasswordField('confirm_password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Запази новата парола
                    </button>
                </form>
            <?php endif; ?>
            
            <?php if (!$success_message && $valid_code): ?>
                <div class="auth-links">
                    <p><a href="login.php"><i class="fas fa-arrow-left"></i> Върни се към входа</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="assets/js/reset_password.js"></script>

<?php include 'footer.php'; ?>