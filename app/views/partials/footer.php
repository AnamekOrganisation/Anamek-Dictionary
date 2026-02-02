<?php
function icon_svg($icon){
    $icons = [
        'facebook' => '<svg width="20" height="20" viewBox="0 0 512 512"><path d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z"></path></svg>',
        'twitter' => '<svg width="20" height="20" viewBox="0 0 20 20"><path d="m15.08,2.1h2.68l-5.89,6.71,6.88,9.1h-5.4l-4.23-5.53-4.84,5.53H1.59l6.24-7.18L1.24,2.1h5.54l3.82,5.05,4.48-5.05Zm-.94,14.23h1.48L6,3.61h-1.6l9.73,12.71h0Z"></path></svg>',
        'instagram' => '<svg width="20" height="20" viewBox="0 0 20 20"><path d="M13.55,1H6.46C3.45,1,1,3.44,1,6.44v7.12c0,3,2.45,5.44,5.46,5.44h7.08c3.02,0,5.46-2.44,5.46-5.44V6.44 C19.01,3.44,16.56,1,13.55,1z M17.5,14c0,1.93-1.57,3.5-3.5,3.5H6c-1.93,0-3.5-1.57-3.5-3.5V6c0-1.93,1.57-3.5,3.5-3.5h8 c1.93,0,3.5,1.57,3.5,3.5V14z"></path><circle cx="14.87" cy="5.26" r="1.09"></circle><path d="M10.03,5.45c-2.55,0-4.63,2.06-4.63,4.6c0,2.55,2.07,4.61,4.63,4.61c2.56,0,4.63-2.061,4.63-4.61 C14.65,7.51,12.58,5.45,10.03,5.45L10.03,5.45L10.03,5.45z M10.08,13c-1.66,0-3-1.34-3-2.99c0-1.65,1.34-2.99,3-2.99s3,1.34,3,2.99 C13.08,11.66,11.74,13,10.08,13L10.08,13L10.08,13z"></path></svg>',
        'youtube' => '<svg width="20" height="20" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>',
        'tiktok' => '<svg width="20" height="20" viewBox="0 0 24 24"><path d="M12.53.02C13.84 0 15.14.01 16.44 0c.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.03 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-3.3 2.97-5.96 6.27-6.08.88-.05 1.78.16 2.59.52V9.45c-1.32-.46-2.78-.57-4.17-.3-2.42.47-4.3 2.51-4.56 4.93-.06.41-.07.82-.07 1.24.06 1.58.74 3.1 1.92 4.16 1.12.98 2.62 1.48 4.11 1.3 1.1-.09 2.17-.59 2.93-1.39.81-.85 1.27-1.97 1.28-3.13-.01-4.43-.02-8.86-.01-13.29z"/></svg>',
    ];
    return $icons[$icon] ?? '';
}

// Fetch social links from database
try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    $socialLinks = $pdo->query("SELECT platform, url FROM social_links")->fetchAll(PDO::FETCH_KEY_PAIR);
} catch (Exception $e) {
    $socialLinks = [];
}
?>


  <!-- FOOTER -->
<?php 
if (isset($is_404) && $is_404) {
    return;
}
?>
<footer class="footer">
        <div class="footer-container">
            <div class="footer-top">
            <div class="footer-logo"><img src="<?= BASE_URL ?>/public/img/logo.webp" alt=""></div>
             <div class="social-icons">
                    <?php 
                    $platforms = ['facebook', 'twitter', 'instagram', 'tiktok', 'youtube'];
                    foreach ($platforms as $platform): 
                        if (!empty($socialLinks[$platform])): ?>
                            <a href="<?= htmlspecialchars($socialLinks[$platform]) ?>" target="_blank" class="social-icon">
                                <?= icon_svg($platform) ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="footer-bottom">
            <nav class="footer-nav">
                <a href="<?= BASE_URL ?>/about" class="footer-link"><?= __('About') ?></a>
                <a href="<?= BASE_URL ?>/privacy" class="footer-link"><?= __('Privacy Policy') ?></a>
                <a href="<?= BASE_URL ?>/terms" class="footer-link"><?= __('Terms of Use') ?></a>
                <a href="<?= BASE_URL ?>/contact" class="footer-link"><?= __('Contact') ?></a>
                <a href="<?= BASE_URL ?>/cookies" class="footer-link"><?= __('Cookies') ?></a>
            </nav>
            </div>
            <p class="footer-copyright">© <?php echo date('Y'); ?> anamek - Dictionnaire Amazigh. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- Global Floating Script Toggle -->
    <div class="floating-script-toggle" id="floatingScriptToggle">
        <button class="script-toggle-btn active" data-script="tfng" title="Tifinagh">
            <span class="script-icon">ⵜ</span>
            <span class="script-label">ⵜⵉⴼⵉⵏⴰⵖ</span>
        </button>
        <button class="script-toggle-btn" data-script="lat" title="Latin">
            <span class="script-icon">A</span>
            <span class="script-label">Latin</span>
        </button>
    </div>

    
    <script src="<?= BASE_URL ?>/public/js/main.js"></script>
</body>
</html>
