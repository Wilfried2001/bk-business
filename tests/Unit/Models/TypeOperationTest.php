<?php
/**
 * Tests pour le modèle TypeOperation
 */

class TypeOperationTest extends TestCase {

    public function testFindByLabelReturnsTypeOperation(): void {
        require_once APP_PATH . '/models/TypeOperation.php';

        $typeModel = new TypeOperation();
        $typeId = $typeModel->getOrCreateAdjustmentType();
        $type = $typeModel->find($typeId);

        $this->assertIsArray($type);
        $this->assertArrayHasKey('libelle', $type);
        $this->assertEquals('AJUSTEMENT', $type['libelle']);
    }

    public function testGetByServiceReturnsArray(): void {
        require_once APP_PATH . '/models/TypeOperation.php';

        $typeModel = new TypeOperation();
        $service = $typeModel->query('SELECT id_service FROM service WHERE actif = 1 LIMIT 1');

        if (empty($service)) {
            $this->markTestSkipped('Aucun service actif en base.');
        }

        $types = $typeModel->getByService((int)$service[0]['id_service']);
        $this->assertIsArray($types);
    }

    public function testGetAllUniqueByLabelDeduplicatesSameLabels(): void {
        require_once APP_PATH . '/models/TypeOperation.php';

        $typeModel = new TypeOperation();
        $label = 'TEST_DUPLICATE_TYPE_' . uniqid();

        try {
            $typeModel->create([
                'libelle' => $label,
                'description' => 'Premier enregistrement de test',
                'impact_float' => 0,
                'impact_caisse' => 0,
            ]);
            $typeModel->create([
                'libelle' => $label,
                'description' => 'Deuxième enregistrement de test',
                'impact_float' => 0,
                'impact_caisse' => 0,
            ]);

            $types = $typeModel->getAllUniqueByLabel('libelle');
            $matchingTypes = array_values(array_filter($types, fn($type) => ($type['libelle'] ?? '') === $label));

            $this->assertCount(1, $matchingTypes);
        } finally {
            $typeModel->execute('DELETE FROM type_operation WHERE libelle = ?', [$label]);
        }
    }
}
