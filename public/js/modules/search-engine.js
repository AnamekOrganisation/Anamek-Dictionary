/**
 * Search Engine Module
 * Handles search execution, autocomplete suggestions, and recent searches.
 */

window.performSearch = function() {
    const searchInput = document.getElementById('search-input');
    const searchLang = document.getElementById('search-lang');
    if (searchInput && searchInput.value.trim()) {
        const lang = searchLang ? searchLang.value : 'ber';
        window.location.href = BASE_URL + '/search?q=' + encodeURIComponent(searchInput.value) + '&lang=' + encodeURIComponent(lang);
    }
};

let autocompleteTimeout;

window.initSearchAutocomplete = function() {
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
};

window.fetchAutocompleteSuggestions = async function(query, resultsContainer) {
    try {
        const loadingText = (typeof TRANSLATIONS !== 'undefined' && TRANSLATIONS.no_results) ? TRANSLATIONS.no_results : 'Searching...';
        resultsContainer.innerHTML = '<div class="autocomplete-loading">' + loadingText + '</div>';
        resultsContainer.classList.add('show');
        resultsContainer.style.display = 'block';
        
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
};

window.selectWord = function(query, id, count) {
    if (!query) return;
    
    let searchTerm = query;
    let targetId = id;
    let targetCount = count;

    if (typeof query === 'object') {
        const obj = query;
        searchTerm = obj.word_lat || obj.word_tfng || obj.translation_fr || '';
        targetId = obj.id;
        targetCount = obj.count || 1;
    }
    
    if (!searchTerm) return;

    if (targetCount === 1 && targetId) {
        window.location.href = BASE_URL + '/word/' + encodeURIComponent(searchTerm).replace(/%20/g, '-') + '-' + targetId;
    } else {
        window.location.href = BASE_URL + '/search?q=' + encodeURIComponent(searchTerm);
    }
};

window.addToRecentSearches = function(wordId) {
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
};

window.displayRecentSearches = function() {
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
            
            const currentScript = localStorage.getItem('preferred_script') || 'tfng';
            if (window.applyScriptToAll) applyScriptToAll(currentScript);
        })
        .catch(console.error);
};

window.initSearchLanguageDropdown = function() {
    const langDropdowns = document.querySelectorAll('.lang-dropdown');
    
    langDropdowns.forEach(dropdown => {
        const langBtn = dropdown.querySelector('.lang-btn');
        const langMenu = dropdown.querySelector('.lang-menu');
        const langItems = dropdown.querySelectorAll('.lang-item');
        const langBtnText = langBtn ? (langBtn.querySelector('span > span') || langBtn.querySelector('span')) : null; 

        if (langBtn && langMenu) {
            langBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                document.querySelectorAll('.lang-menu').forEach(menu => {
                    if (menu !== langMenu) menu.style.display = 'none';
                });
                
                const isVisible = langMenu.style.display !== 'none';
                langMenu.style.display = isVisible ? 'none' : 'block';
            });

            langItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const value = this.dataset.value;
                    
                    if (langBtnText) {
                        const selectedSpan = this.querySelector('span');
                        langBtnText.innerHTML = selectedSpan.innerHTML;
                        
                        if (selectedSpan.classList.contains('word-display')) {
                            langBtnText.classList.add('word-display');
                            langBtnText.dataset.tfng = selectedSpan.dataset.tfng;
                            langBtnText.dataset.lat = selectedSpan.dataset.lat;
                        } else {
                            langBtnText.classList.remove('word-display');
                            delete langBtnText.dataset.tfng;
                            delete langBtnText.dataset.lat;
                        }
                        
                        const currentPrefScript = localStorage.getItem('preferred_script') || 'tfng';
                        if (langBtnText.classList.contains('word-display')) {
                             langBtnText.textContent = currentPrefScript === 'tfng' ? langBtnText.dataset.tfng : langBtnText.dataset.lat;
                        }
                    }

                    langItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');

                    const hiddenInput = dropdown.querySelector('input[name="lang"]');
                    if (hiddenInput) {
                        hiddenInput.value = value;
                    }

                    const d = new Date();
                    d.setTime(d.getTime() + (30 * 24 * 60 * 60 * 1000));
                    document.cookie = "search_lang=" + value + ";expires=" + d.toUTCString() + ";path=/";

                    langMenu.style.display = 'none';
                    
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

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.lang-dropdown')) {
            document.querySelectorAll('.lang-menu').forEach(menu => {
                menu.style.display = 'none';
            });
        }
    });
};
