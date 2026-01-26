<?php
// UserController already handles auth and data fetching
// $user and $stats are passed to this view
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>
<style>
        .dashboard-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        .dashboard-header h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
        }
        .dashboard-header p {
            margin: 0;
            opacity: 0.9;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            color: #7f8c8d;
            font-size: 14px;
            font-weight: 500;
            text-transform: uppercase;
        }
        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
        }
        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }
        .quick-link {
            display: block;
            padding: 20px;
            background: white;
            border-radius: 8px;
            text-decoration: none;
            color: #2c3e50;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .quick-link:hover {
            transform: translateY(-3px);
        }
        .quick-link-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
        .quick-link-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .quick-link-desc {
            font-size: 13px;
            color: #7f8c8d;
        }
        .section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .section h2 {
            margin: 0 0 20px 0;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <?php include ROOT_PATH . '/app/views/partials/header.php'; ?>

    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>Bienvenue, <?= htmlspecialchars($user['full_name'] ?: $user['username']) ?>!</h1>
            <p>Type de compte: <?= ucfirst($user['user_type']) ?></p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div style="background: #d4edda; border-left: 4px solid #28a745; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                <?= htmlspecialchars($_SESSION['success']) ?>
                <?php 
                $isSignup = (strpos($_SESSION['success'], 'créé') !== false);
                unset($_SESSION['success']); 
                ?>
            </div>
            <script>
                if (typeof trackEvent === 'function') {
                    trackEvent('<?= $isSignup ? "sign_up" : "login" ?>', {
                        method: 'email'
                    });
                }
            </script>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Points de contribution</h3>
                <div class="stat-value"><?= $stats['contribution_points'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Contributions totales</h3>
                <div class="stat-value"><?= $stats['total_contributions'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Contributions approuvées</h3>
                <div class="stat-value"><?= $stats['approved_contributions'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Quiz complétés</h3>
                <div class="stat-value"><?= $stats['quizzes_taken'] ?></div>
            </div>
        </div>

        <div class="section">
            <h2>Actions rapides</h2>
            <div class="quick-links">
                <a href="<?= BASE_URL ?>/contribute/word" class="quick-link">
                    <div class="quick-link-icon">➕</div>
                    <div class="quick-link-title">Ajouter un mot</div>
                    <div class="quick-link-desc">Contribuez au dictionnaire</div>
                </a>
                <a href="<?= BASE_URL ?>/quizzes" class="quick-link">
                    <div class="quick-link-icon">🎮</div>
                    <div class="quick-link-title">Prendre un quiz</div>
                    <div class="quick-link-desc">Testez vos connaissances</div>
                </a>
                <a href="<?= BASE_URL ?>/user/contributions" class="quick-link">
                    <div class="quick-link-icon">📝</div>
                    <div class="quick-link-title">Mes contributions</div>
                    <div class="quick-link-desc">Voir le statut</div>
                </a>
                <a href="<?= BASE_URL ?>/user/profile" class="quick-link">
                    <div class="quick-link-icon">👤</div>
                    <div class="quick-link-title">Mon profil</div>
                    <div class="quick-link-desc">Gérer mon compte</div>
                </a>
            </div>
        </div>

        <?php if (!$user['email_verified']): ?>
            <div style="background: #fff3cd; border-left: 4px solid #ffc107; color: #856404; padding: 15px; border-radius: 4px;">
                ⚠️ Votre email n'est pas encore vérifié. Vérifiez votre boîte de réception.
            </div>
        <?php endif; ?>
    </div>

    <?php include ROOT_PATH . '/app/views/partials/footer.php'; ?>
<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
