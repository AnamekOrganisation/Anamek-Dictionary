<?php require_once ROOT_PATH . '/app/views/partials/header.php'; ?>
<div class="container">
    <h2>Résultats de recherche</h2>
    <?php if (!empty($proverbs)): ?>
        <ul>
            <?php foreach ($proverbs as $proverb): ?>
                <li>
                    <?= htmlspecialchars($proverb['proverb_lat']) ?> 
                    <a href="index.php?action=edit-proverb&id=<?= $proverb['id'] ?>">Modifier</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>Aucun proverbe trouvé.</p>
    <?php endif; ?>
</div>
<?php require_once ROOT_PATH . '/app/views/partials/footer.php'; ?>