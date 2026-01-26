<div class="relative rounded-xl overflow-hidden shadow-lg featured-word-card proverb-card mb-8"
    style="background-image: url('<?= BASE_URL ?>/public/img/bg.webp'); background-size: cover; background-position: center;">
    
    <!-- Use the same gradient as Word of the Day -->
    <div class="bg-gradient-custom"></div>
    
    <div class="relative p-8 text-white z-10 flex flex-col h-full justify-between"> 
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <svg class="w-6 h-6 text-amber-500" fill="currentColor" viewBox="0 0 32 32">
                <path d="M9.563 8.469l-0.813-1.25c-5.625 3.781-8.75 8.375-8.75 12.156 0 3.656 2.688 5.375 4.969 5.375 2.875 0 4.906-2.438 4.906-5 0-2.156-1.375-4-3.219-4.688-0.531-0.188-1.031-0.344-1.031-1.25 0-1.156 0.844-2.875 3.938-5.344zM21.969 8.469l-0.813-1.25c-5.563 3.781-8.75 8.375-8.75 12.156 0 3.656 2.75 5.375 5.031 5.375 2.906 0 4.969-2.438 4.969-5 0-2.156-1.406-4-3.313-4.688-0.531-0.188-1-0.344-1-1.25 0-1.156 0.875-2.875 3.875-5.344z"></path>
            </svg>
            <h3 class="text-2xl font-bold text-blue-900"><?= __('Proverb of the Day'); ?></h3>
        </div>

        <!-- Content -->
        <a href="<?= BASE_URL ?>/proverb/<?= $proverbOfTheDay['id']; ?>" class="block text-decoration-none group">
            <!-- Added proverb-display class for JS selector compatibility -->
            <span class="tifinagh-display proverb-display text-4xl mb-4 text-white font-medium leading-relaxed group-hover:text-amber-100 transition-colors" 
                  data-tfng="<?= htmlspecialchars($proverbOfTheDay['proverb_tfng']); ?>"
                  data-lat="<?= htmlspecialchars($proverbOfTheDay['proverb_lat']); ?>" 
                  dir="ltr">
                <?= htmlspecialchars($proverbOfTheDay['proverb_tfng']); ?>
            </span>
            
        </a>

        <!-- Actions -->
        <div class="flex items-center gap-3 mt-8">
            <button class="flex items-center gap-2 px-6 py-2.5 rounded-full bg-white/20 hover:bg-white/30 transition-all text-sm font-bold text-white backdrop-blur-sm border border-white/20 hover:-translate-y-0.5" onclick="shareProverb(this)">
                <i class="fas fa-share-alt"></i> <?= __('Share') ?>
            </button>
            <button class="flex items-center gap-2 px-6 py-2.5 rounded-full bg-linear-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 shadow-amber-900/20 transition-all text-sm font-bold text-white shadow-lg hover:-translate-y-0.5 border border-white/10" onclick="copyProverb(this)">
                <i class="fas fa-copy"></i> <?= __('Copy') ?>
            </button>
        </div>
    </div>
</div>
