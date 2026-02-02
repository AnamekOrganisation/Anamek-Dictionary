// ⴰⵙⵓⵙⵎ ⵉⴳⴰ ⵓⵔⵖ

document.addEventListener('DOMContentLoaded', function() {
    // Initialize language dropdown
    initLanguageDropdown();
    
    // Initialize global script toggle
    initGlobalScriptToggle();
    
    // Initialize social media auto-save
    initSocialMediaAutoSave();
    
    // Initialize search language dropdown
    initSearchLanguageDropdown();

    // Initialize search autocomplete
    initSearchAutocomplete();

    // Display recent searches
    displayRecentSearches();

    // Initialize account dropdown
    initAccountDropdown();
});

/* =========================================
   Dropdown Management
   ========================================= */

function initLanguageDropdown() {
    const dropdown = document.getElementById('languageDropdown');
    const languageBtn = document.querySelector('.secondary-brand .language');
    const accountDropdown = document.getElementById('accountDropdown');
    
    if (!dropdown || !languageBtn) return;

    // Toggle dropdown on click
    languageBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
        if (accountDropdown) accountDropdown.classList.remove('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.language-container')) {
            dropdown.classList.remove('show');
        }
    });
}

function initAccountDropdown() {
    const accountBtn = document.querySelector('.account-container .account');
    const dropdown = document.getElementById('accountDropdown');
    const langDropdown = document.getElementById('languageDropdown');

    if (!accountBtn || !dropdown) return;

    accountBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        dropdown.classList.toggle('show');
        if (langDropdown) langDropdown.classList.remove('show');
    });
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.secondary-brand') && !e.target.closest('.account-container')) {
        const langDropdown = document.getElementById('languageDropdown');
        const accountDropdown = document.getElementById('accountDropdown');
        if (langDropdown) langDropdown.classList.remove('show');
        if (accountDropdown) accountDropdown.classList.remove('show');
    }
});

/* =========================================
   Script Toggling Logic (Tifinagh/Latin)
   ========================================= */

function initGlobalScriptToggle() {
    // Default to 'tfng' if not set
    const currentScript = localStorage.getItem('preferred_script') || 'tfng';
    
    // Apply saved preference
    applyScriptToAll(currentScript);
    
    // Update button states
    updateToggleButtons(currentScript);
    
    // Add click listeners to any global toggle buttons
    const toggleButtons = document.querySelectorAll('.script-toggle-btn');
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const script = this.dataset.script;
            setGlobalScript(script);
        });
    });
}

function setGlobalScript(script) {
    localStorage.setItem('preferred_script', script);
    applyScriptToAll(script);
    updateToggleButtons(script);
}

function applyScriptToAll(script) {
    // Update all elements with data-tfng and data-lat attributes
    document.querySelectorAll('[data-tfng][data-lat]').forEach(element => {
        // Safety Check: To prevent wiping out complex HTML structures (like "Word of the Day"),
        // only replace content if the element is empty, has no element children, 
        // or explicitly has a 'safe-text-replace' class.
        if (element.children.length === 0 || element.classList.contains('safe-text-replace')) {
            if (script === 'tfng') {
                element.textContent = element.dataset.tfng;
            } else {
                element.textContent = element.dataset.lat;
            }
        }
    });
    
    // Update all proverb displays
    document.querySelectorAll('.proverb-tfng, .proverb-lat').forEach(el => {
        if (script === 'tfng') {
            el.style.display = el.classList.contains('proverb-tfng') ? 'block' : 'none';
        } else {
            el.style.display = el.classList.contains('proverb-lat') ? 'block' : 'none';
        }
    });
    
    // Update word displays
    document.querySelectorAll('.word-display').forEach(word => {
        if (word.dataset.tfng && word.dataset.lat) {
            word.textContent = script === 'tfng' ? word.dataset.tfng : word.dataset.lat;
        }
    });
    
    // Update any other script-switchable content
    document.querySelectorAll('.tifinagh-text').forEach(el => {
        el.style.display = script === 'tfng' ? 'block' : 'none';
    });
    
    document.querySelectorAll('.latin-text').forEach(el => {
        el.style.display = script === 'lat' ? 'block' : 'none';
    });
}

