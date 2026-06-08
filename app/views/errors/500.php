<div class="container mt-5">
    <div class="alert alert-danger text-center">
        <h1 class="display-4">Erreur interne du serveur</h1>
        <p class="lead"><?= e($message) ?></p>
        <?php if (!empty($details)): ?>
            <pre class="small bg-light p-3 rounded"><?= e($details) ?></pre>
        <?php endif; ?>
        <a href="<?= url('dashboard') ?>" class="btn btn-primary mt-3">Retour au tableau de bord</a>
    </div>
</div>
