<?php
$page_title = 'Политика за поверителност';
include 'header.php';
?>

<link rel="stylesheet" href="assets/css/terms.css">

<div class="privacy-page">
    <div class="privacy-content">
        <h1>Политика за поверителност</h1>
        
        <div class="last-updated-privacy">
            <i class="fas fa-calendar-alt"></i> В сила от: <?php echo date('d.m.Y'); ?>
        </div>
        
        <div class="privacy-section">
            <h2><i class="fas fa-shield-alt"></i> Защита на вашите данни</h2>
            <p>В BookStore сериозно подхождаме към защитата на вашата лична информация. Тази политика описва как събираме, използваме и защитаваме вашите данни.</p>
        </div>
        
        <div class="privacy-section">
            <h2><i class="fas fa-database"></i> 1. Какви данни събираме</h2>
            
            <h3><i class="fas fa-user-edit"></i> 1.1. Данни, предоставени от вас</h3>
            <div class="privacy-list">
                <ul>
                    <li><strong>Име и фамилия</strong></li>
                    <li><strong>Имейл адрес</strong></li>
                    <li><strong>Телефонен номер</strong></li>
                    <li><strong>Адрес за доставка</strong></li>
                    <li><strong>Платежна информация</strong></li>
                </ul>
            </div>
            
            <h3><i class="fas fa-robot"></i> 1.2. Автоматично събирани данни</h3>
            <div class="privacy-list">
                <ul>
                    <li><strong>IP адрес</strong></li>
                    <li><strong>Тип браузър и устройство</strong></li>
                    <li><strong>Страници, които посещавате</strong></li>
                    <li><strong>Време на посещение</strong></li>
                </ul>
            </div>
        </div>
        
        <div class="privacy-section">
            <h2><i class="fas fa-cogs"></i> 2. Как използваме вашите данни</h2>
            <table class="privacy-table">
                <thead>
                    <tr>
                        <th>Цел</th>
                        <th>Данни</th>
                        <th>Правно основание</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Обработка на поръчки</strong></td>
                        <td>Име, адрес, телефон</td>
                        <td><span class="tag tag-contract">Изпълнение на договор</span></td>
                    </tr>
                    <tr>
                        <td><strong>Изпращане на бюлетини</strong></td>
                        <td>Имейл адрес</td>
                        <td><span class="tag tag-consent">Съгласие</span></td>
                    </tr>
                    <tr>
                        <td><strong>Подобряване на услугите</strong></td>
                        <td>Анонимни данни за трафик</td>
                        <td><span class="tag tag-interest">Законен интерес</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="privacy-section">
            <h2><i class="fas fa-share-alt"></i> 3. Споделяне с трети страни</h2>
            <p>Вашите данни могат да бъдат споделяни само със:</p>
            <div class="privacy-list">
                <ul>
                    <li><strong>Куриерски компании</strong> (за доставка)</li>
                    <li><strong>Банкови институции</strong> (за плащания)</li>
                    <li><strong>Служители на BookStore</strong> (за обслужване)</li>
                </ul>
            </div>
            <div class="info-box">
                <p><i class="fas fa-ban"></i> <strong>Никога не продаваме</strong> вашите лични данни на трети страни за маркетингови цели.</p>
            </div>
        </div>
        
        <div class="privacy-section">
            <h2><i class="fas fa-history"></i> 4. Период на съхранение</h2>
            <p>Съхраняваме вашите данни само за необходимото време:</p>
            <div class="privacy-list">
                <ul>
                    <li><strong>Данни за поръчки:</strong> 5 години (съгласно ЗДДС)</li>
                    <li><strong>Акаунт данни:</strong> докато акаунтът е активен</li>
                    <li><strong>Логове на системата:</strong> 1 година</li>
                </ul>
            </div>
        </div>
        
        <div class="privacy-section">
            <h2><i class="fas fa-gavel"></i> 5. Вашите права (GDPR)</h2>
            <p>Съгласно GDPR имате право на:</p>
            <div class="gdpr-rights">
                <div class="gdpr-right">
                    <h4><i class="fas fa-search"></i> Достъп до данни</h4>
                    <p>Право да знаете какви данни притежаваме</p>
                </div>
                <div class="gdpr-right">
                    <h4><i class="fas fa-edit"></i> Корекция</h4>
                    <p>Поправяне на неточни данни</p>
                </div>
                <div class="gdpr-right">
                    <h4><i class="fas fa-trash-alt"></i> Изтриване</h4>
                    <p>Право на забрава</p>
                </div>
                <div class="gdpr-right">
                    <h4><i class="fas fa-pause-circle"></i> Ограничаване</h4>
                    <p>Ограничаване на обработката</p>
                </div>
            </div>
        </div>
        
        <div class="privacy-section">
            <h2><i class="fas fa-cookie-bite"></i> 6. Бисквитки (Cookies)</h2>
            <p>Използваме следните типове бисквитки:</p>
            
            <div class="cookie-types">
                <div class="cookie-card">
                    <h4><i class="fas fa-cog"></i> Необходими</h4>
                    <ul>
                        <li>Сесийни бисквитки</li>
                        <li>Кошница</li>
                    </ul>
                </div>
                <div class="cookie-card">
                    <h4><i class="fas fa-chart-line"></i> Аналитични</h4>
                    <ul>
                        <li>Google Analytics</li>
                        <li>Топлинни карти</li>
                    </ul>
                </div>
                <div class="cookie-card">
                    <h4><i class="fas fa-heart"></i> Предпочитания</h4>
                    <ul>
                        <li>Език</li>
                        <li>Тема</li>
                    </ul>
                </div>
            </div>
            
            <div class="info-box" style="background: #e3f2fd; border-color: #3498db;">
                <p><i class="fas fa-info-circle"></i> Можете да контролирате бисквитките през настройките на вашия браузър. Продължаването да използвате сайта се счита за съгласие с използването на бисквитки.</p>
            </div>
        </div>
        
        <div class="data-protection-box">
            <h3><i class="fas fa-user-shield"></i> Контакти за защита на данните</h3>
            <p><i class="fas fa-user-tie"></i> <strong>Лице за защита на данните:</strong> Иван Иванов</p>
            <p><i class="fas fa-envelope"></i> <strong>Имейл:</strong> dpo@bookstore.bg</p>
            <p><i class="fas fa-phone"></i> <strong>Телефон:</strong> 02 987 6543</p>
            <p><i class="fas fa-map-marker-alt"></i> <strong>Адрес:</strong> гр. София, ул. "Примерна" №1</p>
        </div>
        
        <div class="privacy-actions">
            <a href="javascript:void(0);" class="privacy-btn privacy-btn-accept">
                <i class="fas fa-check-circle"></i> Приемам бисквитки
            </a>
            <a href="javascript:void(0);" class="privacy-btn privacy-btn-decline">
                <i class="fas fa-times-circle"></i> Отказвам ненужни
            </a>
            <a href="contact.php" class="privacy-btn privacy-btn-secondary">
                <i class="fas fa-question-circle"></i> Имам въпрос
            </a>
        </div>
        
        <div class="privacy-actions">
            <a href="index.php" class="privacy-btn" style="background: #2c3e50; color: white;">
                <i class="fas fa-home"></i> Към начална страница
            </a>
        </div>
    </div>
</div>

<script src="assets/js/terms.js"></script>

<?php include 'footer.php'; ?>