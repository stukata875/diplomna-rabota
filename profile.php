<?php
include 'config.php';

// Проверка дали потребителят е влязъл
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Вземане на потребителски данни
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Функции за жанрове
function getUserPreferredGenres($conn, $user_id) {
    $sql = "SELECT preferred_genres FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    if (!empty($row['preferred_genres'])) {
        return explode(',', $row['preferred_genres']);
    }
    return [];
}

function getReadingStats($conn, $user_id) {
    $stats = [
        'total_orders' => 0,
        'total_books' => 0,
        'total_spent' => 0
    ];
    
    // Брой поръчки
    $sql = "SELECT COUNT(*) as count FROM orders WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $stats['total_orders'] = $row['count'];
    }
    $stmt->close();
    
    return $stats;
}

// Вземане на предпочитани жанрове
$preferred_genres = getUserPreferredGenres($conn, $user_id);
$reading_stats = getReadingStats($conn, $user_id);

// Вземане на всички жанрове за селекцията
$genres_sql = "SELECT id, name, color FROM genres ORDER BY name";
$genres_result = $conn->query($genres_sql);
$all_genres = [];
while ($row = $genres_result->fetch_assoc()) {
    $all_genres[] = $row;
}

// Обработка на формата за запазване на предпочитания
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_genres'])) {
    $selected_genres = isset($_POST['genres']) ? $_POST['genres'] : [];
    
    // Валидация - максимум 5 жанра
    if (count($selected_genres) > 5) {
        $error = "Можете да изберете максимум 5 жанра!";
    } else {
        $genres_string = implode(',', $selected_genres);
        $update_sql = "UPDATE users SET preferred_genres = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $genres_string, $user_id);
        
        if ($update_stmt->execute()) {
            $success = "Предпочитанията са запазени!";
            $preferred_genres = $selected_genres;
        } else {
            $error = "Грешка при запазване: " . $conn->error;
        }
        $update_stmt->close();
    }
}

$page_title = 'Моят профил';
include 'header.php';
?>

