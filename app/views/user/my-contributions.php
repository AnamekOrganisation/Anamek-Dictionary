<?php
// my-contributions.php view
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>
<style>
        .container { max-width: 1000px; margin: 40px auto; padding: 20px; }
        .stats { display: flex; gap: 20px; margin-bottom: 30px; }
        .stat-card { flex: 1; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
        .stat-card .num { font-size: 24px; font-weight: bold; display: block; }
        .stat-card .label { color: #666; font-size: 14px; }
        
        table { width: 100%; border-collapse: collapse; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #f8f9fa; color: #333; }
        
        .status-badge { padding: 5px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        
        .btn { display: inline-block; padding: 10px 20px; background: #3498db; color: #fff; text-decoration: none; border-radius: 5px; }
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <?php include ROOT_PATH . '/app/views/partials/header.php'; ?>

    <div class="container">
        <div class="header-actions">
            <h1>Mes Contributions</h1>
            <a href="<?= BASE_URL ?>/contribute/word" class="btn">Ajouter un nouveau mot</a>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?= $_SESSION['success'] ?>
                <?php unset($_SESSION['success']); ?>
            </div>
            <script>
                if (typeof trackEvent === 'function') {
                    trackEvent('generate_lead', {
                        event_label: 'contribution_submitted',
                        event_category: 'community'
                    });
                }
            </script>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card">
                <span class="num"><?= $stats['total'] ?></span>
                <span class="label">Total</span>
            </div>
            <div class="stat-card">
                <span class="num"><?= $stats['approved'] ?></span>
                <span class="label">Approuvées</span>
            </div>
            <div class="stat-card">
                <span class="num"><?= $stats['pending'] ?></span>
                <span class="label">En attente</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Détails</th>
                    <th>Date</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contributions)): ?>
                    <tr><td colspan="4" style="text-align:center;">Vous n'avez pas encore de contributions.</td></tr>
                <?php else: ?>
                    <?php foreach ($contributions as $c): ?>
                        <tr>
                            <td><?= ucfirst($c['contribution_type']) ?></td>
                            <td>
                                <?php if ($c['contribution_type'] === 'word'): ?>
                                    <strong><?= htmlspecialchars($c['content_after']['word_tfng']) ?></strong> 
                                    (<?= htmlspecialchars($c['content_after']['word_lat']) ?>)
                                <?php elseif ($c['contribution_type'] === 'example'): ?>
                                    <?= htmlspecialchars(substr($c['content_after']['example_tfng'], 0, 50)) ?>...
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($c['created_at'])) ?></td>
                            <td>
                                <span class="status-badge status-<?= $c['status'] ?>">
                                    <?= ucfirst($c['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
