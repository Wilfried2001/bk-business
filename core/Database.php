<?php
// ============================================================
//  core/Database.php — Connexion PDO (Singleton)
// ============================================================
class Database {

    private static ?Database $instance = null;
    private PDO $pdo;

// Méthode __construct : gère   construct. 
    private function __construct() {
        require_once ROOT_PATH . '/config/database.php';
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            $message = 'Connexion à la base de données impossible.';
            if (Config::get('APP_ENV', 'production') === 'development') {
                $message = 'Connexion BDD échouée : ' . $e->getMessage();
            }
            throw new RuntimeException($message, 0, $e);
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

// Méthode getConnection : gère getConnection. 
    public function getConnection(): PDO {
        return $this->pdo;
    }
}
