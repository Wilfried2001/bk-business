<?php
// ============================================================
//  Configuration générale de l'application
// ============================================================
define('APP_NAME',    'BK Business');
define('APP_VERSION', '1.0.0');
define('BASE_URL',    'http://localhost:8000');

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
if (!defined('APP_PATH')) {
    define('APP_PATH', ROOT_PATH . '/app');
}
if (!defined('VIEWS_PATH')) {
    define('VIEWS_PATH', APP_PATH . '/views');
}

