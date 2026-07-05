<?php
/**
 * Tests pour le modèle SoldeService
 * Teste les opérations critiques sur les soldes
 */

class SoldeServiceTest extends TestCase {

    /**
     * Teste la récupération du solde d'un service
     */
    public function testGetSoldeService(): void {
        require_once APP_PATH . '/models/SoldeService.php';

        $soldeService = new SoldeService();
        $solde = $soldeService->getSolde(1, 'FLOAT');

        if ($solde) {
            $this->assertIsArray($solde);
            $this->assertArrayHasKey('montant_actuel', $solde);
            $this->assertArrayHasKey('type_solde', $solde);
            $this->assertEquals('FLOAT', $solde['type_solde']);
        }
    }

    /**
     * Teste la mise à jour du solde (crédit)
     */
    public function testMettreAJourSoldeCredit(): void {
        require_once APP_PATH . '/models/SoldeService.php';

        $soldeService = new SoldeService();
        
        // Obtenir un solde existant
        $solde = $soldeService->getSolde(1, 'FLOAT');
        if (!$solde) {
            $this->markTestSkipped('Aucun solde FLOAT pour le service 1');
        }

        $idSolde = (int)$solde['id_solde'];
        $soldeAvantCredit = (float)$solde['montant_actuel'];

        // Effectuer un crédit de 10000
        $variation = 10000.00;
        $resultat = $soldeService->mettreAJour($idSolde, $variation, 'CREDIT');

        // Vérifier que le solde a augmenté
        $this->assertEquals($soldeAvantCredit, $resultat['solde_avant']);
        $this->assertEquals($soldeAvantCredit + $variation, $resultat['solde_apres']);

        // Vérifier en base
        $soldeApres = $soldeService->find($idSolde);
        $this->assertEquals($soldeAvantCredit + $variation, (float)$soldeApres['montant_actuel']);
    }

    /**
     * Teste la mise à jour du solde (débit)
     */
    public function testMettreAJourSoldeDebit(): void {
        require_once APP_PATH . '/models/SoldeService.php';

        $soldeService = new SoldeService();
        
        $solde = $soldeService->getSolde(1, 'CAISSE');
        if (!$solde) {
            $this->markTestSkipped('Aucun solde CAISSE pour le service 1');
        }

        $idSolde = (int)$solde['id_solde'];
        $soldeAvantDebit = (float)$solde['montant_actuel'];

        // Effectuer un débit de 5000
        $variation = 5000.00;
        $resultat = $soldeService->mettreAJour($idSolde, $variation, 'DEBIT');

        // Vérifier que le solde a diminué
        $this->assertEquals($soldeAvantDebit, $resultat['solde_avant']);
        $this->assertEquals($soldeAvantDebit - $variation, $resultat['solde_apres']);
    }

    /**
     * Teste la récupération de tous les soldes avec seuils
     */
    public function testGetAllAvecSeuils(): void {
        require_once APP_PATH . '/models/SoldeService.php';

        $soldeService = new SoldeService();
        $soldes = $soldeService->getAllAvecSeuils();

        $this->assertIsArray($soldes);
        
        if (!empty($soldes)) {
            $this->assertArrayHasKey('montant_actuel', $soldes[0]);
            $this->assertArrayHasKey('valeur_seuil', $soldes[0]);
            $this->assertArrayHasKey('en_alerte', $soldes[0]);
        }
    }

    /**
     * Teste le calcul du pourcentage de disponibilité
     */
    public function testGetDisponibilitePourcentage(): void {
        require_once APP_PATH . '/models/SoldeService.php';

        $soldeService = new SoldeService();
        $pourcentage = $soldeService->getDisponibilitePourcentage();

        $this->assertIsInt($pourcentage);
        $this->assertGreaterThanOrEqual(0, $pourcentage);
        $this->assertLessThanOrEqual(100, $pourcentage);
    }

    /**
     * Teste l'alerte automatique quand montant < seuil
     */
    public function testDetectionAlerte(): void {
        require_once APP_PATH . '/models/SoldeService.php';

        $soldeService = new SoldeService();
        $soldes = $soldeService->getAllAvecSeuils();

        $alertes = array_filter($soldes, fn($s) => (int)$s['en_alerte'] === 1);

        // Vérifier que chaque alerte a bien un montant inférieur au seuil
        foreach ($alertes as $alerte) {
            $this->assertLessThan(
                (float)$alerte['valeur_seuil'],
                (float)$alerte['montant_actuel'],
                "Si en_alerte = 1, le montant devrait être < seuil"
            );
        }
    }

    /**
     * Teste l'exception quand solde introuvable
     */
    public function testMettreAJourSoldeIntrouvable(): void {
        require_once APP_PATH . '/models/SoldeService.php';

        $soldeService = new SoldeService();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Solde introuvable');

        // Essayer de mettre à jour un solde inexistant
        $soldeService->mettreAJour(999999, 1000, 'CREDIT');
    }
}
