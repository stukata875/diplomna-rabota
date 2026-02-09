<?php
$page_title = 'Общи условия';
include 'header.php';
?>

<link rel="stylesheet" href="assets/css/terms.css">

<div class="terms-page">
    <div class="terms-content">
        <h1>Общи условия</h1>
        
        <div class="last-updated-terms">
            <i class="fas fa-sync-alt"></i> Последна актуализация: <?php echo date('d.m.Y'); ?>
        </div>
        
        <div class="terms-intro">
            <p><strong>Важно:</strong> Моля, прочетете внимателно тези Общи условия преди да използвате нашите услуги. 
            С използването на нашия уебсайт и услуги, вие се съгласявате с тези условия.</p>
        </div>
        
        <h2><i class="fas fa-book section-icon"></i> 1. Определения</h2>
        <p>В тези Общи условия следните термини имат следното значение:</p>
        <div class="terms-list">
            <ul>
                <li><strong>"Книжарница BookStore"</strong> - онлайн платформа за продажба на книги</li>
                <li><strong>"Потребител"</strong> - всяко лице, което използва уебсайта</li>
                <li><strong>"Клиент"</strong> - потребител, който прави поръчка</li>
                <li><strong>"Поръчка"</strong> - заявка за покупка на стоки</li>
            </ul>
        </div>
        
        <h2><i class="fas fa-user-plus section-icon"></i> 2. Регистрация и акаунт</h2>
        <p>За да направите поръчка, трябва да създадете потребителски акаунт. Вие отговаряте за:</p>
        <div class="terms-list">
            <ul>
                <li>Точността на предоставената информация</li>
                <li>Защитата на вашата парола</li>
                <li>Всички действия, извършени чрез вашия акаунт</li>
            </ul>
        </div>
        
        <h2><i class="fas fa-credit-card section-icon"></i> 3. Поръчки и плащания</h2>
        <table class="terms-table">
            <thead>
                <tr>
                    <th>Вид плащане</th>
                    <th>Описание</th>
                    <th>Срокове</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Наложен платеж</strong></td>
                    <td>Плащане при получаване на стоката</td>
                    <td><span class="badge badge-success">Веднага при доставка</span></td>
                </tr>
                <tr>
                    <td><strong>Банков превод</strong></td>
                    <td>Предплащане по банков път</td>
                    <td><span class="badge badge-warning">2-3 работни дни</span></td>
                </tr>
                <tr>
                    <td><strong>Кредитна/дебитна карта</strong></td>
                    <td>Онлайн плащане</td>
                    <td><span class="badge badge-success">Веднага</span></td>
                </tr>
            </tbody>
        </table>
        
        <div class="info-box">
            <p><i class="fas fa-info-circle"></i> <strong>Информация:</strong> Всички плащания се обработват сигурно през нашия платежен процесор.</p>
        </div>
        
        <h2><i class="fas fa-shipping-fast section-icon"></i> 4. Доставка</h2>
        <p>Доставката се извършва чрез куриерска фирма. Сроковете за доставка са:</p>
        <div class="terms-list">
            <ul>
                <li><strong>София:</strong> 1-2 работни дни</li>
                <li><strong>Други градове:</strong> 2-3 работни дни</li>
                <li><strong>Села:</strong> 3-5 работни дни</li>
            </ul>
        </div>
        
        <h2><i class="fas fa-undo section-icon"></i> 5. Връщане и рекламации</h2>
        <p>Имате право да върнете стоката в срок от 14 дни от получаването, ако:</p>
        <div class="terms-list">
            <ul>
                <li>Стоката е повредена</li>
                <li>Не сте получили заявената стока</li>
                <li>Стоката не отговаря на описанието</li>
            </ul>
        </div>
        
        <div class="terms-warning">
            <p><strong>Забележка:</strong> Продукти с нарушена опаковка или след употреба не подлежат на връщане.</p>
        </div>
        
        <h2><i class="fas fa-copyright section-icon"></i> 6. Интелектуална собственост</h2>
        <p>Цялото съдържание на уебсайта (текстове, изображения, лого и др.) е защитено с авторски права.</p>
        
        <h2><i class="fas fa-shield-alt section-icon"></i> 7. Ограничение на отговорността</h2>
        <p>BookStore не носи отговорност за:</p>
        <div class="terms-list">
            <ul>
                <li>Злоупотреби с кредитни карти от трети страни</li>
                <li>Закъснения по вина на куриерски фирми</li>
                <li>Неправилно попълнени данни от клиента</li>
            </ul>
        </div>
        
        <h2><i class="fas fa-sync section-icon"></i> 8. Промени в условията</h2>
        <p>BookStore си запазва правото да променя тези условия по всяко време. Актуалните условия винаги ще бъдат достъпни на тази страница.</p>
        
        <div class="terms-contact">
            <h3><i class="fas fa-headset"></i> Контакти за въпроси:</h3>
            <p><i class="fas fa-phone"></i> <strong>Телефон:</strong> 02 123 4567</p>
            <p><i class="fas fa-envelope"></i> <strong>Имейл:</strong> terms@bookstore.bg</p>
            <p><i class="fas fa-clock"></i> <strong>Работно време:</strong> Понеделник - Петък: 9:00 - 18:00</p>
        </div>
        
        <div class="terms-actions">
            <a href="index.php" class="terms-btn terms-btn-primary">
                <i class="fas fa-store"></i> Върни се в книжарницата
            </a>
            <a href="contact.php" class="terms-btn terms-btn-secondary">
                <i class="fas fa-comments"></i> Свържи се с нас
            </a>
        </div>
    </div>
</div>

<script src="assets/js/terms.js"></script>

<?php include 'footer.php'; ?>