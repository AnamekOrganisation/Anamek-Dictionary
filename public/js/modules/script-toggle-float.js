/**
 * Script Toggle Float Button
 * Allows users to switch between Tifinagh and Latin scripts
 */
document.addEventListener('DOMContentLoaded', function() {
    const scriptToggle = document.createElement('div');
    scriptToggle.className = 'script-toggle-float';
    scriptToggle.innerHTML = `
        <span class="tifinagh" style="color: var(--v-primary); font-weight: 700;">ⴰ</span>
        <span id="currentScript">Latin</span>
    `;
    
    // Only show on word pages
    if (window.location.pathname.includes('/word/')) {
        document.body.appendChild(scriptToggle);
        
        scriptToggle.addEventListener('click', function() {
            const currentScript = document.getElementById('currentScript');
            if (currentScript.textContent === 'Latin') {
                currentScript.textContent = 'Tifinagh';
                // Logic to toggle script display
                document.querySelectorAll('.tifinagh').forEach(el => el.style.display = 'inline');
                document.querySelectorAll('.latin-only').forEach(el => el.style.display = 'none');
            } else {
                currentScript.textContent = 'Latin';
                document.querySelectorAll('.tifinagh').forEach(el => el.style.display = 'inline');
                document.querySelectorAll('.latin-only').forEach(el => el.style.display = 'inline');
            }
        });
    }
});
