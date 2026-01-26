    <div class="serch-header">
        <div class="search-section">
            <h1 class="search-title"><?php 
            $lang = getLanguage();
            echo "<!-- Language: $lang -->";
            
            // Generate sentence based on language
            switch ($lang) {
                case 'zgh_Latn':
                    $sentence = "amawal ad ismun {$wordCount} n tguriwin " .
                                "d {$proverbCount}" . ($proverbCount == 1 ? " inzi" : " inzitn") . ".";
                    break;
                case 'ber_MA':
                    $sentence = "ⴰⵎⴰⵡⴰⵍ ⴰⴷ ⵉⵙⵎⵓⵏ {$wordCount} ⵏ ⵜⴳⵓⵔⵉⵡⵉⵏ " .
                                "ⴷ {$proverbCount}" . ($proverbCount == 1 ? " ⵉⵏⵣⵉ" : " ⵉⵏⵣⵉⵜⵏ") . ".";
                    break;
                default: // English/French fallback
                    $sentence = "Le dictionnaire contient actuellement {$wordCount} mot" . ($wordCount != 1 ? "s" : "") .
                                " et {$proverbCount} proverbe" . ($proverbCount != 1 ? "s" : "") . ".";
            }
            ?>
                <h1 class="search-title"><?php echo htmlspecialchars($sentence); ?></h1>
                <script>
                    const BASE_URL = "<?= BASE_URL ?>";
                    const STR_NOT_FOUND = "<?= __('Word not found') ?>";
                </script>
            </h1>

            <?php if (isset($_GET['notfound'])): ?>
            <div class="alert alert-warning"
                style="max-width: 800px; margin: 10px auto; padding: 15px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px; color: #856404; text-align: center;">
                <i class="fas fa-exclamation-triangle"></i> <?= __('Word not found') ?>
            </div>
            <?php endif; ?>

            <form id="searchForm" style="width: 100%; display: flex; justify-content: center;">
                <div class="search-bar-container"
                    style="position:relative; justify-content:center; width: 100%; max-width: 800px;">
                    <div class="lang-dropdown">
                        <button type="button" class="lang-btn">
                            <span><span><?php echo isMobileDevice() ? "ⵜⵎⵣ" : "ⵜⴰⵎⴰⵣⵉⵖⵜ"; ?></span>
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                style="margin-left:6px;" viewBox="0 0 24 24">
                                <polygon
                                    points="12 17.414 3.293 8.707 4.707 7.293 12 14.586 19.293 7.293 20.707 8.707 12 17.414" />
                            </svg>
                        </button>
                        <div class="lang-menu" style="display:none;">
                            <div class="lang-item" data-value="ber">
                                <span><?php echo isMobileDevice() ? "ⵜⵎⵣ" : "ⵜⴰⵎⴰⵣⵉⵖⵜ"; ?></span>
                            </div>
                            <div class="lang-item" data-value="fr">
                                <span><?php echo isMobileDevice() ? "FR" : "Français"; ?></span></div>
                        </div>
                    </div>
                    <input type="text" id="search-input" class="search-bar" placeholder="<?php echo __('Search'); ?>"
                        aria-label="<?= __('Search') ?>" autocomplete="off">
                    <button type="submit" class="search-btn" id="searchBtn"><svg aria-hidden="true" fill="none"
                            height="19" version="1.1" viewBox="0 0 19 19" width="19" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M2.04004 8.79391C2.04004 5.18401 5.02763 2.23297 8.74367 2.23297C12.4597 2.23297 15.4473 5.18401 15.4473 8.79391C15.4473 12.4038 12.4597 15.3549 8.74367 15.3549C5.02763 15.3549 2.04004 12.4038 2.04004 8.79391ZM8.74367 0.732971C4.22666 0.732971 0.540039 4.32838 0.540039 8.79391C0.540039 13.2595 4.22666 16.8549 8.74367 16.8549C10.4144 16.8549 11.9716 16.363 13.2706 15.5171C13.6981 15.2387 14.2697 15.2585 14.6339 15.6158L17.4752 18.4027C17.7668 18.6887 18.2338 18.6887 18.5254 18.4027V18.4027C18.8251 18.1087 18.8251 17.626 18.5254 17.332L15.725 14.5853C15.3514 14.2188 15.3296 13.6296 15.6192 13.1936C16.4587 11.9301 16.9473 10.4197 16.9473 8.79391C16.9473 4.32838 13.2607 0.732971 8.74367 0.732971Z"
                                fill="currentColor"></path>
                        </svg></button>
                    <div id="autocomplete-results" class="suggestions" style="display:none;"></div>
                </div>
            </form>
            <div class="search-type"> <?= __('Search type') ?>
                <label><input type="radio" name="stype" value="start" checked><?= __('Start by') ?></label>
                <label><input type="radio" name="stype" value="exact"><?= __('Exact') ?></label>
                <label><input type="radio" name="stype" value="contain"><?= __('Contains') ?></label>
            </div>
        </div>
    </div>
