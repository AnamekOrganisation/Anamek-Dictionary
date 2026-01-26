        <div class="relative rounded-xl overflow-hidden shadow-lg featured-word-card"
            style="background-image: url('<?= BASE_URL ?>/public/img/wod.webp'); background-size: cover; background-position: center center;">
            <div class="bg-gradient-custom "></div>
            <div class="word_of_the_day_content relative p-8 text-white">
                <div class="flex items-center gap-3 mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-star w-6 h-6 text-amber-500">
                        <polygon
                            points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2">
                        </polygon>
                    </svg>
                    <h3 class="text-2xl font-bold text-blue-900"><?php echo __('Word of the Day'); ?></h3>
                </div>
                <h4 class="tifinagh-display text-4xl mb-3 text-white"><?= $wordOfTheDay['word_tfng']; ?></h4>
                <p class="text-2xl font-semibold mb-4"><?= $wordOfTheDay['word_lat']; ?></p>
                <p class="text-base leading-relaxed mb-6"><?= $wordOfTheDay['definition_short']; ?></p>
                <div class="flex items-center gap-2 text-amber-300"><span class="text-sm font-medium">Voir la définition
                        complète</span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" class="lucide lucide-chevron-right w-4 h-4">
                        <path d="m9 18 6-6-6-6"></path>
                    </svg></div>
            </div>
        </div>
