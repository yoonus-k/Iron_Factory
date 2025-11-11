// Toggle password visibility
function togglePassword() {
    // اجلب كل الحقول اللي نوعها password أو text (ممكن تكون انقلبت Text سابقاً)
    const passwordInputs = document.querySelectorAll('input[type="password"], input[type="text"].password-field');
    const eyeIcons = document.querySelectorAll('#eye-icon');
    let show = true;

    passwordInputs.forEach(input => {
        if (input.type === 'text') {
            show = false;
        }
    });

    passwordInputs.forEach(input => {
        if (show) {
            // أظهر الكل
            input.type = 'text';
            input.classList.add('password-field');
        } else {
            // أخفِ الكل
            input.type = 'password';
            input.classList.add('password-field');
        }
    });

    eyeIcons.forEach(icon => {
        if (show) {
            icon.innerHTML =
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268-2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
        } else {
            icon.innerHTML =
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        }
    });
}

// Handle form submission with loading state
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoading = document.getElementById('btnLoading');

        if (submitBtn) submitBtn.disabled = true;
        if (btnText) btnText.classList.add('hidden');
        if (btnLoading) {
            btnLoading.classList.remove('hidden');
            btnLoading.classList.add('flex');
        }
    });
}

// Animation on page load
window.addEventListener('load', function() {
    const glassEffect = document.querySelector('.glass-effect');
    if (!glassEffect) return;
    glassEffect.style.opacity = '0';
    glassEffect.style.transform = 'translateY(20px)';

    setTimeout(function() {
        glassEffect.style.transition = 'all 0.6s ease-out';
        glassEffect.style.opacity = '1';
        glassEffect.style.transform = 'translateY(0)';
    }, 100);
});

// Auto-hide success messages after 5 seconds
// 👇 هنا تحتاج تنقل الشرط من الـBlade إلى JS (مثلاً باستخدام متغير جافاسكربت)
setTimeout(function() {
    const successAlert = document.querySelector('.bg-green-50');
    if (successAlert) {
        successAlert.style.transition = 'opacity 0.5s ease-out';
        successAlert.style.opacity = '0';
        setTimeout(function() {
            successAlert.remove();
        }, 500);
    }
}, 5000);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Alt + L to focus on email input
    if (e.altKey && e.key.toLowerCase() === 'l') {
        e.preventDefault();
        const emailInput = document.getElementById('email');
        if (emailInput) emailInput.focus();
    }
});
