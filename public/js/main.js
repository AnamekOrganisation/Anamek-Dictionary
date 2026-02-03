/**
 * Amawal - Dictionnaire Amazigh
 * Main Entry Point
 */

document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize UI Utilities
    if (window.initLanguageDropdown) initLanguageDropdown();
    if (window.initAccountDropdown) initAccountDropdown();
    if (window.initSocialMediaAutoSave) initSocialMediaAutoSave();
    
    // 2. Initialize Script Toggling (Tifinagh/Latin)
    if (window.initGlobalScriptToggle) initGlobalScriptToggle();
    
    // 3. Initialize Search Functionality
    if (window.initSearchLanguageDropdown) initSearchLanguageDropdown();
    if (window.initSearchAutocomplete) initSearchAutocomplete();
    
    // 4. Display Recent Searches
    if (window.displayRecentSearches) displayRecentSearches();

    // Attach search form submit listener if it exists
    const searchForm = document.getElementById('searchForm');
    if (searchForm && window.performSearch) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            performSearch();
        });
    }
});

/* === Helper: Toast Notification === */
function showToast(message) {
    var toast = document.querySelector('.toast-notification');
    if (!toast) {
        toast = document.createElement('div');
        toast.className = 'toast-notification';
        document.body.appendChild(toast);
    }
    
    // SVG Checkmark
    var checkIcon = '<svg xmlns="http://www.w3.org/2000/svg" height="20" viewBox="0 0 24 24" width="20" fill="#4ade80"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/></svg>';
    
    toast.innerHTML = checkIcon + '<span>' + message + '</span>';
    
    // Force reflow for animation
    void toast.offsetWidth;
    
    toast.classList.add('show');
    
    if (window.toastTimeout) clearTimeout(window.toastTimeout);
    window.toastTimeout = setTimeout(function() {
        toast.classList.remove('show');
    }, 3000);
}

/* === Word Actions (Copy/Share) === */
window.copyWord = function(text) {
    if (!text) return;
    
    // Modern API
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            showToast('Copié : ' + text);
        }).catch(function(err) {
            console.error('Async copy failed', err);
            fallbackCopy(text);
        });
    } else {
        fallbackCopy(text);
    }
};

function fallbackCopy(text) {
    var textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-9999px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        var successful = document.execCommand('copy');
        if(successful) showToast('Copié : ' + text);
        else showToast('Erreur lors de la copie');
    } catch (err) {
        console.error('Fallback copy failed', err);
        showToast('Erreur : Impossible de copier');
    }
    
    document.body.removeChild(textArea);
}

window.shareWord = function(text, url) {
    if (!url) url = window.location.href;
    
    // Use existing share modal if available
    if (window.showShareModal) {
        window.showShareModal(text, url);
    } else if (navigator.share) {
        // Fallback to native share
        navigator.share({
            title: 'Anamek Dictionary',
            text: 'Définition de: ' + text,
            url: url
        }).catch(function(err) {
            console.log('Share canceled', err);
        });
    } else {
        // Final fallback: copy URL
        window.copyWord(url);
        showToast('Lien copié pour le partage');
    }
};
