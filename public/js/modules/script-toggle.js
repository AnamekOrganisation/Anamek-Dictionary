/**
 * Script Toggling Module (Tifinagh/Latin)
 * Handles global script preferences and UI updates.
 */

window.initGlobalScriptToggle = function() {
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
};

window.setGlobalScript = function(script) {
    localStorage.setItem('preferred_script', script);
    applyScriptToAll(script);
    updateToggleButtons(script);
};

window.applyScriptToAll = function(script) {
    // Update all elements with data-tfng and data-lat attributes
    document.querySelectorAll('[data-tfng][data-lat]').forEach(element => {
        // Safety Check
        if (element.children.length === 0 || element.classList.contains('safe-text-replace') || element.classList.contains('word-display')) {
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
};

window.updateToggleButtons = function(script) {
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
};

window.toggleScriptInSearch = function() {
    const currentScript = localStorage.getItem('preferred_script') || 'tfng';
    const newScript = currentScript === 'tfng' ? 'lat' : 'tfng';
    setGlobalScript(newScript);
};
