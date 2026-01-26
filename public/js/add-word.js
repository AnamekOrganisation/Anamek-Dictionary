document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.add-word-form');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate Tifinagh input
        const tifInput = form.querySelector('[name="word_ber_tif"]');
        if (!isTifinagh(tifInput.value)) {
            alert('Please enter valid Tifinagh characters');
            return;
        }
        
        // Validate Latin input
        const latInput = form.querySelector('[name="word_ber_lat"]');
        if (!isLatinAmazigh(latInput.value)) {
            alert('Please enter valid Latin Amazigh characters');
            return;
        }
        
        // If validation passes, submit the form
        form.submit();
    });
    
    function isTifinagh(text) {
        // Tifinagh Unicode range: 2D30-2D7F
        return /^[\u2D30-\u2D7F\s]+$/.test(text);
    }
    
    function isLatinAmazigh(text) {
        // Latin Amazigh characters
        return /^[a-zA-ZḌḍḤḥṚṛṢṣṬṭƐƔḒḓẒẓčğǧȷṯṱẓṣḍḥṛṭ\s]+$/.test(text);
    }
});