<div class="container-fluid">
    <div class="row">
        <aside class="col-lg-2 bg-white border-end sidebar py-4">
            <div class="px-3 mb-4 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-compass"></i> Bk business</h5>
                <button class="btn btn-sm btn-outline-secondary d-lg-none close-sidebar" type="button"
                    aria-label="Fermer le menu">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="list-group list-group-flush">
                <a class="list-group-item list-group-item-action" href="<?= url('dashboard') ?>">
                    <i class="bi bi-speedometer2"></i> Tableau de bord
                </a>
                <?php if (Auth::hasRole(['AGENT', 'SUPERVISEUR', 'DG'])): ?>
                <a class="list-group-item list-group-item-action" href="<?= url('transactions') ?>">
                    <i class="bi bi-arrow-left-right"></i> Transactions
                </a>
                <?php endif; ?>
                <?php if (Auth::hasRole(['SUPERVISEUR', 'DG'])): ?>
                <a class="list-group-item list-group-item-action" href="<?= url('stocks') ?>">
                    <i class="bi bi-boxes"></i> Stocks
                </a>
                <a class="list-group-item list-group-item-action" href="<?= url('alertes') ?>">
                    <i class="bi bi-exclamation-triangle"></i> Alertes
                </a>
                <?php endif; ?>
                <?php if (Auth::hasRole(['COMPTABLE', 'DG'])): ?>
                <a class="list-group-item list-group-item-action" href="<?= url('commissions') ?>">
                    <i class="bi bi-percent"></i> Commissions
                </a>
                <a class="list-group-item list-group-item-action" href="<?= url('commissions/config') ?>">
                    <i class="bi bi-gear"></i> Paramétrage commissions
                </a>
                <?php endif; ?>
                <?php if (Auth::hasRole(['SUPERVISEUR', 'COMPTABLE', 'DG'])): ?>
                <a class="list-group-item list-group-item-action" href="<?= url('rapports') ?>">
                    <i class="bi bi-bar-chart"></i> Rapports
                </a>
                <?php endif; ?>
                <?php if (Auth::hasRole(['DG'])): ?>
                <a class="list-group-item list-group-item-action" href="<?= url('utilisateurs') ?>">
                    <i class="bi bi-people"></i> Utilisateurs
                </a>
                <?php endif; ?>
            </div>
        </aside>
        <main class="col-lg-10 main-content">
            <?php if ($success = Session::getFlash('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= e($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
            <?php endif; ?>
            <?php if ($error = Session::getFlash('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= e($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
            <?php endif; ?>