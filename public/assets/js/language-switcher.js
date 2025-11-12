// Language Switcher JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Update language display on page load
    updateLanguageDisplay();
});

function updateLanguageDisplay() {
    const currentLang = document.documentElement.lang || 'ar';
    const currentLangSpan = document.querySelector('.current-lang');
    
    if (currentLangSpan) {
        currentLangSpan.textContent = currentLang === 'ar' ? 'AR' : 'EN';
    }
    
    // Update active state in language menu
    const languageLinks = document.querySelectorAll('.language-menu a');
    languageLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('data-lang') === currentLang) {
            link.classList.add('active');
        }
    });
}

function toggleLanguageMenu() {
    const menu = document.getElementById('languageMenu');
    if (menu) {
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }
}

function changeLanguage(lang, event) {
    if (event) {
        event.preventDefault();
    }

    // Save the selected language to localStorage
    localStorage.setItem('language', lang);

    // Send request to change language
    fetch(`/locale/${lang}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        }
    }).then(response => {
        // Reload the page to update content
        window.location.reload();
    }).catch(error => {
        console.error('Error:', error);
        // Fallback to direct redirect
        window.location.href = `/locale/${lang}`;
    });
}

// Close language menu when clicking outside
document.addEventListener('click', function(event) {
    const languageBtn = document.querySelector('.language-btn');
    const languageMenu = document.getElementById('languageMenu');

    if (languageBtn && languageMenu && 
        !languageBtn.contains(event.target) && 
        !languageMenu.contains(event.target)) {
        languageMenu.style.display = 'none';
    }
});