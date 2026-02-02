<?php
// UserController already handles auth and data fetching
// $user and $stats are passed to this view
include ROOT_PATH . '/app/views/partials/header.php';
?>
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/admin.css">
    <style>
        :root {
            --premium-gradient-1: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            --premium-gradient-2: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
            --premium-gradient-3: linear-gradient(135deg, #10b981 0%, #3b82f6 100%);
            --premium-gradient-4: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(255, 255, 255, 0.4);
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

        .dashboard-header h1 {
            margin: 0 0 10px 0;
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            padding: 28px;
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 10px 25px rgba(0,0,0,0.03);
            transition: transform 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card h3 {
            margin: 0 0 12px 0;
            color: #64748b;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .stat-value {
            font-size: 42px;
            font-weight: 800;
            color: #1e293b;
            line-height: 1;
        }

        .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
            margin-top: 30px;
        }
        .quick-link {
            display: flex;
            flex-direction: column;
            padding: 32px;
            background: white;
            border-radius: 24px;
            text-decoration: none;
            color: #1e293b;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .quick-link:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            border-color: transparent;
        }

        .quick-link-icon-wrapper {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            transition: all 0.3s ease;
            position: relative;
            z-index: 2;
        }
        
        /* Icon Glow Effects */
        .link-add .quick-link-icon-wrapper { background: #ecfdf5; color: #10b981; }
        .link-quiz .quick-link-icon-wrapper { background: #fffbeb; color: #f59e0b; }
        .link-contrib .quick-link-icon-wrapper { background: #eff6ff; color: #3b82f6; }
        .link-profile .quick-link-icon-wrapper { background: #fdf2f8; color: #ec4899; }

        .quick-link:hover .quick-link-icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }

        .quick-link-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 8px;
            display: block;
        }
        .quick-link-desc {
            font-size: 15px;
            color: #64748b;
            line-height: 1.5;
            display: block;
        }

        .section {
            background: transparent;
            padding: 0;
            margin-bottom: 40px;
        }
        .section h2 {
            margin: 0 0 24px 0;
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .section h2::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }
    </style>


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
                <!-- Add Word -->
                <a href="<?= BASE_URL ?>/contribute/word" class="quick-link link-add">
                    <div class="quick-link-icon-wrapper">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-opacity="0.2"/>
                        </svg>
                    </div>
                    <span class="quick-link-title">Ajouter un mot</span>
                    <span class="quick-link-desc">Partagez vos connaissances et enrichissez le dictionnaire.</span>
                </a>

                <!-- Take Quiz -->
                <a href="<?= BASE_URL ?>/quizzes" class="quick-link link-quiz">
                    <div class="quick-link-icon-wrapper">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2" y="6" width="20" height="12" rx="3" stroke="currentColor" stroke-width="2"/>
                            <path d="M6 12H8M7 11V13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="15.5" cy="10.5" r="1" fill="currentColor"/>
                            <circle cx="17.5" cy="13.5" r="1" fill="currentColor"/>
                            <path d="M11 6V4M13 6V4" stroke="currentColor" stroke-width="2" stroke-opacity="0.3"/>
                        </svg>
                    </div>
                    <span class="quick-link-title">Prendre un quiz</span>
                    <span class="quick-link-desc">Évaluez vos progrès avec des défis linguistiques interactifs.</span>
                </a>

                <!-- Contributions -->
                <a href="<?= BASE_URL ?>/user/contributions" class="quick-link link-contrib">
                    <div class="quick-link-icon-wrapper">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 20H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15 5L19 9" stroke="currentColor" stroke-width="2" stroke-opacity="0.3"/>
                        </svg>
                    </div>
                    <span class="quick-link-title">Mes contributions</span>
                    <span class="quick-link-desc">Suivez l'état de vos soumissions et vos statistiques de partage.</span>
                </a>

                <!-- Profile -->
                <a href="<?= BASE_URL ?>/user/profile" class="quick-link link-profile">
                    <div class="quick-link-icon-wrapper">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 21V19C20 16.7909 18.2091 15 16 15H8C5.79086 15 4 16.7909 4 19V21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            <circle cx="12" cy="7" r="4" stroke="currentColor" stroke-width="2"/>
                            <path d="M12 11V13" stroke="currentColor" stroke-width="2" stroke-opacity="0.3" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span class="quick-link-title">Mon profil</span>
                    <span class="quick-link-desc">Personnalisez votre compte et gérez vos préférences.</span>
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
