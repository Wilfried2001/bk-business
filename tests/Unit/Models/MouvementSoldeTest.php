<?php
/**
 * Tests pour le modèle MouvementSolde
 */

class MouvementSoldeTest extends TestCase {

    public function testCreateMouvement(): void {
        require_once APP_PATH . '/models/MouvementSolde.php';
        require_once APP_PATH . '/models/Transaction.php';
        require_once APP_PATH . '/models/SoldeService.php';

        $transaction = new Transaction();
        $soldeService = new SoldeService();

        $transactionRow = $transaction->query('SELECT id_transaction FROM transaction LIMIT 1');
        $soldeRow = $soldeService->query('SELECT id_solde FROM solde_service LIMIT 1');

        if (empty($transactionRow) || empty($soldeRow)) {
            $this->markTestSkipped('Aucun transaction ou solde_service disponible pour créer un mouvement.');
        }

        $mouvement = new MouvementSolde();
        $id = $mouvement->createMouvement(
            (int)$transactionRow[0]['id_transaction'], 
            (int)$soldeRow[0]['id_solde'], 
            'CREDIT', 
            1000.0, 
            5000.0, 
            6000.0, 
            'Test mouvement'
        );

        $this->assertGreaterThan(0, $id);
        $this->assertRowExists('mouvement_solde', ['id_mouvement' => $id]);
    }

    public function testGetByTransaction(): void {
        require_once APP_PATH . '/models/MouvementSolde.php';
        require_once APP_PATH . '/models/Transaction.php';

        $transaction = new Transaction();
        $transactionRow = $transaction->query('SELECT id_transaction FROM transaction LIMIT 1');

        if (empty($transactionRow)) {
            $this->markTestSkipped('Aucune transaction disponible pour tester getByTransaction.');
        }

        $mouvement = new MouvementSolde();
        $rows = $mouvement->getByTransaction((int)$transactionRow[0]['id_transaction']);

        $this->assertIsArray($rows);
    }
}
