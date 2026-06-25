<div class="container mt-5">
    <div class="app-alert app-alert-danger text-center">
        <h1 class="display-4">Erreur interne du serveur</h1>
        <p class="lead"><?= e($message) ?></p>
        <?php if (!empty($details)): ?>
            <pre class="small bg-light p-3 rounded"><?= e($details) ?></pre>
        <?php endif; ?>
        <a href="<?= url('dashboard') ?>" class="inline-flex items-center font-semibold px-4 py-2 rounded-md text-sm tracking-wide bg-gradient-to-r from-[#0d47a1] to-[#1565c0] text-white mt-3">Retour au tableau de bord</a>
    </div>
</div>
