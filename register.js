// register.js - JavaScript за register страница

document.addEventListener('DOMContentLoaded', function() {
    initRegisterPage();
});

function initRegisterPage() {
    // Toggle password visibility
    setupPasswordToggles();
    
    // Password strength checker
    setupPasswordStrength();
    
    // Password match checker
    setupPasswordMatch();
    
    // Form validation
    setupFormValidation();
    
    // Auto focus
    const emailField = document.getElementById('reg_email');
    if (emailField) {
        emailField.focus();
    }
}

function setupPasswordToggles() {
    const toggleButtons = document.querySelectorAll('.register-toggle-password');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const fieldId = this.closest('.password-wrapper').querySelector('input').id;
            toggleRegPassword(fieldId);
        });
    });
}

function toggleRegPassword(fieldId) {
    const passwordInput = document.getElementById(fieldId);
    const toggleIcon = passwordInput.parentNode.querySelector('.register-toggle-password i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        toggleIcon.className = 'fas fa-eye';
    }
}

function setupPasswordStrength() {
    const passwordInput = document.getElementById('reg_password');
    if (!passwordInput) return;
    
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strengthBar = document.querySelector('.strength-bar');
        const strengthText = document.querySelector('.strength-text span');
        const passwordStrength = document.querySelector('.password-strength');
        
        if (password.length === 0) {
            passwordStrength.style.display = 'none';
            return;
        }
        
        passwordStrength.style.display = 'block';
        
        let strength = 0;
        let message = 'Слаба';
        let color = '#ff0000';
        let width = '25%';
        
        // Проверки за сила
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        switch(strength) {
            case 0:
                message = 'Много слаба';
                color = '#ff0000';
                width = '10%';
                break;
            case 1:
                message = 'Слаба';
                color = '#ff6600';
                width = '25%';
                break;
            case 2:
                message = 'Средна';
                color = '#ffcc00';
                width = '50%';
                break;
            case 3:
                message = 'Добра';
                color = '#99cc00';
                width = '75%';
                break;
            case 4:
                message = 'Много добра';
                color = '#339900';
                width = '100%';
                break;
        }
        
        strengthBar.style.setProperty('--strength-width', width);
        strengthBar.style.setProperty('--strength-color', color);
        strengthText.textContent = message;
        strengthText.style.color = color;
    });
}

function setupPasswordMatch() {
    const confirmPassword = document.getElementById('reg_confirm_password');
    if (!confirmPassword) return;
    
    confirmPassword.addEventListener('input', function() {
        const password = document.getElementById('reg_password').value;
        const confirmPasswordValue = this.value;
        const matchDiv = document.getElementById('passwordMatch');
        
        if (confirmPasswordValue.length === 0) {
            matchDiv.innerHTML = '';
            return;
        }
        
        if (password === confirmPasswordValue) {
            matchDiv.innerHTML = '<span style="color:#339900;"><i class="fas fa-check-circle"></i> Паролите съвпадат</span>';
        } else {
            matchDiv.innerHTML = '<span style="color:#e60000;"><i class="fas fa-times-circle"></i> Паролите не съвпадат</span>';
        }
    });
}

function setupFormValidation() {
    const form = document.getElementById('registerForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        const password = document.getElementById('reg_password').value;
        const confirmPassword = document.getElementById('reg_confirm_password').value;
        const terms = document.getElementById('reg_terms').checked;
        
        let errors = [];
        
        if (password.length < 6) {
            errors.push('Паролата трябва да е поне 6 символа');
        }
        
        if (password !== confirmPassword) {
            errors.push('Паролите не съвпадат');
        }
        
        if (!terms) {
            errors.push('Трябва да се съгласите с Общите условия');
        }
        
        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join('\n'));
        }
    });
}