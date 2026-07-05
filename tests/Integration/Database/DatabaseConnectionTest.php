<?php
/**
 * Tests d'intégration de la base de données
 */

class DatabaseConnectionTest extends TestCase {

    public function testDatabaseConnectionIsAvailable(): void {
        $db = self::getTestDatabase();
        $this->assertInstanceOf(PDO::class, $db);
    }

    public function testMainTablesExist(): void {
        $db = self::getTestDatabase();

        $requiredTables = [
            'utilisateur',
            'service',
            'transaction',
            'solde_service',
            'type_operation',
            'agence',
        ];

        foreach ($requiredTables as $table) {
            $stmt = $db->prepare('SHOW TABLES LIKE ?');
            $stmt->execute([$table]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->assertNotEmpty($result, "La table {$table} doit exister dans la base de données.");
        }
    }
}
