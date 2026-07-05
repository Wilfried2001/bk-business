<?php
require_once __DIR__ . '/../core/Config.php';
Config::loadEnv(__DIR__ . '/../.env');
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}
require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/core/Database.php';
try {
    $db = Database::getInstance()->getConnection();
    echo 'OK';
} catch (Exception $e) {
    echo 'ERR: ' . $e->getMessage();
}
