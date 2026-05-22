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

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';
require_once ROOT_PATH . '/vendor/autoload.php';

// Démarrer la session
Session::start();

// Instancier le routeur
$router = new Router();

// Charger les routes
require_once ROOT_PATH . '/routes/web.php';

// Résoudre la requête
$router->resolve();
