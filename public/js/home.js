// Amawal Home Page JavaScript
console.log('Home.js loaded successfully');

function getScriptToggleHTML(toggleClass = '', tfngLabel = 'ⵜⵉⴼⵉⵏⴰⵖ', latLabel = 'Latin') {
    return `
        <div class="script-toggle ${toggleClass}" style="margin-bottom:15px;">
            <button onclick="updateScriptDisplay('tfng')" class="script-btn tfng-btn">${tfngLabel}</button>
            <button onclick="updateScriptDisplay('lat')" class="script-btn lat-btn">${latLabel}</button>
        </div>
    `;
}

// Search redirections and autocomplete are now handled globally in main.js.
// We only keep homepage-specific logic here (like displaying recent searches).

// Language dropdown logic (if specific to homepage headers not covered by main.js, keep it, otherwise remove?)
// main.js handles .lang-dropdown universally. home.js seems to handle #langBtn specifically. 
// If #langBtn is unique to homepage, keep it. 
const langBtn = document.getElementById('langBtn');
const langBtnText = document.getElementById('langBtnText');
const langMenu = document.getElementById('langMenu');
let currentLang = 'ber';

if (langBtn) {
    langBtn.addEventListener('click', function (e) {
        langMenu.style.display = langMenu.style.display === 'block' ? 'none' : 'block';
        e.stopPropagation();
    });
}

if (langMenu) {
    langMenu.querySelectorAll('.lang-item').forEach(item => {
        item.addEventListener('click', function () {
            currentLang = this.getAttribute('data-value');
            langBtnText.textContent = this.textContent;
            langMenu.style.display = 'none';
            // Search input clearing/fetching is handled by user interaction now
        });
    });
}

document.addEventListener('click', function () {
    if (langMenu) langMenu.style.display = 'none';
});

// Update suggestions when language or search type changes - Removed as main.js handles its own state
// document.querySelectorAll('input[name="lang"],input[name="stype"]'... removed

// Call these initially
console.log('Initializing daily content...');
// displayRecentSearches() is now handled by main.js
console.log('Daily content initialized');


function updateProverbScript(script) {
    // Get all elements with proverb-display and proverb-explanation class
    const proverbDisplays = document.querySelectorAll('.proverb-display');
    const proverbExplanations = document.querySelectorAll('.proverb-explanation');

    proverbDisplays.forEach(el => {
        const tfng = el.dataset.tfng;
        const lat = el.dataset.lat;
        if (tfng && lat) {
            el.textContent = script === 'tfng' ? tfng : lat;
            el.style.fontFamily = script === 'tfng'
                ? "'Noto Sans Tifinagh', sans-serif"
                : "'Arial', sans-serif";
            // el.style.direction = script === 'tfng' ? 'rtl' : 'ltr';
        }
    });

    proverbExplanations.forEach(el => {
        const tfng = el.dataset.tfng;
        const lat = el.dataset.lat;
        if (tfng && lat) {
            el.textContent = script === 'tfng' ? tfng : lat;
            el.style.fontFamily = script === 'tfng'
                ? "'Noto Sans Tifinagh', sans-serif"
                : "'Arial', sans-serif";
            // el.style.direction = script === 'tfng' ? 'rtl' : 'ltr';
        }
    });

    // Update button states
    const tfngBtn = document.querySelector('.proverb-tfng-btn');
    const latBtn = document.querySelector('.proverb-lat-btn');
    if (tfngBtn && latBtn) {
        tfngBtn.classList.toggle('active', script === 'tfng');
        latBtn.classList.toggle('active', script === 'lat');
    }

    // Store preference
    localStorage.setItem('preferred_proverb_script', script);
}

function speakWord(text) {
    const utter = new SpeechSynthesisUtterance(text);
    const voices = window.speechSynthesis.getVoices();
    let amaVoice = voices.find(v =>
        v.lang && (v.lang.toLowerCase().includes('ber') ||
            v.lang.toLowerCase().includes('tzm') ||
            v.lang.toLowerCase().includes('zgh'))
    );
    if (amaVoice) {
        utter.voice = amaVoice;
    }
    utter.lang = amaVoice ? amaVoice.lang : 'ber';
    window.speechSynthesis.speak(utter);
}

// Recent searches management
const MAX_RECENT_SEARCHES = 5;

// Recent search functions removed as they are now handled by main.js
// Only scripts for toggling display modes or specific homepage widgets remain here.

function updateProverbScript(script) {
    // ... (logic for proverbs)
    // Same for logic above.
    
    // Actually, updateScriptDisplay was also duplicated? 
    // main.js has applyScriptToAll? 
    // Let's keep updateProverbScript if main.js doesn't include it.
}

function speakWord(text) {
    // ...
}

// Ensure recent searches are displayed IF main.js hasn't already done it (though main.js has DOMContentLoaded listener).
// We can remove the initialization call here too since main.js handles it.
// console.log('Initializing daily content...');
// displayRecentSearches(); <- Removed








