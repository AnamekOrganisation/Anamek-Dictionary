/**
 * Actions Module
 * Handles sharing and copying functionality for words and proverbs.
 */

window.copyToClipboard = function(text, successMsg = "Copié !") {
    if (!text) return;
    
    const showSuccess = () => {
        if (window.showToast) window.showToast(successMsg);
    };

    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(showSuccess).catch(err => {
            console.error('Clipboard API failed', err);
            fallbackCopyToClipboard(text, showSuccess);
        });
    } else {
        fallbackCopyToClipboard(text, showSuccess);
    }
};

window.fallbackCopyToClipboard = function(text, callback) {
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
};

window.shareContent = function(title, text, url) {
    const shareData = {
        title: title || 'Amawal',
        text: text,
        url: url || window.location.href
    };

    if (navigator.share) {
        navigator.share(shareData).catch(err => {
            console.log('Share canceled or failed', err);
            if (window.showShareModal) window.showShareModal(text, shareData.url);
        });
    } else {
        if (window.showShareModal) window.showShareModal(text, shareData.url);
    }
};

window.shareProverb = function(button) {
    const container = button ? button.closest('.proverb-card, .proverb-section, .proverb-page-container') : document.querySelector('.proverb-card');
    if (!container) return;

    const tfng = container.querySelector('.proverb-text, .proverb-display')?.innerText || "";
    const lat = container.querySelector('.proverb-subtext, .proverb-explanation, [data-lat]')?.getAttribute('data-lat') || "";
    const fr = container.querySelector('.proverb-translation, .translation, p.quote-text + .translation')?.innerText || "";
    
    const text = `${tfng}\n${lat}\n${fr}`;
    const url = (typeof ABSOLUTE_URL !== 'undefined') ? ABSOLUTE_URL + '/proverbs' : window.location.href;
    
    shareContent('Amawal Proverb', text, url);
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
        (typeof ABSOLUTE_URL !== 'undefined' && tfng) ? ABSOLUTE_URL + '/word/' + encodeURIComponent(tfng) : (permalink || window.location.href)
    );
};