function updateToggleButtons(script) {
    document.querySelectorAll('.script-toggle-btn').forEach(btn => {
        if (btn.dataset.script === script) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    
    // Also update old-style buttons if they exist
    document.querySelectorAll('.script-btn').forEach(btn => {
        const isTfngBtn = btn.classList.contains('proverb-tfng-btn');
        const isLatBtn = btn.classList.contains('proverb-lat-btn');
        
        if ((isTfngBtn && script === 'tfng') || (isLatBtn && script === 'lat')) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

// Used specifically if there is a button to toggle search script only, 
// though generally we might want to decouple this.
function toggleScriptInSearch() {
    const currentScript = localStorage.getItem('preferred_script') || 'tfng';
    const newScript = currentScript === 'tfng' ? 'lat' : 'tfng';
    setGlobalScript(newScript);
}

/* =========================================
   Search Functionality
   ========================================= */

function performSearch() {
    const searchInput = document.getElementById('search-input');
    if (searchInput && searchInput.value.trim()) {
        window.location.href = BASE_URL + '/search?q=' + encodeURIComponent(searchInput.value);
    }
}

// Search Form Submission
const searchForm = document.getElementById('searchForm');
if (searchForm) {
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
        performSearch();
    });
}

// Search Autocomplete
let autocompleteTimeout;

function initSearchAutocomplete() {
    const searchInput = document.getElementById('search-input');
    const autocompleteResults = document.getElementById('autocomplete-results');
    
    if (!searchInput || !autocompleteResults) return;
    
    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(autocompleteTimeout);
        
        if (query.length < 2) {
            autocompleteResults.style.display = 'none';
            return;
        }
        
        autocompleteTimeout = setTimeout(() => {
            fetchAutocompleteSuggestions(query, autocompleteResults);
        }, 300);
    });
    
    // Handle keyboard navigation
    searchInput.addEventListener('keydown', function(e) {
        const items = autocompleteResults.querySelectorAll('.autocomplete-item');
        const currentActive = autocompleteResults.querySelector('.autocomplete-item.active');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (!currentActive) {
                items[0]?.classList.add('active');
            } else {
                const next = currentActive.nextElementSibling;
                currentActive.classList.remove('active');
                if (next && next.classList.contains('autocomplete-item')) {
                    next.classList.add('active');
                } else {
                    items[0]?.classList.add('active');
                }
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (currentActive) {
                const prev = currentActive.previousElementSibling;
                currentActive.classList.remove('active');
                if (prev && prev.classList.contains('autocomplete-item')) {
                    prev.classList.add('active');
                } else {
                    items[items.length - 1]?.classList.add('active');
                }
            }
        } else if (e.key === 'Enter') {
            if (currentActive) {
                e.preventDefault();
                currentActive.click();
            }
        } else if (e.key === 'Escape') {
            autocompleteResults.style.display = 'none';
        }
    });
    
    // Close on click outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.search-bar-container') && !e.target.closest('.search-container')) {
            autocompleteResults.style.display = 'none';
        }
    });
}

async function fetchAutocompleteSuggestions(query, resultsContainer) {
    try {
        const loadingText = (typeof TRANSLATIONS !== 'undefined' && TRANSLATIONS.no_results) ? TRANSLATIONS.no_results : 'Searching...';
        resultsContainer.innerHTML = '<div class="autocomplete-loading">' + loadingText + '</div>';
        resultsContainer.classList.add('show');
        resultsContainer.style.display = 'block';
        
        // Find the language relative to the results container to handle multiple search bars
        const searchContainer = resultsContainer.closest('.search-bar-container');
        const lang = searchContainer?.querySelector('input[name="lang"]')?.value || 'ber';
        
        const response = await fetch(BASE_URL + '/api/autocomplete?q=' + encodeURIComponent(query) + '&lang=' + encodeURIComponent(lang));
        const data = await response.json();
        
        if (data.results && data.results.length > 0) {
            let html = '';
            data.results.forEach(word => {
                const displayText = word.translation_fr || word.word_tfng || word.word_lat;
                const safeWord = (word.word_lat || word.word_tfng || word.translation_fr).replace(/'/g, "\\'");
                
                html += '<div class="autocomplete-item" onmousedown="selectWord(\'' + safeWord + '\', ' + (word.id || 0) + ', ' + (word.count || 0) + ')">' +
                        '<div><span class="autocomplete-word">' + displayText + '</span></div>' +
                        '<span class="autocomplete-arrow">→</span>' +
                        '</div>';
            });
            resultsContainer.innerHTML = html;
        } else {
            const noResultsText = (typeof TRANSLATIONS !== 'undefined' && TRANSLATIONS.no_results) ? TRANSLATIONS.no_results : 'No results found';
            resultsContainer.innerHTML = '<div class="autocomplete-no-results">' + noResultsText + '</div>';
        }
    } catch (error) {
        console.error('Autocomplete error:', error);
        resultsContainer.style.display = 'none';
    }
}

// Helper to select a word (redirects to search results page or direct word page)
window.selectWord = function(query, id, count) {
    if (!query) return;
    
    let searchTerm = query;
    let targetId = id;
    let targetCount = count;

    // Handle legacy object passing if necessary
    if (typeof query === 'object') {
        const obj = query;
        searchTerm = obj.word_lat || obj.word_tfng || obj.translation_fr || '';
        targetId = obj.id;
        targetCount = obj.count || 1;
    }
    
    if (!searchTerm) return;

    if (targetCount === 1 && targetId) {
        // Unique word: Redirect directly to word page
        window.location.href = BASE_URL + '/word/' + encodeURIComponent(searchTerm).replace(/%20/g, '-') + '-' + targetId;
    } else {
        // Multiple entries or unknown: Redirect to general search results
        window.location.href = BASE_URL + '/search?q=' + encodeURIComponent(searchTerm);
    }
};

/* =========================================
   Recent Searches
   ========================================= */

function addToRecentSearches(wordId) {
    fetch(`${BASE_URL}/api/add-recent-search`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ word_id: wordId })
    })
    .then(response => response.json())
    .then(data => {
         if (data.success) {
             displayRecentSearches();
         }
    })
    .catch(error => console.error('Error adding recent search:', error));
}

