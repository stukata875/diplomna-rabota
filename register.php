<?php
include 'config.php';

$error_message = '';
$success_message = '';

// Проверка дали потребителят вече е влязъл
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Обработка на формата за регистрация
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']) ? true : false;
    
    // Валидация
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = 'Моля, попълнете всички полета';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Моля, въведете валиден email адрес';
    } elseif (strlen($password) < 6) {
        $error_message = 'Паролата трябва да е поне 6 символа';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Паролите не съвпадат';
    } elseif (!$terms) {
        $error_message = 'Трябва да се съгласите с Общите условия';
    } else {
        // Проверка дали email вече съществува
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error_message = 'Този email вече е регистриран';
        } else {
            // Хеширане на паролата
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Добавяне на нов потребител
            $stmt = $conn->prepare("INSERT INTO users (email, password, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("ss", $email, $hashed_password);
            
            if ($stmt->execute()) {
                // Успешна регистрация - пренасочване към вход
                header('Location: login.php?registered=success');
                exit();
            } else {
                $error_message = 'Грешка при регистрацията. Моля, опитайте отново.';
            }
        }
        $stmt->close();
    }
}

$page_title = 'Регистрация';
include 'header.php';
?>

<style>
/* СТИЛОВЕ ЗА СТРАНИЦАТА С РЕГИСТРАЦИЯ */
.register-page {
    max-width: 1200px;
    margin: 40px auto;
    padding: 0 20px;
    min-height: 70vh;
}

.register-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    overflow: hidden;
}

.register-wrapper {
    display: flex;
    min-height: 600px;
}

/* ЛЯВА ЧАСТ - ИНФОРМАЦИЯ */
.register-info {
    flex: 1;
    background: #f8f9fa;
    padding: 40px;
    border-right: 1px solid #e0e0e0;
}

.register-info h2 {
    font-size: 24px;
    color: #333;
    margin-bottom: 30px;
    font-weight: 600;
    border-bottom: 2px solid #e60000;
    padding-bottom: 15px;
}

.benefits-list {
    list-style: none;
    padding: 0;
    margin: 0 0 40px 0;
}

.benefit-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 25px;
    padding-bottom: 25px;
    border-bottom: 1px solid #eee;
}

.benefit-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.benefit-icon {
    flex: 0 0 40px;
    height: 40px;
    background: #e60000;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
}

.benefit-text strong {
    display: block;
    color: #333;
    margin-bottom: 5px;
    font-size: 16px;
}

.benefit-text p {
    color: #666;
    margin: 0;
    font-size: 14px;
    line-height: 1.5;
}

.testimonial-box {
    background: white;
    padding: 25px;
    border-radius: 8px;
    border-left: 4px solid #e60000;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.testimonial-text {
    font-style: italic;
    color: #555;
    line-height: 1.6;
    margin: 0 0 15px 0;
}

.testimonial-author strong {
    display: block;
    color: #333;
    font-size: 16px;
}

.testimonial-author span {
    color: #666;
    font-size: 14px;
}

/* ДЯСНА ЧАСТ - ФОРМА */
.register-form-container {
    flex: 1;
    padding: 40px;
}

.form-header {
    text-align: center;
    margin-bottom: 30px;
}

.form-header h1 {
    font-size: 28px;
    color: #333;
    margin: 0 0 10px 0;
    font-weight: 600;
}

.form-header p {
    color: #666;
    margin: 0;
    font-size: 16px;
}

/* ГРЕШКИ */
.register-alert {
    padding: 15px 20px;
    border-radius: 6px;
    margin-bottom: 20px;
    font-size: 15px;
}

.register-alert-error {
    background: #ffe6e6;
    color: #e60000;
    border: 1px solid #ffcccc;
}

/* ФОРМА */
.register-form-group {
    margin-bottom: 25px;
}

.register-form-group label {
    display: block;
    color: #333;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 15px;
}

.input-with-icon {
    position: relative;
}

.input-with-icon i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

.register-input {
    width: 100%;
    padding: 14px 15px 14px 45px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 16px;
    outline: none;
    transition: border 0.3s;
}

.register-input:focus {
    border-color: #e60000;
}

/* ПАРОЛИ */
.password-wrapper {
    position: relative;
}

