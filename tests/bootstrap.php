<?php
/**
 * Bootstrap pour les tests PHPUnit
 * Configure l'environnement et charge les dépendances
 */

// Définir le répertoire racine
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

if (!defined('APP_PATH')) {
    define('APP_PATH', ROOT_PATH . '/app');
}

// Charger l'autoloader Composer
require_once ROOT_PATH . '/vendor/autoload.php';

// Charger les fichiers de configuration et helpers
require_once ROOT_PATH . '/config/bootstrap.php';

// Variables d'environnement pour les tests
putenv('APP_ENV=testing');
if (!defined('APP_ENV')) {
    define('APP_ENV', 'testing');
}
if (!defined('IA_ENABLED')) {
    define('IA_ENABLED', false); // Désactiver l'IA pour les tests
}

// Préparer un environnement HTTP de test et démarrer la session
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
$_SERVER['HTTP_ACCEPT'] = $_SERVER['HTTP_ACCEPT'] ?? 'text/html';
$_SERVER['HTTPS'] = $_SERVER['HTTPS'] ?? 'off';

Session::start();

// Classe TestCase de base pour les tests
if (!class_exists('TestCase')) {
    abstract class TestCase extends \PHPUnit\Framework\TestCase {
        protected static ?PDO $db = null;

        protected function setUp(): void {
            parent::setUp();
        }

        protected function tearDown(): void {
            parent::tearDown();
        }

        /**
         * Obtenir une connexion de test
         */
        protected static function getTestDatabase(): PDO {
            if (self::$db === null) {
                $host = defined('DB_HOST') ? DB_HOST : 'localhost';
                $name = defined('DB_NAME') ? DB_NAME : 'bk_business';
                $user = defined('DB_USER') ? DB_USER : 'root';
                $pass = defined('DB_PASS') ? DB_PASS : '';
                $charset = defined('DB_CHARSET') ? DB_CHARSET : 'utf8mb4';

                $dsn = "mysql:host={$host};dbname={$name};charset={$charset}";
                self::$db = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
            }
            return self::$db;
        }

        /**
         * Exécuter une requête SQL
         */
        protected function executeSQL(string $sql, array $params = []): PDOStatement {
            $db = self::getTestDatabase();
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        }

        /**
         * Assertion : vérifier qu'une ligne existe en base
         */
        protected function assertRowExists(string $table, array $conditions): void {
            $where = implode(' AND ', array_map(fn($k) => "$k = ?", array_keys($conditions)));
            $sql = "SELECT COUNT(*) as count FROM $table WHERE $where";
            $stmt = $this->executeSQL($sql, array_values($conditions));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->assertEquals(
                1,
                $result['count'],
                "Row with conditions " . json_encode($conditions) . " not found in table $table"
            );
        }

        /**
         * Assertion : vérifier qu'une ligne n'existe pas en base
         */
        protected function assertRowNotExists(string $table, array $conditions): void {
            $where = implode(' AND ', array_map(fn($k) => "$k = ?", array_keys($conditions)));
            $sql = "SELECT COUNT(*) as count FROM $table WHERE $where";
            $stmt = $this->executeSQL($sql, array_values($conditions));
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->assertEquals(
                0,
                $result['count'],
                "Row with conditions " . json_encode($conditions) . " should not exist in table $table"
            );
        }
    }
}
