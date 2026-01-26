<?php
/**
 * View Partial: Google Adsense
 * Renders an ad unit based on settings.
 * 
 * @param string $slot The ad slot ID to use.
 */
$settingModel = new \App\Models\Setting($this->pdo);
$adsEnabled = $settingModel->get('google_ads_enabled', '0');
$clientId = $settingModel->get('google_ads_client_id', '');

if ($adsEnabled !== '1' || empty($clientId) || empty($slot)) {
    return;
}
?>

<!-- Google Adsense -->
<div class="ad-container" style="display: flex; justify-content: center; margin: 2rem 0; overflow: hidden; max-width: 100%;">
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?= htmlspecialchars($clientId) ?>" crossorigin="anonymous"></script>
    <ins class="adsbygoogle"
         style="display:block"
         data-ad-client="<?= htmlspecialchars($clientId) ?>"
         data-ad-slot="<?= htmlspecialchars($slot) ?>"
         data-ad-format="auto"
         data-full-width-responsive="true"></ins>
    <script>
         (adsbygoogle = window.adsbygoogle || []).push({});
    </script>
</div>
