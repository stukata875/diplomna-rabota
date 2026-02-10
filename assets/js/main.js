// ============================================
// ОСНОВНИ ФУНКЦИИ ЗА ВСЕКИ СТРАНИЦА
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // 1. Мобилно меню
    initMobileMenu();
    
    // 2. Обновяване на брояча в кошницата
    updateCartCount();
    
    // 3. Инициализация на tooltips
    initTooltips();
    
    // 4. Проверка за активна навигация
    highlightActiveNav();
    
    // 5. Лениво зареждане на изображения
    initLazyLoading();
    
    // 6. Инициализация на форми за добавяне в кошницата
    initAddToCartForms();
});

// 1. МОБИЛНО МЕНЮ
function initMobileMenu() {
    const menuToggle = document.querySelector('.mobile-menu-btn');
    const mainNav = document.getElementById('mainNav');
    
    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
        });
    }
    
    // Затваряне на менюто при клик извън него
    document.addEventListener('click', function(event) {
        if (mainNav && menuToggle && !mainNav.contains(event.target) && !menuToggle.contains(event.target)) {
            mainNav.classList.remove('active');
        }
    });
}

// 2. ОБНОВЯВАНЕ НА БРОЯЧА В КОШНИЦАТА
function updateCartCount() {
    fetch('get_cart_count.php')
        .then(response => response.text())
        .then(count => {
            const cartCountElements = document.querySelectorAll('#cart-count, .cart-count');
            cartCountElements.forEach(element => {
                element.textContent = count;
            });
        })
        .catch(error => console.error('Error updating cart count:', error));
}

// 3. TOOLTIPS
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[title]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const tooltip = document.createElement('div');
    tooltip.className = 'custom-tooltip';
    tooltip.textContent = this.title;
    tooltip.style.cssText = `
        position: absolute;
        background: #333;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 1001;
        pointer-events: none;
        white-space: nowrap;
    `;
    
    document.body.appendChild(tooltip);
    
    const x = e.pageX + 10;
    const y = e.pageY + 10;
    tooltip.style.left = x + 'px';
    tooltip.style.top = y + 'px';
    
    this._tooltip = tooltip;
}

function hideTooltip() {
    if (this._tooltip) {
        this._tooltip.remove();
        delete this._tooltip;
    }
}

// 4. АКТИВНА НАВИГАЦИЯ
function highlightActiveNav() {
    const currentPage = window.location.pathname.split('/').pop();
    const navLinks = document.querySelectorAll('.main-nav a');
    
    navLinks.forEach(link => {
        const linkPage = link.getAttribute('href').split('/').pop();
        if (linkPage === currentPage || 
            (currentPage === '' && linkPage === 'index.php') ||
            (link.href === window.location.href)) {
            link.style.color = '#e60000';
            link.style.fontWeight = 'bold';
        }
    });
}

// 5. ЛЕНИВО ЗАРЕЖДАНЕ НА ИЗОБРАЖЕНИЯ
function initLazyLoading() {
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('img[data-src]');
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    }
}

// 6. ФОРМИ ЗА ДОБАВЯНЕ В КОШНИЦАТА
function initAddToCartForms() {
    const addToCartForms = document.querySelectorAll('.add-to-cart-form');
    
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            addToCart(this);
        });
    });
}

// ФУНКЦИЯ ЗА ДОБАВЯНЕ В КОШНИЦАТА
function addToCart(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn ? submitBtn.textContent : '';
    
    // Показване на индикатор за зареждане
    if (submitBtn) {
        submitBtn.textContent = 'Добавяне...';
        submitBtn.disabled = true;
    }
    
    // Изпращане на AJAX заявка
    fetch('add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Обновяване на брояча
            updateCartCount();
            
            // Показване на известие
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Грешка при добавяне в кошницата', 'error');
    })
    .finally(() => {
        // Възстановяване на бутона
        if (submitBtn) {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        }
    });
}

// ФУНКЦИЯ ЗА ПОКАЗВАНЕ НА ИЗВЕСТИЯ
function showNotification(message, type = 'success') {
    // Създаване на контейнер, ако не съществува
    let container = document.getElementById('notifications-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notifications-container';
        container.className = 'notifications-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 99999;
            max-width: 400px;
            width: 100%;
            pointer-events: none;
        `;
        document.body.appendChild(container);
    }
    
    // Създаване на уникален ID за известието
    const notificationId = 'notification-' + Date.now();
    
    // Определяне на икона според типа
    const icons = {
        'success': '✓',
        'error': '✗',
        'warning': '⚠',
        'info': 'i'
    };
    
    // Създаване на известието
    const notification = document.createElement('div');
    notification.id = notificationId;
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-icon">${icons[type] || '!'}</div>
        <div class="notification-content">
            <div class="notification-message">${message}</div>
        </div>
        <button class="notification-close" onclick="removeNotification('${notificationId}')">&times;</button>
    `;
    
    // Добавяне на стилове
    notification.style.cssText = `
        background: white;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 14px;
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        pointer-events: auto;
        border-left: 5px solid #27ae60;
        max-width: 400px;
    `;
    
    // Стилове според типа
    if (type === 'success') {
        notification.style.borderLeftColor = '#27ae60';
        notification.style.background = 'linear-gradient(135deg, #ffffff 0%, #f8fff9 100%)';
    } else if (type === 'error') {
        notification.style.borderLeftColor = '#e74c3c';
        notification.style.background = 'linear-gradient(135deg, #ffffff 0%, #fff8f8 100%)';
    }
    
    // Добавяне на известието в контейнера
    container.appendChild(notification);
    
    // Показване с анимация
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Автоматично затваряне
    setTimeout(() => {
        removeNotification(notificationId);
    }, 4000);
    
    return notificationId;
}

// ФУНКЦИЯ ЗА ПРЕМАХВАНЕ НА ИЗВЕСТИЕ
function removeNotification(notificationId) {
    const notification = document.getElementById(notificationId);
    if (!notification) return;
    
    notification.classList.remove('show');
    notification.classList.add('hide');
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

// Добавяне на CSS анимации за известия
if (!document.querySelector('#notification-animations')) {
    const style = document.createElement('style');
    style.id = 'notification-animations';
    style.textContent = `
        .notification.show {
            transform: translateX(0);
            opacity: 1;
        }
        .notification.hide {
            transform: translateX(100%);
            opacity: 0;
        }
        .notification-icon {
            width: 28px;
            height: 28px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: white;
            font-weight: bold;
        }
        .notification-success .notification-icon {
            background: #27ae60;
        }
        .notification-error .notification-icon {
            background: #e74c3c;
        }
        .notification-content {
            flex: 1;
        }
        .notification-message {
            color: #2c3e50;
            font-size: 14px;
            line-height: 1.4;
        }
        .notification-close {
            background: none;
            border: none;
            color: #95a5a6;
            font-size: 20px;
            cursor: pointer;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.2s;
            flex-shrink: 0;
        }
        .notification-close:hover {
            background: #f5f5f5;
            color: #e74c3c;
        }
    `;
    document.head.appendChild(style);
}