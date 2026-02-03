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
