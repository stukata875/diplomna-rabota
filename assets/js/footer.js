// JavaScript за формата за абонамент във footer
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.querySelector('.newsletter-form');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const emailInput = this.querySelector('input[type="email"]');
            const email = emailInput.value.trim();
            
            // Валидация на имейл
            if (!isValidEmail(email)) {
                showFormError('Моля, въведете валиден имейл адрес.', emailInput);
                return;
            }
            
            // Показване на съобщение за успех
            showFormSuccess('Благодарим ви за абонамента!', emailInput);
            
            // Изчистване на полето
            emailInput.value = '';
        });
    }
    
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    function showFormError(message, input) {
        // Премахване на съществуващи съобщения
        removeExistingMessages();
        
        // Създаване на съобщение за грешка
        const errorDiv = document.createElement('div');
        errorDiv.className = 'form-error';
        errorDiv.textContent = message;
        errorDiv.style.cssText = `
            color: #dc2626;
            font-size: 13px;
            margin-top: 8px;
            padding: 8px 12px;
            background: #fef2f2;
            border-radius: 6px;
            border: 1px solid #fecaca;
            animation: slideDown 0.3s ease;
        `;
        
        input.parentNode.appendChild(errorDiv);
        
        // Фокус върху полето
        input.focus();
        input.style.borderColor = '#dc2626';
        
        // Възстановяване на цвета след 3 секунди
        setTimeout(() => {
            input.style.borderColor = '#d1d5db';
        }, 3000);
    }
    
    function showFormSuccess(message, input) {
        // Премахване на съществуващи съобщения
        removeExistingMessages();
        
        // Създаване на съобщение за успех
        const successDiv = document.createElement('div');
        successDiv.className = 'form-success';
        successDiv.textContent = message;
        successDiv.style.cssText = `
            color: #059669;
            font-size: 13px;
            margin-top: 8px;
            padding: 8px 12px;
            background: #f0fdf4;
            border-radius: 6px;
            border: 1px solid #bbf7d0;
            animation: slideDown 0.3s ease;
        `;
        
        input.parentNode.appendChild(successDiv);
        
        // Премахване на съобщението след 5 секунди
        setTimeout(() => {
            if (successDiv.parentNode) {
                successDiv.style.animation = 'slideUp 0.3s ease';
                setTimeout(() => successDiv.remove(), 300);
            }
        }, 5000);
    }
    
    function removeExistingMessages() {
        const messages = document.querySelectorAll('.form-error, .form-success');
        messages.forEach(message => message.remove());
    }
    
    // Добавяне на CSS анимации
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideUp {
            from { opacity: 1; transform: translateY(0); }
            to { opacity: 0; transform: translateY(-10px); }
        }
    `;
    document.head.appendChild(style);
});