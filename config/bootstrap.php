<?php
// ============================================================
//  config/bootstrap.php — Chargement de l'environnement et des configs
// ============================================================

Config::loadEnv(ROOT_PATH . '/.env');

require_once ROOT_PATH . '/config/app.php';
require_once ROOT_PATH . '/config/database.php';
