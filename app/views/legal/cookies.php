<?php include ROOT_PATH . '/app/views/partials/header.php'; ?>

<main class="main legal-page py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="premium-card rounded-4 shadow-sm p-4 p-lg-5 bg-white border-0">
                    <h1 class="fw-bold mb-4 text-primary">🍪 <?= __('Politique relative aux cookies') ?></h1>
                    
                    <section class="mb-5">
                        <h2 class="h4 fw-bold mb-3">Qu’est-ce qu’un cookie ?</h2>
                        <p>Un cookie est un petit fichier texte enregistré sur votre appareil lors de la consultation d’un site web. Il permet d’améliorer l’expérience utilisateur.</p>
                    </section>

                    <section class="mb-5">
                        <h2 class="h4 fw-bold mb-3">Utilisation des cookies</h2>
                        <p>Anamek utilise des cookies afin de :</p>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item bg-transparent border-0 ps-0">Assurer le bon fonctionnement du site</li>
                            <li class="list-group-item bg-transparent border-0 ps-0">Améliorer la navigation et l’expérience utilisateur</li>
                            <li class="list-group-item bg-transparent border-0 ps-0">Collecter des statistiques anonymes de fréquentation</li>
                        </ul>
                    </section>

                    <section class="mb-5">
                        <h2 class="h4 fw-bold mb-3">Types de cookies utilisés</h2>
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item bg-transparent border-0 ps-0 fw-medium">Cookies nécessaires : <span class="fw-normal text-secondary">indispensables au fonctionnement du site</span></li>
                            <li class="list-group-item bg-transparent border-0 ps-0 fw-medium">Cookies analytiques : <span class="fw-normal text-secondary">utilisés pour analyser l’utilisation du site et améliorer ses performances</span></li>
                            <li class="list-group-item bg-transparent border-0 ps-0 fw-medium">Cookies tiers : <span class="fw-normal text-secondary">pouvant être déposés par des services externes utilisés à des fins statistiques</span></li>
                        </ul>
                    </section>

                    <section class="mb-0">
                        <h2 class="h4 fw-bold mb-3">Gestion des cookies</h2>
                        <p>Vous pouvez configurer votre navigateur pour accepter, refuser ou supprimer les cookies. Le refus des cookies peut toutefois limiter certaines fonctionnalités du site.</p>
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
