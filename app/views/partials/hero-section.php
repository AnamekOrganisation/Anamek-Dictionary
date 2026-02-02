<?php
// Page Context Detection
$currentUri = $_SERVER['REQUEST_URI'];
$isProverbs = strpos($currentUri, '/proverbs') !== false || strpos($currentUri, '/proverb/') !== false;
$is404 = isset($page_title) && $page_title === '404 Not Found'; 
if (isset($is_404) && $is_404) {
    return; 
}

// Info pages detection
$isInfoPage = strpos($currentUri, '/about') !== false || strpos($currentUri, '/contact') !== false || strpos($currentUri, '/privacy') !== false;

$commonData = DictionaryController::getSharedData();
$totalWords = $commonData['wordCount'];
$totalProverbs = $commonData['proverbCount'];

$headerTitle = $isProverbs 
    ? __('Proverbs Dictionary') . ' <span id="proverb-count">' . number_format($totalProverbs) . '</span>' 
    : __('Le dictionnaire contient actuellement') . ' <span id="word-count">' . number_format($totalWords) . '</span> ' . __('mots') . ' ' . __('et') . ' <span id="proverb-count">' . number_format($totalProverbs) . '</span> ' . __('proverbes.');

// Search Placeholder
$searchPlaceholder = $isProverbs ? __('Search a proverb...') : __('Search a word or phrase');
?>
    <div class="hero-search" <?php if ($isInfoPage) echo 'style="display:none;"'; ?>>
        <div class="container">
            <?php if ($isProverbs): ?>
                <!-- Proverbs Specific Header -->
                <div class="text-center mb-6">
                    <h1 class="display-5 fw-bold mb-2" style="font-size: 2.5rem; color: #fff;">
                        <?= __('Amazigh Proverbs') ?>
                    </h1>
                    <p class="lead" style="color: rgba(255,255,255,0.8); font-size: 1.1rem;"><?= __('Discover ancestral wisdom through our proverbs.') ?></p>
                </div>

                <!-- Proverbs Search Bar -->
                <div class="row justify-content-center mb-5" style="margin-bottom: 0 !important;">
                    <div class="col-md-8 col-lg-8">
                        <form action="<?= BASE_URL ?>/proverbs" method="GET" class="search-form" style="width: 100%; display: flex; justify-content: center;">
                            <div class="search-bar-container search-box">
                                <input type="text" 
                                       name="q" 
                                       class="search-bar" 
                                       placeholder="<?= __('Search a proverb...') ?>" 
                                       value="<?= e($_GET['q'] ?? '') ?>"
                                       autocomplete="off"
                                       style="padding-left: 20px;">
                                
                                <?php if (!empty($_GET['q'])): ?>
                                    <a href="<?= BASE_URL ?>/proverbs" class="btn text-muted p-2" style="display: flex; align-items: center; color: #666; text-decoration: none;">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <button class="search-btn" type="submit">
                                    <svg aria-hidden="true" fill="none" height="19" version="1.1" viewBox="0 0 19 19" width="19" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M2.04004 8.79391C2.04004 5.18401 5.02763 2.23297 8.74367 2.23297C12.4597 2.23297 15.4473 5.18401 15.4473 8.79391C15.4473 12.4038 12.4597 15.3549 8.74367 15.3549C5.02763 15.3549 2.04004 12.4038 2.04004 8.79391ZM8.74367 0.732971C4.22666 0.732971 0.540039 4.32838 0.540039 8.79391C0.540039 13.2595 4.22666 16.8549 8.74367 16.8549C10.4144 16.8549 11.9716 16.363 13.2706 15.5171C13.6981 15.2387 14.2697 15.2585 14.6339 15.6158L17.4752 18.4027C17.7668 18.6887 18.2338 18.6887 18.5254 18.4027V18.4027C18.8251 18.1087 18.8251 17.626 18.5254 17.332L15.725 14.5853C15.3514 14.2188 15.3296 13.6296 15.6192 13.1936C16.4587 11.9301 16.9473 10.4197 16.9473 8.79391C16.9473 4.32838 13.2607 0.732971 8.74367 0.732971Z" fill="currentColor"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                <!-- Default Dictionary Header -->
                <?php 
                $savedLang = $_COOKIE['search_lang'] ?? 'ber';
                $isFr = $savedLang === 'fr';
                $langLabel = $isFr ? 'Français' : 'ⵜⴰⵎⴰⵣⵉⵖⵜ';
                ?>
                <h6><?= $headerTitle ?></h6>
                <form id="searchForm" style="width: 100%; display: flex; justify-content: center;">
                    <div class="search-bar-container search-box">
                        <div class="lang-dropdown">
                            <input type="hidden" name="lang" id="search-lang" value="<?= htmlspecialchars($savedLang) ?>">
                            <button type="button" class="lang-btn">
                                <span><span class="lang-text"><?= $langLabel ?></span></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" style="margin-left:6px;" viewBox="0 0 24 24">
                                    <polygon points="12 17.414 3.293 8.707 4.707 7.293 12 14.586 19.293 7.293 20.707 8.707 12 17.414"></polygon>
                                </svg>
                            </button>
                            <div class="lang-menu" style="display:none;">
                                <div class="lang-item <?= !$isFr ? 'active' : '' ?>" data-value="ber">
                                    <span>ⵜⴰⵎⴰⵣⵉⵖⵜ</span>
                                </div>
                                <div class="lang-item <?= $isFr ? 'active' : '' ?>" data-value="fr">
                                    <span>Français</span>
                                </div>
                            </div>
                        </div>
                        <input type="text" id="search-input" class="search-bar" placeholder="<?= $searchPlaceholder ?>" aria-label="Search" autocomplete="off">
                        <button type="submit" class="search-btn" id="searchBtn">
                            <svg aria-hidden="true" fill="none" height="19" version="1.1" viewBox="0 0 19 19" width="19" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.04004 8.79391C2.04004 5.18401 5.02763 2.23297 8.74367 2.23297C12.4597 2.23297 15.4473 5.18401 15.4473 8.79391C15.4473 12.4038 12.4597 15.3549 8.74367 15.3549C5.02763 15.3549 2.04004 12.4038 2.04004 8.79391ZM8.74367 0.732971C4.22666 0.732971 0.540039 4.32838 0.540039 8.79391C0.540039 13.2595 4.22666 16.8549 8.74367 16.8549C10.4144 16.8549 11.9716 16.363 13.2706 15.5171C13.6981 15.2387 14.2697 15.2585 14.6339 15.6158L17.4752 18.4027C17.7668 18.6887 18.2338 18.6887 18.5254 18.4027V18.4027C18.8251 18.1087 18.8251 17.626 18.5254 17.332L15.725 14.5853C15.3514 14.2188 15.3296 13.6296 15.6192 13.1936C16.4587 11.9301 16.9473 10.4197 16.9473 8.79391C16.9473 4.32838 13.2607 0.732971 8.74367 0.732971Z" fill="currentColor"></path>
                            </svg>
                        </button>
                        <div id="autocomplete-results" class="autocomplete-results suggestions" style="display:none;"></div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
