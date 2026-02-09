// terms.js - JavaScript за terms.php и privacy.php

document.addEventListener('DOMContentLoaded', function() {
    initTermsPrivacyPage();
});

function initTermsPrivacyPage() {
    // Плавно показване на секции при скрол
    setupScrollAnimations();
    
    // Ховер ефект за бутоните
    setupButtonHovers();
    
    // Cookie функции (само за privacy.php)
    if (document.querySelector('.privacy-page')) {
        setupCookieButtons();
    }
}

function setupScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Наблюдаване на всички секции
    const sections = document.querySelectorAll('.terms-page h2, .terms-list, .terms-table, .terms-contact, .privacy-section, .privacy-table, .data-protection-box');
    sections.forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(section);
    });
}

function setupButtonHovers() {
    const buttons = document.querySelectorAll('.terms-btn, .privacy-btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

function setupCookieButtons() {
    // Бутон "Приемам бисквитки"
    const acceptBtn = document.querySelector('[onclick*="acceptCookies"]');
    if (acceptBtn) {
        acceptBtn.removeAttribute('onclick');
        acceptBtn.addEventListener('click', acceptCookies);
    }
    
    // Бутон "Отказвам ненужни"
    const declineBtn = document.querySelector('[onclick*="declineCookies"]');
    if (declineBtn) {
        declineBtn.removeAttribute('onclick');
        declineBtn.addEventListener('click', declineCookies);
    }
}

function acceptCookies() {
    document.cookie = "cookies_accepted=true; max-age=31536000; path=/";
    showNotification('Благодарим ви! Вашето съгласие е записано.', 'success');
}

function declineCookies() {
    document.cookie = "cookies_accepted=false; max-age=31536000; path=/";
    showNotification('Ненужните бисквитки са деактивирани.', 'warning');
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#27ae60' : '#e74c3c'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        animation: slideInRight 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    `;
    notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'exclamation'}-circle"></i> ${message}`;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Добавяне на CSS анимации
const style = document.createElement('style');
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