function displayRecentSearches() {
    const recentWordsList = document.getElementById('recentWordsList');
    if (!recentWordsList) return;

    fetch(`${BASE_URL}/api/recent-searches`)
        .then(response => response.json())
        .then(response => {
            if (!response.data || response.data.length === 0) {
                recentWordsList.innerHTML = '<li style="padding:15px; background:white; border-radius:6px; color:#666; text-align:center;">Aucune recherche récente</li>';
                return;
            }

            recentWordsList.innerHTML = response.data.map(word => {
                const query = (word.word_lat || word.word_tfng || '').replace(/'/g, "\\'");
                return `
                <li class="recent-item" onmousedown='selectWord("${query}")' style="cursor:pointer">
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <span class="word-display" data-tfng="${word.word_tfng}" data-lat="${word.word_lat}">
                                ${word.word_tfng}
                            </span>
                            <div class="recent-translation">${word.translation_fr}</div>
                        </div>
                        <div class="recent-count">
                            ${word.search_count || 1} ${word.search_count > 1 ? 'recherches' : 'recherche'}
                        </div>
                    </div>
                </li>
            `;}).join('');
            
            // Re-apply script preference to new elements
            const currentScript = localStorage.getItem('preferred_script') || 'tfng';
            applyScriptToAll(currentScript);
        })
        .catch(console.error);
}

/* =========================================
   Social Media Auto-Save
   ========================================= */

function initSocialMediaAutoSave() {
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
}

async function saveSocialLink(input) {
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
}

/* =========================================
   Mobile Menu & UI Handlers
   ========================================= */

const mobile = document.querySelector('.mobile');
const menu = document.querySelector('.nav-menu');
const close_menu = document.querySelector('.menu_close');

if (mobile && menu && close_menu) {
    mobile.addEventListener('click', function(e) {
        e.stopPropagation(); 
        menu.classList.toggle('active');
        document.body.classList.toggle('no-scroll');
    });

    close_menu.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        menu.classList.remove('active');
        document.body.classList.remove('no-scroll');
    });
}

// Smooth Scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
});

/* =========================================
   Search Language Dropdown
   ========================================= */

function initSearchLanguageDropdown() {
    const langDropdowns = document.querySelectorAll('.lang-dropdown');
    
    langDropdowns.forEach(dropdown => {
        const langBtn = dropdown.querySelector('.lang-btn');
        const langMenu = dropdown.querySelector('.lang-menu');
        const langItems = dropdown.querySelectorAll('.lang-item');
        // Find the text span - structurally it's button > span > span
        const langBtnText = langBtn ? (langBtn.querySelector('span > span') || langBtn.querySelector('span')) : null; 

        if (langBtn && langMenu) {
            // Toggle menu
            langBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                // Close all other open dropdowns first
                document.querySelectorAll('.lang-menu').forEach(menu => {
                    if (menu !== langMenu) menu.style.display = 'none';
                });
                
                const isVisible = langMenu.style.display !== 'none';
                langMenu.style.display = isVisible ? 'none' : 'block';
            });

            // Select language
            langItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.stopPropagation(); // Prevent bubbling
                    const value = this.dataset.value;
                    const text = this.querySelector('span').textContent;

                    // Update button text
                    if (langBtnText) {
                        langBtnText.textContent = text;
                    }

                    // Update active state in THIS menu
                    langItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    // Update hidden input
                    const hiddenInput = dropdown.querySelector('input[name="lang"]');
                    if (hiddenInput) {
                        hiddenInput.value = value;
                    }

                    // Save preference in cookie for 30 days
                    const d = new Date();
                    d.setTime(d.getTime() + (30 * 24 * 60 * 60 * 1000));
                    document.cookie = "search_lang=" + value + ";expires=" + d.toUTCString() + ";path=/";

                    // Close menu
                    langMenu.style.display = 'none';
                    
                    // Trigger a new search if input is not empty
                    const searchContainer = dropdown.closest('.search-bar-container');
                    const searchInput = searchContainer?.querySelector('.search-bar');
                    const autocompleteResults = searchContainer?.querySelector('.suggestions');
                    if (searchInput && searchInput.value.length >= 2 && autocompleteResults) {
                        fetchAutocompleteSuggestions(searchInput.value, autocompleteResults);
                    }
                });
            });
        }
    });

    // Global click listener to close all dropdowns is handled by the general click listener at top
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.lang-dropdown')) {
            document.querySelectorAll('.lang-menu').forEach(menu => {
                menu.style.display = 'none';
            });
        }
    });
}

