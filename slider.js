// Слайдер за най-новите книги в index.php
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.querySelector('.new-books-slider');
    const prevBtn = document.getElementById('new-prev-btn');
    const nextBtn = document.getElementById('new-next-btn');
    const bookCards = document.querySelectorAll('.new-book-card');
    
    if (!slider || !prevBtn || !nextBtn || bookCards.length === 0) return;
    
    const cardWidth = bookCards[0].offsetWidth + 20; // ширина + gap
    let currentPosition = 0;
    const maxPosition = -cardWidth * (bookCards.length - 4); // показваме 4 книги
    
    // Бутон "Назад"
    prevBtn.addEventListener('click', function() {
        if (currentPosition < 0) {
            currentPosition += cardWidth * 2;
            if (currentPosition > 0) currentPosition = 0;
            slider.style.transform = `translateX(${currentPosition}px)`;
        }
    });
    
    // Бутон "Напред"
    nextBtn.addEventListener('click', function() {
        if (currentPosition > maxPosition) {
            currentPosition -= cardWidth * 2;
            if (currentPosition < maxPosition) currentPosition = maxPosition;
            slider.style.transform = `translateX(${currentPosition}px)`;
        }
    });
    
    // AJAX за добавяне в кошницата за новите книги
    const newCartForms = document.querySelectorAll('.new-book-card .add-to-cart-form');
    
    newCartForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn ? submitBtn.textContent : '';
            
            // Показваме индикация
            if (submitBtn) {
                submitBtn.textContent = 'Добавяне...';
                submitBtn.disabled = true;
            }
            
            // Изпращаме AJAX заявка
            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    return response.json();
                }
                throw new Error('Невалиден отговор');
            })
            .then(data => {
                if (data.success) {
                    // Обновяваме брояча в хедъра
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                        // Анимация
                        cartCount.style.transform = 'scale(1.2)';
                        setTimeout(() => {
                            cartCount.style.transform = 'scale(1)';
                        }, 300);
                    }
                    
                    // Показваме известие
                    if (typeof showNotification === 'function') {
                        showNotification(data.message, 'success');
                    } else {
                        showSimpleNotification(data.message, 'success');
                    }
                } else {
                    if (typeof showNotification === 'function') {
                        showNotification(data.message, 'error');
                    } else {
                        showSimpleNotification(data.message, 'error');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // При грешка, изпращаме формата нормално
                this.submit();
            })
            .finally(() => {
                // Възстановяваме бутона
                if (submitBtn) {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            });
        });
    });
    
    // Проста функция за известия
    function showSimpleNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${type === 'success' ? '#4CAF50' : '#f44336'};
            color: white;
            border-radius: 4px;
            z-index: 10000;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 3000);
    }
    
    // Добавяне на CSS анимации
    if (!document.querySelector('#new-books-notifications')) {
        const style = document.createElement('style');
        style.id = 'new-books-notifications';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
});