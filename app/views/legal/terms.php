<?php include ROOT_PATH . '/app/views/partials/header.php'; ?>

<main class="main legal-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="premium-card rounded-4 shadow-sm p-4 p-lg-5 bg-white border-0">
                    <h1 class="fw-bold mb-4 text-primary">📜 <?= __('Conditions d’utilisation') ?></h1>
                    
                    <section class="mb-5">
                        <p class="lead text-secondary">En accédant et en utilisant le site Anamek, vous acceptez pleinement les présentes conditions d’utilisation.</p>
                    </section>

                    <section class="mb-5">
                        <h2 class="h4 fw-bold mb-3">Objet du site</h2>
                        <p>Anamek est une plateforme éducative et culturelle dédiée à la langue et au patrimoine amazighs. Le contenu est fourni à des fins informatives et pédagogiques.</p>
                    </section>

                    <section class="mb-5">
                        <h2 class="h4 fw-bold mb-3">Utilisation autorisée</h2>
                        <p>Le site est destiné à un usage personnel, éducatif et non commercial. Toute utilisation commerciale du contenu nécessite une autorisation écrite préalable.</p>
                    </section>

                    <section class="mb-5">
                        <h2 class="h4 fw-bold mb-3">Propriété intellectuelle</h2>
                        <p>L’ensemble du contenu du site (textes, données, structure, design, logos) est protégé par les lois relatives à la propriété intellectuelle. La reproduction ou la redistribution du contenu est autorisée uniquement à des fins éducatives, sous réserve de mentionner clairement la source « Anamek ».</p>
                    </section>

                    <section class="mb-5">
                        <h2 class="h4 fw-bold mb-3">Responsabilité de l’utilisateur</h2>
                        <p>L’utilisateur s’engage à :</p>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item bg-transparent border-0 ps-0"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Ne pas publier de contenu illégal, offensant ou trompeur</li>
                            <li class="list-group-item bg-transparent border-0 ps-0"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Ne pas tenter d’endommager ou de perturber le fonctionnement du site</li>
                            <li class="list-group-item bg-transparent border-0 ps-0"><i class="fas fa-exclamation-triangle text-warning me-2"></i> Respecter les lois et règlements en vigueur</li>
                        </ul>
                    </section>

                    <section class="mb-5">
                        <h2 class="h4 fw-bold mb-3">Suspension de l’accès</h2>
                        <p>Anamek se réserve le droit de suspendre ou de supprimer l’accès au site en cas de violation des présentes conditions.</p>
                    </section>

                    <section class="mb-0">
                        <h2 class="h4 fw-bold mb-3">Droit applicable</h2>
                        <p>Les présentes conditions sont régies par la législation applicable dans le pays d’exploitation du site.</p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
.legal-page { background: #f8faff; min-height: 80vh; }
.legal-page section p { line-height: 1.8; color: #444; }
.legal-page section h2 { color: #181d4b; }
.premium-card { border: 1px solid rgba(0,0,0,0.05) !important; }
</style>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
