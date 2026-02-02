<?php
// my-contributions.php view - Redesigned with premium dashboard aesthetic
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<style>
    :root {
        --premium-gradient-1: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
        --glass-bg: rgba(255, 255, 255, 0.7);
        --glass-border: rgba(255, 255, 255, 0.4);
        --admin-accent: #f99417;
    }

    .dashboard-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
        font-family: 'Outfit', 'Inter', sans-serif;
    }

    .dashboard-header {
        background: var(--premium-gradient-1);
        color: white;
        padding: 40px;
        border-radius: 24px;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(99, 102, 241, 0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .dashboard-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        filter: blur(50px);
    }

    .dashboard-header .content { position: relative; z-index: 2; }
    .dashboard-header h1 {
        margin: 0 0 5px 0;
        font-size: 32px;
        font-weight: 800;
        letter-spacing: -0.5px;
    }
    .dashboard-header p { margin: 0; opacity: 0.9; font-size: 16px; }

    .btn-premium {
        background: white;
        color: #6366f1;
        padding: 12px 24px;
        border-radius: 14px;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        position: relative;
        z-index: 2;
    }
    .btn-premium:hover { transform: translateY(-3px); box-shadow: 0 15px 20px rgba(0,0,0,0.15); color: #4f46e5; }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }
    .stat-card {
        background: var(--glass-bg);
        backdrop-filter: blur(12px);
        padding: 24px;
        border-radius: 20px;
        border: 1px solid var(--glass-border);
        box-shadow: 0 10px 25px rgba(0,0,0,0.03);
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }
    .stat-info h2 { font-size: 32px; font-weight: 800; margin: 0; color: #1e293b; }
    .stat-info p { font-size: 13px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin: 0; }

    .stat-total .stat-icon { background: #eff6ff; color: #3b82f6; }
    .stat-approved .stat-icon { background: #ecfdf5; color: #10b981; }
    .stat-pending .stat-icon { background: #fffbeb; color: #f59e0b; }

    .table-container {
        background: white;
        border-radius: 24px;
        padding: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }
    .modern-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .modern-table th {
        padding: 20px 24px;
        text-align: left;
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .modern-table td {
        padding: 20px 24px;
        color: #334155;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }
    .modern-table tr:last-child td { border-bottom: none; }
    .modern-table tr:hover td { background-color: #f8fafc; }

    .type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        background: #f1f5f9;
        color: #475569;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        text-transform: capitalize;
    }
    .status-pending { background: #fffbeb; color: #b45309; }
    .status-approved { background: #ecfdf5; color: #047857; }
    .status-rejected { background: #fef2f2; color: #b91c1c; }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-icon {
        width: 80px;
        height: 80px;
        background: #f1f5f9;
        color: #94a3b8;
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin: 0 auto 24px;
    }
    .empty-state h3 { font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px; }
    .empty-state p { color: #64748b; margin-bottom: 24px; }

    @media (max-width: 768px) {
        .dashboard-header { flex-direction: column; text-align: center; gap: 24px; padding: 30px 20px; }
        .modern-table th:nth-child(3), .modern-table td:nth-child(3) { display: none; }
        .stat-card { padding: 20px; }
    }
</style>

<?php include ROOT_PATH . '/app/views/partials/header.php'; ?>

<div class="dashboard-container">
    <div class="dashboard-header">
        <div class="content">
            <h1>Mes Contributions</h1>
            <p>Gérez et suivez vos partages avec la communauté.</p>
        </div>
        <a href="<?= BASE_URL ?>/contribute/word" class="btn-premium">
            <i class="fas fa-plus"></i>
            <span>Nouveau mot</span>
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4 p-3 rounded-4 d-flex align-items-center gap-3">
            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;">
                <i class="fas fa-check small"></i>
            </div>
            <div class="fw-medium"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card stat-total">
            <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
            <div class="stat-info">
                <h2><?= number_format($stats['total']) ?></h2>
                <p>Total</p>
            </div>
        </div>
        <div class="stat-card stat-approved">
            <div class="stat-icon"><i class="fas fa-check-double"></i></div>
            <div class="stat-info">
                <h2><?= number_format($stats['approved']) ?></h2>
                <p>Approuvées</p>
            </div>
        </div>
        <div class="stat-card stat-pending">
            <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
            <div class="stat-info">
                <h2><?= number_format($stats['pending']) ?></h2>
                <p>En attente</p>
            </div>
        </div>
    </div>

    <div class="table-container">
        <?php if (empty($contributions)): ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="fas fa-pen-nib"></i></div>
                <h3>Aucune contribution</h3>
                <p>Vous n'avez pas encore partagé de mots ou d'exemples.</p>
                <a href="<?= BASE_URL ?>/contribute/word" class="btn btn-primary rounded-pill px-4 fw-bold">Commencer à contribuer</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Détails</th>
                            <th>Date</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contributions as $c): ?>
                            <tr>
                                <td>
                                    <span class="type-badge">
                                        <i class="fas <?= ($c['contribution_type'] === 'word') ? 'fa-book' : 'fa-quote-left' ?> small opacity-50"></i>
                                        <?= ucfirst($c['contribution_type']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($c['contribution_type'] === 'word'): ?>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold font-tifinagh fs-5" style="color: #1e293b;"><?= htmlspecialchars($c['content_after']['word_tfng']) ?></span>
                                            <span class="small text-muted"><?= htmlspecialchars($c['content_after']['word_lat']) ?></span>
                                        </div>
                                    <?php elseif ($c['contribution_type'] === 'example'): ?>
                                        <div class="d-flex flex-column">
                                            <span class="font-tifinagh" style="color: #475569;"><?= htmlspecialchars(mb_strimwidth($c['content_after']['example_tfng'], 0, 100, "...")) ?></span>
                                            <span class="small text-muted italic"><?= htmlspecialchars(mb_strimwidth($c['content_after']['example_lat'], 0, 80, "...")) ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-medium"><?= date('d M Y', strtotime($c['created_at'])) ?></span>
                                        <span class="small text-muted opacity-75"><?= date('H:i', strtotime($c['created_at'])) ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-<?= $c['status'] ?>">
                                        <i class="fas <?= $c['status'] === 'approved' ? 'fa-check' : ($c['status'] === 'pending' ? 'fa-clock' : 'fa-times') ?> small opacity-75"></i>
                                        <?= ucfirst($c['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