.password-wrapper .register-toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: #999;
    cursor: pointer;
    padding: 0;
    font-size: 16px;
}

.password-strength {
    margin-top: 10px;
}

.strength-bar {
    height: 4px;
    background: #eee;
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: 5px;
}

.strength-bar::after {
    content: '';
    display: block;
    height: 100%;
    width: 20%;
    background: #ff4d4d;
    transition: width 0.3s;
}

.password-match {
    margin-top: 5px;
    font-size: 14px;
}

/* ЧЕКБОКС */
.register-terms {
    margin-bottom: 30px;
}

.checkbox-wrapper {
    display: flex;
    align-items: flex-start;
}

.checkbox-wrapper input[type="checkbox"] {
    margin-right: 10px;
    margin-top: 3px;
}

.terms-text {
    color: #666;
    font-size: 14px;
    line-height: 1.5;
}

.terms-text a {
    color: #e60000;
    text-decoration: none;
}

.terms-text a:hover {
    text-decoration: underline;
}

/* БУТОН */
.register-submit-btn {
    width: 100%;
    padding: 16px;
    background: #e60000;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: background 0.3s;
    margin-bottom: 20px;
}

.register-submit-btn:hover {
    background: #c40000;
}

/* ЛИНК ЗА ВХОД */
.register-login-link {
    text-align: center;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.register-login-link p {
    color: #666;
    margin: 0;
}

.register-login-link a {
    color: #e60000;
    text-decoration: none;
    font-weight: 600;
}

.register-login-link a:hover {
    text-decoration: underline;
}

/* АДАПТИВНОСТ */
@media (max-width: 992px) {
    .register-wrapper {
        flex-direction: column;
    }
    
    .register-info {
        border-right: none;
        border-bottom: 1px solid #e0e0e0;
        padding: 30px;
    }
    
    .register-form-container {
        padding: 30px;
    }
}

@media (max-width: 576px) {
    .register-page {
        padding: 0 15px;
    }
    
    .register-info,
    .register-form-container {
        padding: 20px;
    }
    
    .form-header h1 {
        font-size: 24px;
    }
    
    .benefit-item {
        flex-direction: column;
    }
    
    .benefit-icon {
        margin-bottom: 10px;
    }
}
</style>

<div class="register-page">
    <div class="register-container">
        <div class="register-wrapper">
            <div class="register-info">
                <h2>Предимства на регистрацията</h2>
                <ul class="benefits-list">
                    <li class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="benefit-text">
                            <strong>Бързо пазаруване</strong>
                            <p>Завършвайте поръчки за секунди</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <div class="benefit-text">
                            <strong>История на поръчките</strong>
                            <p>Проследявайте всичките си покупки</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="benefit-text">
                            <strong>Специални отстъпки</strong>
                            <p>До 20% отстъпка за регистрирани</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="benefit-text">
                            <strong>Любими книги</strong>
                            <p>Запазвайте книги за по-късно</p>
                        </div>
                    </li>
                    <li class="benefit-item">
                        <div class="benefit-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="benefit-text">
                            <strong>Известия</strong>
                            <p>Нови книги и промоции</p>
                        </div>
                    </li>
                </ul>
                
                <div class="testimonial-box">
                    <p class="testimonial-text">
                        "Регистрирах се преди година и вече съм поръчал над 20 книги. Много удобно и бързо!"
                    </p>
                    <div class="testimonial-author">
                        <strong>Иван Иванов</strong>
                        <span>постоянен клиент</span>
                    </div>
                </div>
            </div>
            
            <div class="register-form-container">
                <div class="form-header">
                    <h1>Създаване на акаунт</h1>
                    <p>Присъединете се към нашата общност от книголюбители</p>
                </div>
                
                <?php if ($error_message): ?>
                    <div class="register-alert register-alert-error">
                        <?= htmlspecialchars($error_message) ?>
                    </div>
                <?php endif; ?>
                
                <form method="post" action="" class="register-form" id="registerForm">
                    <div class="register-form-group">
                        <label for="reg_email">Email адрес</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope"></i>
                            <input type="email" 
                                   id="reg_email" 
                                   name="email" 
                                   class="register-input"
                                   value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
                                   required 
                                   placeholder="Вашият@email.com">
                        </div>
                    </div>
                    
                    <div class="register-form-group">
                        <label for="reg_password">Парола</label>
                        <div class="input-with-icon password-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" 
                                   id="reg_password" 
                                   name="password" 
                                   class="register-input"
                                   required 
                                   placeholder="Поне 6 символа"
                                   minlength="6">
                            <button type="button" class="register-toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div class="password-strength" id="passwordStrength">
                            <div class="strength-bar"></div>
                            <div class="strength-text">Сила на паролата: <span>Слаба</span></div>
                        </div>
                    </div>
                    
                    <div class="register-form-group">
                        <label for="reg_confirm_password">Потвърдете паролата</label>
                        <div class="input-with-icon password-wrapper">
                            <i class="fas fa-lock"></i>
                            <input type="password" 
                                   id="reg_confirm_password" 
                                   name="confirm_password" 
                                   class="register-input"
                                   required 
                                   placeholder="Въведете паролата отново">
                            <button type="button" class="register-toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <div id="passwordMatch" class="password-match"></div>
                    </div>
                    
                    <div class="register-form-group register-terms">
                        <div class="checkbox-wrapper">
                            <input type="checkbox" id="reg_terms" name="terms" required>
                            <label for="reg_terms">
                                <span class="terms-text">
                                    Съгласявам се с <a href="terms.php" target="_blank">Общите условия</a> и 
                                    <a href="privacy.php" target="_blank">Политиката за поверителност</a>
                                </span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="register-submit-btn">
                        Регистрирай се
                    </button>
                </form>
                
                <div class="register-login-link">
                    <p>Вече имате акаунт? <a href="login.php" class="login-link">Влезте в профила си</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript за регистрацията
document.addEventListener('DOMContentLoaded', function() {
    // Показване/скриване на паролата
    const toggleButtons = document.querySelectorAll('.register-toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentElement.querySelector('input');
            const icon = this.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        });
    });
    
    // Проверка за сила на паролата
    const passwordInput = document.getElementById('reg_password');
    const strengthBar = document.querySelector('.strength-bar');
    const strengthText = document.querySelector('.strength-text span');
    
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 6) strength += 25;
            if (password.length >= 8) strength += 25;
            if (/[A-Z]/.test(password)) strength += 25;
            if (/[0-9]/.test(password)) strength += 25;
            
            strengthBar.style.width = strength + '%';
            
            if (strength < 50) {
                strengthBar.style.backgroundColor = '#ff4d4d';
                strengthText.textContent = 'Слаба';
            } else if (strength < 75) {
                strengthBar.style.backgroundColor = '#ffa500';
                strengthText.textContent = 'Средна';
            } else {
                strengthBar.style.backgroundColor = '#4CAF50';
                strengthText.textContent = 'Силна';
            }
        });
    }
    
    // Проверка за съвпадение на паролите
    const confirmPasswordInput = document.getElementById('reg_confirm_password');
    const passwordMatchDiv = document.getElementById('passwordMatch');
    
    if (confirmPasswordInput && passwordInput) {
        confirmPasswordInput.addEventListener('input', function() {
            if (this.value === '') {
                passwordMatchDiv.textContent = '';
                passwordMatchDiv.style.color = '';
            } else if (this.value === passwordInput.value) {
                passwordMatchDiv.textContent = '✓ Паролите съвпадат';
                passwordMatchDiv.style.color = '#4CAF50';
            } else {
                passwordMatchDiv.textContent = '✗ Паролите не съвпадат';
                passwordMatchDiv.style.color = '#e60000';
            }
        });
    }
    
    // Валидация на формата преди изпращане
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const email = document.getElementById('reg_email').value;
            const password = document.getElementById('reg_password').value;
            const confirmPassword = document.getElementById('reg_confirm_password').value;
            const terms = document.getElementById('reg_terms').checked;
            
            if (!terms) {
                e.preventDefault();
                alert('Трябва да се съгласите с Общите условия');
                return false;
            }
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Паролите не съвпадат');
                return false;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('Паролата трябва да е поне 6 символа');
                return false;
            }
            
            return true;
        });
    }
});
</script>

<?php include 'footer.php'; ?>