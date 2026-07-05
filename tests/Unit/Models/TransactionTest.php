<?php
/**
 * Tests pour le modèle Transaction
 * Teste les opérations critiques de création et lecture
 */

class TransactionTest extends TestCase {

    /**
     * Teste la création d'une transaction valide
     */
    public function testCreerTransactionValide(): void {
        require_once APP_PATH . '/models/Transaction.php';
        require_once APP_PATH . '/models/Service.php';

        $transaction = new Transaction();
        $service = new Service();

        // Vérifier qu'au moins un service existe
        $services = $service->query("SELECT id_service FROM service WHERE actif = 1 LIMIT 1");
        if (empty($services)) {
            $this->markTestSkipped('Aucun service actif en base de test');
        }

        $idService = (int)$services[0]['id_service'];

        // Créer une transaction
        $type = $transaction->query("SELECT id_type FROM type_operation LIMIT 1");
        $user = $transaction->query("SELECT id_user FROM utilisateur WHERE actif = 1 LIMIT 1");

        if (empty($type) || empty($user)) {
            $this->markTestSkipped('Les références de type ou d\'utilisateur manquent en base de test.');
        }

        $idType = (int)$type[0]['id_type'];
        $idUser = (int)$user[0]['id_user'];

        $id = $transaction->create([
            'id_service' => $idService,
            'id_type' => $idType,
            'id_user' => $idUser,
            'montant' => 50000,
            'reference' => 'TEST_' . time(),
            'statut' => 'VALIDEE',
            'nom_expediteur' => 'Test Sender',
            'nom_benefis' => 'Test Beneficiary',
        ]);

        $this->assertGreaterThan(0, $id, 'La transaction devrait avoir un ID');
        $this->assertRowExists('transaction', ['id_transaction' => $id]);
    }

    /**
     * Teste la récupération d'une transaction avec détails
     */
    public function testRecupererTransactionAvecDetails(): void {
        require_once APP_PATH . '/models/Transaction.php';

        $transaction = new Transaction();
        
        // Récupérer les 5 dernières transactions
        $transactions = $transaction->getAllWithDetails(['limit' => 5]);

        // Si des transactions existent, vérifier la structure
        if (!empty($transactions)) {
            $this->assertIsArray($transactions[0]);
            $this->assertArrayHasKey('id_transaction', $transactions[0]);
            $this->assertArrayHasKey('nom_service', $transactions[0]);
            $this->assertArrayHasKey('montant', $transactions[0]);
        }

        $this->assertIsArray($transactions);
    }

    /**
     * Teste le filtre par statut
     */
    public function testFiltrerTransactionParStatut(): void {
        require_once APP_PATH . '/models/Transaction.php';

        $transaction = new Transaction();
        $transactions = $transaction->getAllWithDetails([
            'statut' => 'VALIDEE',
            'limit' => 10
        ]);

        // Vérifier que toutes les transactions retournées sont validées
        foreach ($transactions as $tx) {
            $this->assertEquals('VALIDEE', $tx['statut']);
        }
    }

    /**
     * Teste le calcul des services les plus utilisés
     */
    public function testGetTopServicesByUsage(): void {
        require_once APP_PATH . '/models/Transaction.php';

        $transaction = new Transaction();
        $topServices = $transaction->getTopServicesByUsage(5);

        $this->assertIsArray($topServices);
        $this->assertLessThanOrEqual(5, count($topServices));

        if (!empty($topServices)) {
            $this->assertArrayHasKey('nom_service', $topServices[0]);
            $this->assertArrayHasKey('total_transactions', $topServices[0]);
        }
    }

    /**
     * Teste la recherche par référence
     */
    public function testRechercherParReference(): void {
        require_once APP_PATH . '/models/Transaction.php';

        $transaction = new Transaction();
        
        // Créer une transaction de test avec une référence unique
        $ref = 'SEARCH_TEST_' . uniqid();
        $id = $transaction->create([
            'id_service' => 1,
            'id_type' => 1,
            'id_user' => 1,
            'montant' => 25000,
            'reference' => $ref,
            'statut' => 'VALIDEE',
            'nom_expediteur' => 'Test',
            'nom_benefis' => 'Test',
        ]);

        // Rechercher par référence
        $resultats = $transaction->getAllWithDetails(['search' => $ref]);

        $this->assertNotEmpty($resultats, "La recherche devrait trouver la transaction");
        $this->assertEquals($ref, $resultats[0]['reference']);
    }
}
