<?php
/**
 * Tests d'intégration pour TransactionController
 */

class TransactionControllerTest extends TestCase {

    protected TransactionController $controller;

    protected function setUp(): void {
        parent::setUp();
        require_once APP_PATH . '/controllers/TransactionController.php';
        $this->controller = new TransactionController();
    }

    public function testDeduireTypeMouvementReturnsCorrectValues(): void {
        $method = new ReflectionMethod(TransactionController::class, 'deduireTypeMouvement');
        $method->setAccessible(true);

        $this->assertEquals('ENTREE', $method->invoke($this->controller, ['impact_caisse' => 10, 'impact_float' => 0]));
        $this->assertEquals('SORTIE', $method->invoke($this->controller, ['impact_caisse' => -5, 'impact_float' => 0]));
        $this->assertEquals('ENTREE', $method->invoke($this->controller, ['impact_caisse' => 0, 'impact_float' => 20]));
        $this->assertEquals('SORTIE', $method->invoke($this->controller, ['impact_caisse' => 0, 'impact_float' => -15]));
        $this->assertEquals('NEUTRE', $method->invoke($this->controller, ['impact_caisse' => 0, 'impact_float' => 0]));
    }

    public function testIsInternationalServiceDetectsInternationalCategories(): void {
        $method = new ReflectionMethod(TransactionController::class, 'isInternationalService');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($this->controller, ['nom' => 'Western Union', 'categorie' => 'LOCAL']));
        $this->assertTrue($method->invoke($this->controller, ['nom' => 'CASH EXPRESS', 'categorie' => 'OTHER']));
        $this->assertTrue($method->invoke($this->controller, ['nom' => 'My Service', 'categorie' => 'INTERNATIONAL']));
        $this->assertFalse($method->invoke($this->controller, ['nom' => 'Transfert local', 'categorie' => 'NATIONAL']));
    }

    public function testStoreInvalidRequestRedirectsToCreate(): void {
        if (!isset($_SERVER['HTTP_HOST'])) {
            $_SERVER['HTTP_HOST'] = 'localhost';
        }

        $db = self::getTestDatabase();

        $service = $db->query("SELECT id_service FROM service WHERE actif = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        $type = $db->query("SELECT id_type FROM type_operation LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        $user = $db->query("SELECT id_user FROM utilisateur WHERE actif = 1 LIMIT 1")->fetch(PDO::FETCH_ASSOC);

        if (!$service || !$type || !$user) {
            $this->markTestSkipped('La base de test ne contient pas de service actif, type ou utilisateur actif.');
        }

        Auth::login([
            'id_user' => (int)$user['id_user'], 
            'nom' => 'Test Utilisateur',
            'role' => 'AGENT',
        ]);

        $reference = 'TEST_INVALID_' . uniqid();
        $_POST = [
            'id_service' => '',
            'id_type' => '',
            'montant' => '-100',
            'reference' => $reference,
            'note' => 'Note de test',
            'motif_transaction' => '',
            'nom_expediteur' => '',
            'expediteur_identifiant' => '',
            'expediteur_telephone' => '',
            'nom_beneficiaire' => '',
            'beneficiaire_identifiant' => '',
            'beneficiaire_telephone' => '',
        ];

        $controller = new class extends TransactionController {
            public ?string $redirectPath = null;

            protected function redirect(string $path): void {
                $this->redirectPath = $path;
            }

            protected function verifyCsrf(): void {
                // Bypass CSRF dans le contexte de test
            }
        };

        $controller->store();

        $this->assertNotNull($controller->redirectPath, 'La redirection doit être déclenchée pour une requête invalide.');
        $this->assertStringContainsString('transactions/create', $controller->redirectPath);

        $stmt = $db->prepare('SELECT COUNT(*) AS count FROM transaction WHERE reference = ?');
        $stmt->execute([$reference]);
        $this->assertSame(0, (int)$stmt->fetchColumn());
    }
}
