<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? ('Connexion — ' . APP_NAME)) ?></title>
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= url('css/business-views.css') ?>">
    <link rel="stylesheet" href="<?= url('css/dark-fix.css') ?>">
    <script src="<?= url('js/icons.js') ?>"></script>
</head>

<body>
    <div class="bk-login-shell">
        <div class="bk-login-card">
            <div class="bk-login-brand">
                <p class="bk-eyebrow">Espace sécurisé</p>
                <h1><?= e(APP_NAME) ?></h1>
                <p>Connexion à la gestion des transactions, stocks et commissions.</p>
            </div>

            <div class="bk-login-form">
                <?php if ($error = Session::getFlash('error')): ?>
                    <div class="app-alert app-alert-danger mb-4" role="alert">
                        <i data-lucide="alert-circle"></i> <?= e($error) ?>
                    </div>
                <?php endif; ?>
                <?php if ($success = Session::getFlash('success')): ?>
                    <div class="app-alert app-alert-success mb-4" role="alert">
                        <i data-lucide="check-circle"></i> <?= e($success) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= url('auth/login') ?>" method="post" id="loginForm" class="bk-page">
                    <?= csrfField() ?>

                    <div>
                        <label for="email" class="bk-label">Adresse email</label>
                        <input type="email" id="email" name="email" class="bk-field" placeholder="votre@email.com" required autofocus>
                    </div>

                    <div>
                        <label for="password" class="bk-label">Mot de passe</label>
                        <input type="password" id="password" name="password" class="bk-field" placeholder="Votre mot de passe" required>
                    </div>

                    <button type="submit" class="bk-btn bk-btn-primary w-full">
                        <i data-lucide="log-in"></i> Se connecter
                    </button>
                </form>

                <p class="text-center text-gray-500 text-xs mt-4">
                    <i data-lucide="shield-check"></i> Connexion sécurisée
                </p>
            </div>
        </div>
    </div>

    <script nonce="<?= e(cspNonce()) ?>">
        if (window.lucide) {
            lucide.replace();
        }
    </script>
</body>

</html>