<style>
.profile-page {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.profile-grid {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 30px;
}

.profile-sidebar {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.user-info-card {
    text-align: center;
    margin-bottom: 20px;
}

.user-avatar {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    margin: 0 auto 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    color: white;
}

.profile-nav {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.profile-nav a {
    padding: 12px 15px;
    color: #333;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile-nav a:hover {
    background: #f5f5f5;
}

.profile-nav a.active {
    background: #9C27B0;
    color: white;
}

.profile-nav a.logout {
    color: #f44336;
}

.profile-content {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.dashboard-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
}

.card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.card-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.card-info h3 {
    font-size: 24px;
    margin: 0;
    color: #333;
}

.card-info p {
    margin: 5px 0 0;
    color: #666;
    font-size: 14px;
}

/* Секция за жанрове */
.genres-section {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.genres-section h2 {
    margin-bottom: 20px;
    color: #333;
    font-size: 22px;
}

.genres-description {
    color: #666;
    margin-bottom: 25px;
    font-size: 15px;
    line-height: 1.5;
}

.genre-selection {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.genre-checkboxes {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}

.genre-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.genre-checkbox:hover {
    border-color: #9C27B0;
    background: #F3E5F5;
}

.genre-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.genre-label {
    cursor: pointer;
    font-weight: 500;
    color: #333;
}

.genre-color-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-left: auto;
}

.selected-genres-count {
    font-size: 14px;
    color: #666;
    margin-bottom: 15px;
}

.selected-genres-count span {
    font-weight: bold;
    color: #9C27B0;
}

.save-genres-btn {
    background: #9C27B0;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    max-width: 200px;
}

.save-genres-btn:hover {
    background: #7B1FA2;
    transform: translateY(-2px);
}

/* Съобщения */
.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #E8F5E9;
    color: #2E7D32;
    border: 1px solid #C8E6C9;
}

.alert-error {
    background: #FFEBEE;
    color: #C62828;
    border: 1px solid #FFCDD2;
}

/* Статистика за четене */
.reading-stats {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
    margin-top: 15px;
}

.stat-item {
    text-align: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-value {
    font-size: 28px;
    font-weight: bold;
    color: #9C27B0;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    color: #666;
}

/* Адаптивен дизайн */
@media (max-width: 768px) {
    .profile-grid {
        grid-template-columns: 1fr;
    }
    
    .dashboard-cards {
        grid-template-columns: 1fr;
    }
    
    .genre-checkboxes {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="profile-page">
    <div class="container">
        <h1><i class="fas fa-user"></i> Моят профил</h1>
        
        <!-- Съобщения -->
        <?php if (isset($success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-grid">
            <div class="profile-sidebar">
                <div class="user-info-card">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h3><?= htmlspecialchars($user['email']) ?></h3>
                    <p>Член от: <?= date('d.m.Y', strtotime($user['created_at'])) ?></p>
                </div>
                
                <nav class="profile-nav">
                    <a href="profile.php" class="active"><i class="fas fa-user"></i> Профил</a>
                    <a href="orders.php"><i class="fas fa-box"></i> Моите поръчки</a>
                    <a href="wishlist.php"><i class="fas fa-heart"></i> Любими книги</a>
                    <a href="settings.php"><i class="fas fa-cog"></i> Настройки</a>
                    <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Изход</a>
                </nav>
            </div>
            
            <div class="profile-content">
                <!-- Статистика -->
                <div class="reading-stats">
                    <h2><i class="fas fa-chart-bar"></i> Статистика на четенето</h2>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-value"><?= $reading_stats['total_orders'] ?></div>
                            <div class="stat-label">Поръчки</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= $reading_stats['total_books'] ?></div>
                            <div class="stat-label">Прочетени книги</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= number_format($reading_stats['total_spent'], 2) ?> лв.</div>
                            <div class="stat-label">Общо похарчено</div>
                        </div>
                    </div>
                </div>
                
                <!-- Жанрови предпочитания -->
                <div class="genres-section">
                    <h2><i class="fas fa-bookmark"></i> Жанрови предпочитания</h2>
                    
                    <p class="genres-description">
                        Изберете вашите любими жанрове, за да получавате персонализирани препоръки за книги.
                        Можете да изберете до 5 жанра.
                    </p>
                    
                    <div class="selected-genres-count">
                        Избрани жанрове: <span id="selected-count"><?= count($preferred_genres) ?></span>/5
                    </div>
                    
                    <form method="post" class="genre-selection">
                        <div class="genre-checkboxes" id="genre-container">
                            <?php foreach ($all_genres as $genre): 
                                $is_checked = in_array($genre['id'], $preferred_genres);
                            ?>
                            <label class="genre-checkbox">
                                <input type="checkbox" 
                                       name="genres[]" 
                                       value="<?= $genre['id'] ?>" 
                                       <?= $is_checked ? 'checked' : '' ?>
                                       onchange="updateSelectedCount()">
                                <span class="genre-label"><?= htmlspecialchars($genre['name']) ?></span>
                                <span class="genre-color-indicator" style="background: <?= $genre['color'] ?>"></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        
                        <button type="submit" name="save_genres" class="save-genres-btn" id="save-btn">
                            <i class="fas fa-save"></i> Запази предпочитанията
                        </button>
                    </form>
                    
                    <?php if (!empty($preferred_genres)): ?>
                    <div style="margin-top: 25px;">
                        <h3><i class="fas fa-star"></i> Вашите текущи предпочитания:</h3>
                        <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                            <?php 
                            foreach ($preferred_genres as $genre_id):
                                $genre_name = '';
                                $genre_color = '#9C27B0';
                                
                                foreach ($all_genres as $g) {
                                    if ($g['id'] == $genre_id) {
                                        $genre_name = $g['name'];
                                        $genre_color = $g['color'];
                                        break;
                                    }
                                }
                                
                                if (!empty($genre_name)):
                            ?>
                            <span style="background: <?= $genre_color ?>; color: white; padding: 6px 12px; border-radius: 20px; font-size: 14px;">
                                <?= htmlspecialchars($genre_name) ?>
                            </span>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Dashboard картички -->
                <div class="dashboard-cards">
                    <div class="card">
                        <div class="card-icon" style="background: #4CAF50;">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="card-info">
                            <h3>0</h3>
                            <p>Активни поръчки</p>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-icon" style="background: #2196F3;">
                            <i class="fas fa-history"></i>
                        </div>
                        <div class="card-info">
                            <h3><?= $reading_stats['total_orders'] ?></h3>
                            <p>Завършени поръчки</p>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-icon" style="background: #FF9800;">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div class="card-info">
                            <h3>0</h3>
                            <p>Любими книги</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Брой избрани жанрове
function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('input[name="genres[]"]:checked');
    const count = checkboxes.length;
    document.getElementById('selected-count').textContent = count;
    
    // Деактивиране на бутона ако са повече от 5
    const saveBtn = document.getElementById('save-btn');
    if (count > 5) {
        saveBtn.disabled = true;
        saveBtn.style.opacity = '0.6';
        saveBtn.style.cursor = 'not-allowed';
        saveBtn.title = 'Максимум 5 жанра!';
    } else {
        saveBtn.disabled = false;
        saveBtn.style.opacity = '1';
        saveBtn.style.cursor = 'pointer';
        saveBtn.title = '';
    }
}

// Инициализация
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
    
    // Ограничаване на чекбоксите
    const checkboxes = document.querySelectorAll('input[name="genres[]"]');
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const checked = document.querySelectorAll('input[name="genres[]"]:checked');
            if (checked.length > 5) {
                this.checked = false;
                alert('Можете да изберете максимум 5 жанра!');
            }
            updateSelectedCount();
        });
    });
});

// Добавяне на Font Awesome ако не е наличен
if (!document.querySelector('#font-awesome')) {
    const link = document.createElement('link');
    link.id = 'font-awesome';
    link.rel = 'stylesheet';
    link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
    document.head.appendChild(link);
}
</script>

<?php include 'footer.php'; ?>