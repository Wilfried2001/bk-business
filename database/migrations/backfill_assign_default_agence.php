<?php
// Script idempotent de backfill : assigne une agence "Default (migrated)"
// Usage: php backfill_assign_default_agence.php

require __DIR__ . '/../../tests/bootstrap.php';

try {
    $db = TestCase::getTestDatabase();

    // Vérifier ou créer agence par défaut
    $stmt = $db->prepare("SELECT id_agence FROM agence WHERE nom = ? LIMIT 1");
    $defaultName = 'Default Agency (migrated)';
    $stmt->execute([$defaultName]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && !empty($row['id_agence'])) {
        $defaultId = (int)$row['id_agence'];
        echo "Default agency exists id={$defaultId}\n";
    } else {
        $stmt = $db->prepare("INSERT INTO agence (nom, code, adresse, ville, actif, created_at) VALUES (?, ?, ?, ?, 1, NOW())");
        $stmt->execute([$defaultName, 'MIGR_DEFAULT', 'Migrated', 'N/A']);
        $defaultId = (int)$db->lastInsertId();
        echo "Created default agency id={$defaultId}\n";
    }

    $tables = [
        'transaction' => 'id_agence',
        'solde_service' => 'id_agence',
        'mouvement_solde' => 'id_agence',
        'alerte_solde' => 'id_agence',
        'commission_transaction' => 'id_agence',
    ];

    foreach ($tables as $table => $col) {
        // Vérifier si la colonne existe
        $check = $db->prepare("SHOW COLUMNS FROM {$table} LIKE ?");
        $check->execute([$col]);
        if ($check->fetch()) {
            $count = $db->prepare("SELECT COUNT(*) FROM {$table} WHERE {$col} IS NULL");
            $count->execute();
            $n = (int)$count->fetchColumn();
            if ($n > 0) {
                echo "Updating {$n} rows in {$table} -> setting {$col}={$defaultId}\n";
                $upd = $db->prepare("UPDATE {$table} SET {$col} = ? WHERE {$col} IS NULL");
                $upd->execute([$defaultId]);
            } else {
                echo "No rows to update in {$table}\n";
            }
        } else {
            echo "Column {$col} not found on {$table}, skipping.\n";
        }
    }

    echo "Backfill completed.\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    exit(1);
}
