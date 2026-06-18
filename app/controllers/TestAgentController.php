<?php
// ============================================================
//  app/controllers/TestAgentController.php — Test l'Agent IA
// ============================================================

class TestAgentController extends Controller {

    public function run(): void {
        // À ne pas utiliser en production !
        if (Config::get('APP_ENV', 'production') === 'production') {
            http_response_code(403);
            die('Tests désactivés en production');
        }

        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Tests - Agent IA BK_Business</title>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { background-color: #f8f9fa; padding: 2rem 0; }
                .test-result { margin-bottom: 1.5rem; }
                .test-pass { border-left: 4px solid #28a745; background-color: #d4edda; }
                .test-fail { border-left: 4px solid #dc3545; background-color: #f8d7da; }
                .test-warn { border-left: 4px solid #ffc107; background-color: #fff3cd; }
                .test-result > div { padding: 1rem; }
                h1 { color: #333; margin-bottom: 1.5rem; }
                hr { border-top: 2px solid #dee2e6; margin: 2rem 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>🧪 Tests de l'Agent IA BK_Business</h1>

                <?php
                $results = [];

                // Test 1 : Fichier de configuration
                $results[] = $this->testConfigFile();

                // Test 2 : Classe ApiClaude
                $results[] = $this->testApiClaudeClass();

                // Test 3 : Clé API
                $results[] = $this->testApiKey();

                // Test 4 : Connexion à la base de données
                $results[] = $this->testDatabase();

                // Test 5 : Contrôleur
                $results[] = $this->testController();

                // Test 6 : Routes
                $results[] = $this->testRoutes();

                // Résumé
                echo "<hr>";
                echo "<h2>📊 Résumé</h2>";

                $pass = array_filter($results, fn($r) => $r['status'] === 'pass');
                $fail = array_filter($results, fn($r) => $r['status'] === 'fail');

                echo "<div class='alert alert-info'>";
                echo "<p><strong>✅ Réussis : " . count($pass) . " / " . count($results) . "</strong></p>";
                if (count($fail) > 0) {
                    echo "<p><strong>❌ Échoués : " . count($fail) . "</strong></p>";
                }
                echo "</div>";

                if (count($fail) === 0) {
                    echo "<div class='alert alert-success'>";
                    echo "<h3>🎉 Tous les tests sont passés !</h3>";
                    echo "<p>L'Agent IA est <strong>prêt pour être utilisé</strong>.</p>";
                    echo '<p><a href="' . url('agent') . '" class="btn btn-primary">Accéder à l\'Agent IA →</a></p>';
                    echo "</div>";
                } else {
                    echo "<div class='alert alert-warning'>";
                    echo "<h3>⚠️ Certains tests ont échoué</h3>";
                    echo "<p>Consultez les détails ci-dessus pour corriger les problèmes.</p>";
                    echo "</div>";
                }
                ?>

            </div>
        </body>
        </html>
        <?php
    }

    private function testConfigFile(): array {
        $file = ROOT_PATH . '/config/agent.php';
        $pass = file_exists($file);

        echo "<div class='test-result " . ($pass ? 'test-pass' : 'test-fail') . "'>";
        echo "<div>";
        echo "<h3>" . ($pass ? "✅" : "❌") . " Test 1 — Fichier de configuration</h3>";

        if ($pass) {
            echo "<p><code>config/agent.php</code> <strong>existe</strong></p>";
            $config = require $file;
            echo "<p><strong>Configuration chargée :</strong></p>";
            echo "<ul>";
            echo "<li>Modèle : <code>" . $config['model'] . "</code></li>";
            echo "<li>Max tokens : " . $config['max_tokens'] . "</li>";
            echo "</ul>";
        } else {
            echo "<p><code>config/agent.php</code> <strong>introuvable</strong></p>";
        }
        echo "</div>";
        echo "</div>";

        return ['test' => 'config_file', 'status' => $pass ? 'pass' : 'fail'];
    }

    private function testApiClaudeClass(): array {
        $file = ROOT_PATH . '/app/models/ApiClaude.php';
        $pass = file_exists($file);

        echo "<div class='test-result " . ($pass ? 'test-pass' : 'test-fail') . "'>";
        echo "<div>";
        echo "<h3>" . ($pass ? "✅" : "❌") . " Test 2 — Classe ApiClaude</h3>";

        if ($pass) {
            echo "<p><code>app/models/ApiClaude.php</code> <strong>existe</strong></p>";

            require_once $file;
            $reflect = new ReflectionClass('ApiClaude');
            $methods = $reflect->getMethods(ReflectionMethod::IS_PUBLIC);

            echo "<p><strong>Méthodes publiques :</strong></p>";
            echo "<ul>";
            foreach ($methods as $method) {
                if ($method->getDeclaringClass()->getName() === 'ApiClaude') {
                    echo "<li><code>" . $method->getName() . "()</code></li>";
                }
            }
            echo "</ul>";
        } else {
            echo "<p><code>app/models/ApiClaude.php</code> <strong>introuvable</strong></p>";
        }
        echo "</div>";
        echo "</div>";

        return ['test' => 'api_claude_class', 'status' => $pass ? 'pass' : 'fail'];
    }

    private function testApiKey(): array {
        echo "<div class='test-result " . (empty(Config::get('ANTHROPIC_API_KEY')) ? 'test-fail' : 'test-pass') . "'>";
        echo "<div>";
        echo "<h3>" . (empty(Config::get('ANTHROPIC_API_KEY')) ? "❌" : "✅") . " Test 3 — Clé API Anthropic</h3>";

        $apiKey = Config::get('ANTHROPIC_API_KEY', '');

        if (empty($apiKey)) {
            echo "<p><strong>Clé API non trouvée</strong></p>";
            echo "<p>⚠️ Solution : Ajoutez à votre <code>.env</code> :</p>";
            echo "<pre>ANTHROPIC_API_KEY=sk-ant-...</pre>";
        } else {
            $valid = preg_match('/^sk-ant-[A-Za-z0-9_-]{20,}$/', $apiKey);
            if ($valid) {
                echo "<p><strong>✅ Clé API trouvée (format valide)</strong></p>";
                echo "<p>Clé : <code>sk-ant-" . substr($apiKey, 7, 15) . "...</code></p>";
            } else {
                echo "<p><strong>⚠️ Clé API trouvée mais format suspect</strong></p>";
                echo "<p>Vérifiez qu'elle commence par <code>sk-ant-</code></p>";
            }
        }
        echo "</div>";
        echo "</div>";

        return ['test' => 'api_key', 'status' => empty($apiKey) ? 'fail' : 'pass'];
    }

    private function testDatabase(): array {
        echo "<div class='test-result'>";
        echo "<div>";
        echo "<h3>Test 4 — Connexion à la base de données</h3>";

        try {
            $db = Database::getInstance()->getConnection();

            // Test simple
            $stmt = $db->prepare("SELECT COUNT(*) as count FROM utilisateur");
            $stmt->execute();
            $result = $stmt->fetch();

            echo "<p>✅ <strong>Connexion réussie</strong></p>";
            echo "<p>Utilisateurs dans la base : <strong>" . $result['count'] . "</strong></p>";

            // Vérifier les tables
            $tables = ['service', 'transaction', 'solde_service'];
            $allExist = true;

            echo "<p><strong>Tables nécessaires :</strong></p>";
            echo "<ul>";

            foreach ($tables as $table) {
                $tableName = $db->quote($table);
                $stmt = $db->query("SHOW TABLES LIKE $tableName");
                $exists = $stmt && $stmt->fetch() !== false;
                $allExist = $allExist && $exists;
                echo "<li>" . ($exists ? "✅" : "❌") . " <code>$table</code></li>";
            }
            echo "</ul>";

            $status = $allExist ? 'pass' : 'fail';
            echo "</div>";
            echo "</div>";

            return ['test' => 'database', 'status' => $status];

        } catch (Exception $e) {
            echo "<p>❌ <strong>Erreur de connexion</strong></p>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
            echo "</div>";
            return ['test' => 'database', 'status' => 'fail'];
        }
    }

    private function testController(): array {
        $file = ROOT_PATH . '/app/controllers/AgentIAController.php';
        $pass = file_exists($file);

        echo "<div class='test-result " . ($pass ? 'test-pass' : 'test-fail') . "'>";
        echo "<div>";
        echo "<h3>" . ($pass ? "✅" : "❌") . " Test 5 — Contrôleur AgentIA</h3>";

        if (!$pass) {
            echo "<p><code>app/controllers/AgentIAController.php</code> <strong>introuvable</strong></p>";
        } else {
            echo "<p><code>app/controllers/AgentIAController.php</code> <strong>trouvé</strong></p>";

            require_once $file;
            $reflect = new ReflectionClass('AgentIAController');
            $methods = $reflect->getMethods(ReflectionMethod::IS_PUBLIC);

            echo "<p><strong>Méthodes publiques :</strong></p>";
            echo "<ul>";
            foreach ($methods as $method) {
                if ($method->getDeclaringClass()->getName() === 'AgentIAController') {
                    echo "<li><code>" . $method->getName() . "()</code></li>";
                }
            }
            echo "</ul>";
        }
        echo "</div>";
        echo "</div>";

        return ['test' => 'controller', 'status' => $pass ? 'pass' : 'fail'];
    }

    private function testRoutes(): array {
        $routesFile = ROOT_PATH . '/routes/web.php';
        $content = file_get_contents($routesFile);

        $hasAgentRoute = strpos($content, "'/agent'") !== false;
        $hasApiRoute = strpos($content, "'/api/agent/ask'") !== false;

        echo "<div class='test-result " . (($hasAgentRoute && $hasApiRoute) ? 'test-pass' : 'test-warn') . "'>";
        echo "<div>";
        echo "<h3>" . (($hasAgentRoute && $hasApiRoute) ? "✅" : "⚠️") . " Test 6 — Routes</h3>";

        echo "<p><strong>Routes configurées :</strong></p>";
        echo "<ul>";
        echo "<li>" . ($hasAgentRoute ? "✅" : "❌") . " <code>GET /agent</code></li>";
        echo "<li>" . ($hasApiRoute ? "✅" : "❌") . " <code>POST /api/agent/ask</code></li>";
        echo "</ul>";

        echo "</div>";
        echo "</div>";

        return ['test' => 'routes', 'status' => (($hasAgentRoute && $hasApiRoute) ? 'pass' : 'warn')];
    }
}
?>

