<?php
// View: Admin Proverbs List
include ROOT_PATH . '/app/views/partials/dashboard-head.php';
?>

<div class="admin-layout">
    <?php 
    $current_page = 'proverbs';
    include ROOT_PATH . '/app/views/partials/admin_sidebar.php'; 
    ?>

    <main class="admin-main p-4 p-lg-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h2 class="fw-bold mb-1">Gestion des Proverbes 📖</h2>
                <p class="text-muted mb-0">Total : <?= number_format($total_proverbs) ?> proverbes enregistrés.</p>
            </div>
            <a href="<?= BASE_URL ?>/admin/add-proverb" class="btn btn-primary rounded-pill px-4 py-2 fw-bold">
                <i class="fas fa-plus me-2"></i>Nouveau Proverbe
            </a>
        </div>

        <?php if ($message): ?>
            <?= $message ?>
        <?php endif; ?>

        <!-- Search & Filter -->
        <div class="glass-card rounded-4 p-4 mb-5">
            <form method="GET" action="<?= BASE_URL ?>/admin/proverbs" class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-start-0" placeholder="Rechercher un proverbe (Tifinagh, Latin, Français...)" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100 rounded-3 fw-bold">Filtrer</button>
                </div>
            </form>
        </div>

        <!-- Proverbs Table -->
        <div class="glass-card rounded-4 overflow-hidden border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Proverbe (Tifinagh / Latin)</th>
                            <th class="py-3">Traduction Française</th>
                            <th class="text-end pe-4 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($proverbs)): ?>
                            <?php foreach ($proverbs as $p): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-primary mb-1"><?= htmlspecialchars($p['proverb_tfng']) ?></div>
                                        <div class="small text-muted italic"><?= htmlspecialchars($p['proverb_lat']) ?></div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;"><?= htmlspecialchars($p['translation_fr']) ?></div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="btn-group">
                                            <a href="<?= BASE_URL ?>/admin/edit-proverb?id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary rounded-start-pill px-3">
                                                <i class="fas fa-edit me-1"></i> Modifier
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger rounded-end-pill px-3" onclick="confirmDelete(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['proverb_tfng'])) ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    <i class="fas fa-feather-alt fa-3x mb-3 opacity-25"></i>
                                    <p>Aucun proverbe trouvé</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav class="mt-5">
                <ul class="pagination justify-content-center gap-2">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i == $current_page_num ? 'active' : '' ?>">
                            <a class="page-link rounded-3 border-0 shadow-sm" href="?page=<?= $i ?><?= isset($_GET['q']) ? '&q='.urlencode($_GET['q']) : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </main>
</div>

<!-- Delete Confirmation Modal -->
<form id="deleteForm" method="POST" action="<?= BASE_URL ?>/admin/delete-proverb">
    <?= csrf_field() ?>
    <input type="hidden" name="id" id="deleteId">
</form>

<script>
function confirmDelete(id, name) {
    if (confirm('Êtes-vous sûr de vouloir supprimer le proverbe : "' + name + '" ? Cette action est irréversible.')) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteForm').submit();
    }
}
</script>

<style>
.italic { font-style: italic; }
.glass-card { background: white; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
.admin-layout { background: #f8f9fa; min-height: 100vh; }
.pagination .page-link { color: #181d4b; padding: 10px 18px; }
.pagination .page-item.active .page-link { background-color: #f99417; color: white; }
</style>

<?php include ROOT_PATH . '/app/views/partials/dashboard-footer.php'; ?>
