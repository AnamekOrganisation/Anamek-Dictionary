<?php include ROOT_PATH . '/app/views/partials/header.php'; ?>

<main class="main legal-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="premium-card rounded-4 shadow-sm p-4 p-lg-5 bg-white border-0">
                    <h1 class="fw-bold mb-4 text-primary">🔒 <?= __('Politique de confidentialité') ?></h1>
                    
                    <section class="mb-5">
                        <p class="lead text-secondary">Chez Anamek, nous respectons votre vie privée et nous nous engageons à protéger les données personnelles que vous partagez avec nous lors de l’utilisation du site.</p>
                    </section>

                    <section class="mb-5">
                        <h2 class="h4 fw-bold mb-3">Informations collectées</h2>
                        <p>Nous pouvons collecter les informations suivantes :</p>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item bg-transparent border-0 ps-0"><i class="fas fa-check-circle text-success me-2"></i> Données fournies volontairement par l’utilisateur (adresse e-mail, messages envoyés via les formulaires).</li>
                            <li class="list-group-item bg-transparent border-0 ps-0"><i class="fas fa-check-circle text-success me-2"></i> Données techniques collectées automatiquement (adresse IP, type de navigateur, système d’exploitation, pages visitées, durée de navigation).</li>
                        </ul>
                    </section>

                    <section class="mb-5">
                        <h2 class="h4 fw-bold mb-3">Utilisation des données</h2>
                        <p>Les informations collectées sont utilisées afin de :</p>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item bg-transparent border-0 ps-0">Améliorer le contenu et les fonctionnalités du site</li>
                            <li class="list-group-item bg-transparent border-0 ps-0">Analyser l’utilisation du site à des fins statistiques</li>
                            <li class="list-group-item bg-transparent border-0 ps-0">Répondre aux demandes et messages des utilisateurs</li>
                        </ul>
                    </section>

                    <section class="mb-5">
                        <h2 class="h4 fw-bold mb-3">Partage des données</h2>
                        <p>Anamek ne vend, ne loue et ne partage pas les données personnelles des utilisateurs avec des tiers, sauf lorsque cela est nécessaire au fonctionnement technique du site ou exigé par la loi.</p>
                    </section>

                    <section class="mb-5">
                        <h2 class="h4 fw-bold mb-3">Sécurité des données</h2>
                        <p>Nous mettons en place des mesures techniques et organisationnelles raisonnables pour protéger les données personnelles. Toutefois, aucune transmission de données sur Internet ne peut être garantie comme totalement sécurisée.</p>
                    </section>

                    <section class="mb-5">
                        <h2 class="h4 fw-bold mb-3">Modifications de la politique</h2>
                        <p>Cette politique de confidentialité peut être mise à jour à tout moment. Toute modification sera publiée sur cette page.</p>
                    </section>

                    <section class="mb-0">
                        <h2 class="h4 fw-bold mb-3">Contact</h2>
                        <p>Pour toute question concernant cette politique, vous pouvez nous contacter via la page <a href="<?= BASE_URL ?>/contact" class="text-decoration-none">« Contact »</a>.</p>
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
