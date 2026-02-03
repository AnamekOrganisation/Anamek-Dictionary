/**
 * UI Utilities Module
 * Handles dropdowns, toasts, social media link saving, and general UI interactions.
 */

window.initLanguageDropdown = function() {
    const dropdown = document.getElementById('languageDropdown');
    const languageBtn = document.getElementById('languageBtn');
    const accountDropdown = document.getElementById('accountDropdown');
    
    if (!dropdown || !languageBtn) return;

    languageBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
        if (accountDropdown) accountDropdown.classList.remove('show');
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.language-container')) {
            dropdown.classList.remove('show');
        }
    });
};

window.initAccountDropdown = function() {
    const accountBtn = document.querySelector('.account-container .account');
    const dropdown = document.getElementById('accountDropdown');
    const langDropdown = document.getElementById('languageDropdown');

    if (!accountBtn || !dropdown) return;

    accountBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
        if (langDropdown) langDropdown.classList.remove('show');
    });
};

window.showToast = function(message) {
    let toast = document.querySelector('.toast-notification');
    if (!toast) {
        toast = document.createElement('div');
        toast.className = 'toast-notification';
        document.body.appendChild(toast);
    }
    toast.innerText = message;
    toast.classList.add('show');
    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
};

window.initSocialMediaAutoSave = function() {
    const socialInputs = document.querySelectorAll('.social-input');
    
    socialInputs.forEach(input => {
        let saveTimeout;
        
        input.addEventListener('input', function() {
            clearTimeout(saveTimeout);
            this.classList.remove('success', 'error');
            this.classList.add('saving');
            
            saveTimeout = setTimeout(() => {
                saveSocialLink(this);
            }, 1000);
        });
    });
};

window.saveSocialLink = async function(input) {
    const platform = input.dataset.platform;
    const url = input.value;
    
    try {
        const formData = new FormData();
        formData.append('platform', platform);
        formData.append('url', url);
        formData.append('csrf_token', document.querySelector('[name="csrf_token"]')?.value || '');
        
        const response = await fetch(BASE_URL + '/dashboard/update-social', {
            method: 'POST',
            body: formData
        });
        
        if (response.ok) {
            input.classList.remove('saving');
            input.classList.add('success');
            setTimeout(() => input.classList.remove('success'), 2000);
        } else {
            throw new Error('Save failed');
        }
    } catch (error) {
        input.classList.remove('saving');
        input.classList.add('error');
        setTimeout(() => input.classList.remove('error'), 2000);
    }
};

window.showShareModal = function(text, url) {
    let modal = document.getElementById('shareModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'shareModal';
        modal.className = 'share-modal';
        modal.innerHTML = `
            <div class="share-modal-content">
                <div class="share-modal-header">
                    <h3>Partager</h3>
                    <button class="share-modal-close">&times;</button>
                </div>
                <div class="share-options">
                    <a href="#" target="_blank" class="share-option twitter x-twitter">
                        <i class="fab fa-x"></i> share on X
                    </a>
                    <a href="#" target="_blank" class="share-option facebook">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </a>
                    <a href="#" target="_blank" class="share-option whatsapp">
                        <i class="fab fa-whatsapp"></i> WhatsApp
                    </a>
                    <button class="share-option copy-link">
                        <i class="fas fa-link"></i> Copier le lien
                    </button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);

        modal.querySelector('.share-modal-close').onclick = () => modal.classList.remove('active');
        modal.onclick = (e) => { if (e.target === modal) modal.classList.remove('active'); };
    }
    
    const xBtn = modal.querySelector('.twitter');
    if (xBtn) xBtn.href = `https://x.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`;
    
    const fbBtn = modal.querySelector('.facebook');
    fbBtn.href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}&quote=${encodeURIComponent(text)}`;
    
    const waBtn = modal.querySelector('.whatsapp');
    waBtn.href = `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`;
    
    [xBtn, fbBtn].forEach(btn => {
        if (!btn) return;
        btn.onclick = (e) => {
            e.preventDefault();
            window.open(btn.href, 'share-window', 'width=600,height=400,menubar=no,toolbar=no,resizable=yes,scrollbars=yes');
            return false;
        };
    });

    const copyBtn = modal.querySelector('.copy-link');
    copyBtn.onclick = () => {
        if (window.copyToClipboard) window.copyToClipboard(url, 'Lien copié !');
    };
    
    modal.classList.add('active');
};

// Global click listener for dropdowns
document.addEventListener('click', function(e) {
    if (!e.target.closest('.secondary-brand') && !e.target.closest('.account-container')) {
        const langDropdown = document.getElementById('languageDropdown');
        const accountDropdown = document.getElementById('accountDropdown');
        if (langDropdown) langDropdown.classList.remove('show');
        if (accountDropdown) accountDropdown.classList.remove('show');
    }
});

// Mobile Menu and Global Handlers
document.addEventListener('DOMContentLoaded', function() {
    const mobileTrigger = document.querySelector('.mobile');
    const navMenu = document.querySelector('.nav-menu');
    
    // Mobile Menu Toggle
    if (mobileTrigger && navMenu) {
        mobileTrigger.addEventListener('click', () => {
            mobileTrigger.classList.toggle('active');
            navMenu.classList.toggle('active');
        });
    }

    // Scroll to Top/Search handler (Removed headerSearchBtn toggle as requested)

    // Smooth Scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href.startsWith('#')) {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });
});