/* =========================================
   Share & Copy Functionality (Consolidated)
   ========================================= */

function copyToClipboard(text, successMsg = "Copié !") {
    if (!text) return;
    
    const showSuccess = () => showToast(successMsg);

    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(showSuccess).catch(err => {
            console.error('Clipboard API failed', err);
            fallbackCopyToClipboard(text, showSuccess);
        });
    } else {
        fallbackCopyToClipboard(text, showSuccess);
    }
}

function fallbackCopyToClipboard(text, callback) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.left = "-9999px";
    textArea.style.top = "0";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        if (callback) callback();
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
    }
    document.body.removeChild(textArea);
}

function shareContent(title, text, url) {
    const shareData = {
        title: title || 'Amawal',
        text: text,
        url: url || window.location.href
    };

    if (navigator.share) {
        navigator.share(shareData).catch(err => {
            console.log('Share canceled or failed', err);
            showShareModal(text, shareData.url);
        });
    } else {
        showShareModal(text, shareData.url);
    }
}

// Proverb Specific Wrappers (Legacy support + Cleaner impl)
window.shareProverb = function(button) {
    const container = button ? button.closest('.proverb-card, .proverb-section, .proverb-page-container') : document.querySelector('.proverb-card');
    if (!container) return;

    const tfng = container.querySelector('.proverb-text, .proverb-display')?.innerText || "";
    const lat = container.querySelector('.proverb-subtext, .proverb-explanation, [data-lat]')?.getAttribute('data-lat') || "";
    const fr = container.querySelector('.proverb-translation, .translation, p.quote-text + .translation')?.innerText || "";
    
    const text = `${tfng}\n${lat}\n${fr}`;
    
    shareContent('Amawal Proverb', text, window.location.href);
};

window.copyProverb = function(button) {
    const container = button ? button.closest('.proverb-card, .daily-proverb-container, .proverb-page-container') : document.querySelector('.proverb-card');
    if (!container) return;

    const tfng = container.querySelector('.proverb-text, .proverb-display')?.innerText || "";
    const lat = container.querySelector('.proverb-subtext, .proverb-explanation, [data-lat]')?.getAttribute('data-lat') || "";
    const fr = container.querySelector('.proverb-translation, .translation')?.innerText || "";
    
    const content = `${tfng}\n${lat}\n${fr}\n\nVia Amawal - ${window.location.href}`;
    
    copyToClipboard(content, 'Proverbe copié !');
};

// Word Specific Wrappers
window.copyWord = function(entryId) {
    const entry = document.getElementById('entry-' + entryId);
    if (!entry) return;
    
    const tfng = entry.querySelector('.word-title')?.textContent.trim();
    const lat = entry.querySelector('.phonetic')?.textContent.trim();
    const translation = entry.querySelector('.definition-text')?.textContent.trim();
    
    const text = (tfng ? tfng + " " : "") + 
                 (lat ? "(" + lat + ") " : "") + 
                 "\n" + (translation || "");
                 
    copyToClipboard(text, "Mot copié !");
};

window.shareWord = function(entryId, title) {
    const entry = document.getElementById('entry-' + entryId);
    if (!entry) return;
    
    const tfng = entry.querySelector('.word-title')?.textContent.trim();
    const permalink = entry.querySelector('.permalink-badge')?.href;
    
    shareContent(
        'Amawal - ' + (title || tfng),
        tfng,
        permalink || window.location.href
    );
};

// UI Components for Share/Toast

function showToast(message) {
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
}

function showShareModal(text, url) {
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
    
    // Update links dynamically
    const xBtn = modal.querySelector('.twitter');
    if (xBtn) xBtn.href = `https://x.com/intent/tweet?text=${encodeURIComponent(text)}&url=${encodeURIComponent(url)}`;
    modal.querySelector('.facebook').href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}&quote=${encodeURIComponent(text)}`;
    modal.querySelector('.whatsapp').href = `https://wa.me/?text=${encodeURIComponent(text + ' ' + url)}`;
    
    const copyBtn = modal.querySelector('.copy-link');
    copyBtn.onclick = () => {
        copyToClipboard(url, 'Lien copié !');
    };
    
    modal.classList.add('active');
}
