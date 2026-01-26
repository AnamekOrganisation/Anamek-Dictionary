        <div id="trendingWords">
            <div class="trendingWordsList g4 b blog no-shadow section">
                <div class="sous-titre-trend"><span><?= __('Most searched words') ?></span></div>
                <table style="width: 100%;">
                    <tbody>
                        <?php if (!empty($trendingWords)): ?>
                            <?php foreach ($trendingWords as $index => $word): ?>
                            <tr>
                                <td style="text-align:center; width: 40px;">
                                    <div class="round-number Mots-round">
                                        <span style="color:white;"><?= $index + 1 ?></span>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?= BASE_URL ?>/word/<?= urlencode($word['word_lat']) ?>-<?= $word['id'] ?>" class="trending-link">
                                        <span class="word-display" style="margin-right:10px; color:black;" 
                                              data-tfng="<?= htmlspecialchars($word['word_tfng']) ?>" 
                                              data-lat="<?= htmlspecialchars($word['word_lat']) ?>">
                                            <?= htmlspecialchars($word['word_tfng']) ?>
                                        </span>
                                    </a>
                                </td>
                                <td style="text-align: right; width: 30px;">
                                    <a href="<?= BASE_URL ?>/word/<?= urlencode($word['word_lat']) ?>-<?= $word['id'] ?>">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="gg-arrow-top-right">
                                            <line x1="7" y1="17" x2="17" y2="7"></line>
                                            <polyline points="7 7 17 7 17 17"></polyline>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 20px; color: #666;">
                                    <?= __('No recent searches') ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                            </div>
        </div>
