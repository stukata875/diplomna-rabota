// cart.js - ЕДИНСТВЕН И КОРЕКТЕН
document.addEventListener('DOMContentLoaded', function() {
    console.log('Cart системата е заредена успешно!');
    
    // Зареди началния брой на количката
    loadInitialCartCount();
    
    // 1. СЛУШАТЕЛ ЗА ВСИЧКИ БУТОНИ С КЛАС .add-to-cart-btn
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            // Намери ID на продукта
            let productId = this.getAttribute('data-id');
            
            if (!productId) {
                console.error('Бутонът няма data-id атрибут!');
                showToast('✗ Грешка: Липсва ID на продукта', 'error');
                return;
            }
            
            console.log('Добавяне на продукт ID:', productId);
            addToCart(productId, this);
        });
    });
    
    // 2. СЛУШАТЕЛ ЗА ВСИЧКИ БУТОНИ С onclick="addToCart(...)"
    // Това е за backwards compatibility
    document.querySelectorAll('[onclick*="addToCart"]').forEach(btn => {
        const onclickText = btn.getAttribute('onclick');
        const match = onclickText.match(/addToCart\((\d+)/);
        if (match) {
            const productId = match[1];
            btn.setAttribute('data-id', productId);
            btn.classList.add('add-to-cart-btn');
            btn.removeAttribute('onclick');
        }
    });
});

// ГЛОБАЛНА ФУНКЦИЯ ЗА ДОБАВЯНЕ
window.addToCart = function(productId, buttonElement = null) {
    console.log('Извикване на addToCart за ID:', productId);
    
    const button = buttonElement || (window.event ? window.event.target : null);
    
    // Запази оригиналния текст
    let originalText = 'Добави в кошницата';
    let originalHTML = '';
    if (button) {
        originalHTML = button.innerHTML;
        originalText = button.textContent;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Добавя се...';
    }
    
    // Изпрати заявката
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'id=' + productId
    })
    .then(response => {
        // Проверка за JSON отговор
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Сървърът върна невалиден отговор (не е JSON)');
        }
        return response.json();
    })
    .then(data => {
        console.log('Отговор от add_to_cart.php:', data);
        
        if (button) {
            button.disabled = false;
            button.innerHTML = originalHTML;
        }
        
        if (data.success) {
            // Обнови брояча
            updateCartCount(data.cart_count);
            // Покажи съобщение
            showToast('✓ ' + data.message, 'success');
        } else {
            showToast('✗ ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Грешка при добавяне в количката:', error);
        if (button) {
            button.disabled = false;
            button.innerHTML = originalHTML;
        }
        showToast('✗ Възникна грешка при добавяне', 'error');
    });
};

// Функция за зареждане на началния брой
function loadInitialCartCount() {
    fetch('get_cart_count.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Грешка при заявка към get_cart_count.php');
            }
            return response.json();
        })
        .then(data => {
            console.log('Данни от get_cart_count.php:', data);
            
            // Поддържай двата формата (cart_count и count)
            const count = data.cart_count !== undefined ? data.cart_count : 
                         data.count !== undefined ? data.count : 0;
            
            updateCartCount(count);
        })
        .catch(error => {
            console.error('Грешка при зареждане на брояча:', error);
            // Използвай PHP стойността като fallback
            const cartCountEl = document.getElementById('cart-count');
            if (cartCountEl && cartCountEl.textContent) {
                const count = parseInt(cartCountEl.textContent) || 0;
                updateCartCount(count);
            }
        });
}

// Функция за обновяване на брояча
function updateCartCount(count) {
    const el = document.getElementById('cart-count');
    if (el) {
        el.textContent = count;
        // Анимация
        el.style.transform = 'scale(1.5)';
        setTimeout(() => {
            el.style.transform = 'scale(1)';
        }, 300);
    }
}

// Функция за toast съобщения
function showToast(message, type = 'success') {
    // Премахни старите toast-ове
    document.querySelectorAll('.cart-toast').forEach(el => el.remove());
    
    const toast = document.createElement('div');
    toast.className = `cart-toast ${type}`;
    toast.textContent = message;
    
    // Стилове за toast
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4CAF50' : '#f44336'};
        color: white;
        border-radius: 6px;
        z-index: 9999;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-weight: bold;
        animation: slideInRight 0.3s ease-out;
        max-width: 300px;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
    
    // Добави CSS анимации (само веднъж)
    if (!document.querySelector('#cart-toast-animations')) {
        const style = document.createElement('style');
        style.id = 'cart-toast-animations';
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
}

// Експортирай функциите за глобална употреба
window.updateCartCount = updateCartCount;
window.showToast = showToast;