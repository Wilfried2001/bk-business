<?php
// ============================================================
//  Configuration de la base de données
// ============================================================
define('DB_HOST',     Config::get('DB_HOST', 'localhost'));
define('DB_NAME',     Config::get('DB_NAME', 'bk_business'));
define('DB_USER',     Config::get('DB_USER', 'root'));
define('DB_PASS',     Config::get('DB_PASS', ''));
define('DB_CHARSET',  Config::get('DB_CHARSET', 'utf8mb4'));
