// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.toggle-password i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Password strength meter
const passwordInput = document.getElementById('password');
if (passwordInput) {
    passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strengthMeter = document.querySelector('.strength-meter');
        const strengthText = document.querySelector('.strength-text');
        
        if (!strengthMeter || !strengthText) return;
        
        let strength = 0;
        
        // Check password length
        if (password.length >= 8) strength += 25;
        
        // Check for uppercase letters
        if (/[A-Z]/.test(password)) strength += 25;
        
        // Check for lowercase letters
        if (/[a-z]/.test(password)) strength += 25;
        
        // Check for numbers and special characters
        if (/[0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) strength += 25;
        
        // Update the strength meter
        const meterElement = strengthMeter.querySelector('::before') || strengthMeter;
        meterElement.style.width = strength + '%';
        
        // Update color based on strength
        if (strength <= 25) {
            meterElement.style.backgroundColor = '#e74a3b';
            strengthText.textContent = 'Weak';
            strengthText.style.color = '#e74a3b';
        } else if (strength <= 50) {
            meterElement.style.backgroundColor = '#f6c23e';
            strengthText.textContent = 'Fair';
            strengthText.style.color = '#f6c23e';
        } else if (strength <= 75) {
            meterElement.style.backgroundColor = '#4e73df';
            strengthText.textContent = 'Good';
            strengthText.style.color = '#4e73df';
        } else {
            meterElement.style.backgroundColor = '#1cc88a';
            strengthText.textContent = 'Strong';
            strengthText.style.color = '#1cc88a';
        }
    });
}

// Password match validation
const confirmPasswordInput = document.getElementById('confirm_password');
if (confirmPasswordInput && passwordInput) {
    confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirmPassword = this.value;
        const matchText = document.querySelector('.password-match');
        
        if (!matchText) return;
        
        if (password === confirmPassword) {
            matchText.textContent = 'Passwords match';
            matchText.style.color = '#1cc88a';
        } else {
            matchText.textContent = 'Passwords do not match';
            matchText.style.color = '#e74a3b';
        }
    });
}

// Form validation
const registerForm = document.getElementById('registerForm');
if (registerForm) {
    registerForm.addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
            return false;
        }
        
        // Check password strength
        if (password.length < 8 || 
            !/[A-Z]/.test(password) || 
            !/[a-z]/.test(password) || 
            !/[0-9!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
            e.preventDefault();
            alert('Password must be at least 8 characters long and include uppercase, lowercase, and special characters or numbers.');
            return false;
        }
        
        return true;
    });
}

// Login form validation
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        
        if (!email || !password) {
            e.preventDefault();
            alert('Please fill in all fields');
            return false;
        }
        
        return true;
    });
}