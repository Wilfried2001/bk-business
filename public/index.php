<?php
// ============================================================
//  public/index.php — Point d'entrée unique de l'application
// ============================================================

// Autoload des classes core
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
if (!defined('APP_PATH')) {
    define('APP_PATH', ROOT_PATH . '/app');
}
if (!defined('VIEWS_PATH')) {
    define('VIEWS_PATH', APP_PATH . '/views');
}

require_once ROOT_PATH . '/vendor/autoload.php';
require_once ROOT_PATH . '/config/bootstrap.php';

// Démarrer la session
Session::start();

if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: interest-cohort=()');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// Instancier le routeur
$router = new Router();

// Charger les routes
require_once ROOT_PATH . '/routes/web.php';

// Résoudre la requête
try {
    $router->resolve();
} catch (Throwable $e) {
    http_response_code(500);
    require_once VIEWS_PATH . '/layouts/header.php';
    $message = 'Une erreur est survenue. Veuillez réessayer plus tard.';
    $details = APP_ENV === 'development' ? $e->getMessage() : '';
    require_once VIEWS_PATH . '/errors/500.php';
    require_once VIEWS_PATH . '/layouts/footer.php';
}
