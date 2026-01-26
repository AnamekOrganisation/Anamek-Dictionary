<?php
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<div class="admin-layout">
    <?php 
    $current_page = 'analytics';
    include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; 
    ?>

    <main class="admin-main p-4 p-lg-5">
        <div class="mb-5">
            <h2 class="fw-bold mb-1">Analytique Détaillée 📊</h2>
            <p class="text-muted">Analyse approfondie du trafic et de l'engagement des utilisateurs (Basé sur les visiteurs uniques).</p>
        </div>

        <div class="row g-4 mb-5">
            <!-- Traffic Breakdown -->
            <div class="col-lg-8">
                <div class="glass-card rounded-4 p-4">
                    <h5 class="fw-bold mb-4">Évolution du Trafic (30 Jours)</h5>
                    <div style="height: 400px;">
                        <canvas id="detailedTrafficChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Popular Pages -->
            <div class="col-lg-4">
                <div class="glass-card rounded-4 p-4 h-100">
                    <h5 class="fw-bold mb-4">Pages les plus vues</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Page</th>
                                    <th class="text-end">Vues</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topPages as $page): ?>
                                <tr>
                                    <td class="small text-truncate" style="max-width: 200px;"><?= htmlspecialchars($page['page_url']) ?></td>
                                    <td class="text-end fw-bold"><?= number_format($page['count']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Search Trends -->
            <div class="col-12">
                <div class="glass-card rounded-4 p-4">
                    <h5 class="fw-bold mb-4">Tendances de Recherche</h5>
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                        <?php foreach (array_slice($popularSearches, 0, 8) as $search): ?>
                        <div class="col">
                            <div class="p-3 border rounded-3 bg-light d-flex justify-content-between align-items-center">
                                <span class="fw-semibold"><?= htmlspecialchars($search['query']) ?></span>
                                <span class="badge bg-primary rounded-pill"><?= $search['count'] ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('detailedTrafficChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chartLabels) ?>,
            datasets: [{
                label: 'Visiteurs Uniques',
                data: <?= json_encode($chartData) ?>,
                backgroundColor: '#181d4b',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
});
</script>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
