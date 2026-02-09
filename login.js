document.addEventListener('DOMContentLoaded', function() {
    initLoginPage();
});

function initLoginPage() {
    // Функция за показване/скриване на парола
    const toggleButtons = document.querySelectorAll('.toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.password-wrapper').querySelector('input');
            togglePassword(input, this);
        });
    });
    
    // Автоматично фокус на email полето
    const emailField = document.getElementById('email');
    if (emailField && !emailField.value) {
        emailField.focus();
    }
    
    // Ефект при грешка
    const errorAlert = document.querySelector('.alert-error');
    if (errorAlert) {
        errorAlert.style.animation = 'shake 0.5s ease-in-out';
    }
}

function togglePassword(input, button) {
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fas fa-eye-slash';
        button.setAttribute('title', 'Скрий паролата');
    } else {
        input.type = 'password';
        icon.className = 'fas fa-eye';
        button.setAttribute('title', 'Покажи паролата');
    }
}