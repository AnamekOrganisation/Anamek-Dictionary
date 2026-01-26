document.addEventListener('DOMContentLoaded', function() {
    const socialForm = document.querySelector('.social-links-form');
    const socialInputs = document.querySelectorAll('.social-input');
    const saveButton = document.querySelector('.save-button');
    let saveTimeout;

    // Auto-save functionality
    socialInputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(saveTimeout);
            const platform = this.name.match(/\[(.*?)\]/)[1];
            const url = this.value;

            // Wait 500ms after typing stops before saving
            saveTimeout = setTimeout(() => {
                saveSocialLink(platform, url, this);
            }, 500);
        });
    });

    // Handle form submission
    socialForm.addEventListener('submit', function(e) {
        e.preventDefault();
        saveAllLinks();
    });

    function saveSocialLink(platform, url, input) {
        const formData = new FormData();
        formData.append('ajax', '1');
        formData.append('platform', platform);
        formData.append('url', url);

        // Show saving indicator
        input.classList.add('saving');

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            input.classList.remove('saving');
            if (data.success) {
                showFeedback(input, true);
            } else {
                showFeedback(input, false, data.message);
            }
        })
        .catch(error => {
            input.classList.remove('saving');
            showFeedback(input, false, 'Erreur de sauvegarde');
        });
    }

    function saveAllLinks() {
        const promises = Array.from(socialInputs).map(input => {
            const platform = input.name.match(/\[(.*?)\]/)[1];
            return saveSocialLink(platform, input.value, input);
        });

        Promise.all(promises)
            .then(() => {
                saveButton.classList.add('success');
                setTimeout(() => saveButton.classList.remove('success'), 2000);
            })
            .catch(() => {
                saveButton.classList.add('error');
                setTimeout(() => saveButton.classList.remove('error'), 2000);
            });
    }

    function showFeedback(input, isSuccess, message = '') {
        input.classList.add(isSuccess ? 'success' : 'error');
        if (message) {
            input.setCustomValidity(message);
            input.reportValidity();
        }
        setTimeout(() => {
            input.classList.remove('success', 'error');
            input.setCustomValidity('');
        }, 2000);
    }
});