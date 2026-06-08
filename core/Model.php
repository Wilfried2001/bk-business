<?php
// ============================================================
//  core/Model.php — Classe de base pour tous les modèles
// ============================================================
abstract class Model {

    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

// Méthode __construct : gère   construct. 
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Trouver un enregistrement par son ID
    public function find(int $id): ?array {
        $table = $this->sanitizeIdentifier($this->table);
        $primaryKey = $this->sanitizeIdentifier($this->primaryKey);
        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE {$primaryKey} = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // Récupérer tous les enregistrements
    public function all(string $orderBy = ''): array {
        $table = $this->sanitizeIdentifier($this->table);
        $sql = "SELECT * FROM {$table}";
        if ($orderBy) {
            if (!preg_match('/^[a-zA-Z0-9_.,\s]+$/', $orderBy)) {
                throw new InvalidArgumentException('Invalid ORDER BY clause.');
            }
            $sql .= " ORDER BY {$orderBy}";
        }
        return $this->db->query($sql)->fetchAll();
    }

    private function sanitizeIdentifier(string $name): string {
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
            throw new InvalidArgumentException('Invalid database identifier.');
        }
        return $name;
    }

    // Insérer un enregistrement
    public function create(array $data): int {
        if (empty($data)) {
            throw new InvalidArgumentException('Cannot insert empty data set.');
        }

        $table = $this->sanitizeIdentifier($this->table);
        $columns = array_map([$this, 'sanitizeIdentifier'], array_keys($data));
        $placeholders = ':' . implode(', :', $columns);
        $columns = implode(', ', $columns);

        $stmt = $this->db->prepare("INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})");
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    // Mettre à jour un enregistrement
    public function update(int $id, array $data): bool {
        if (empty($data)) {
            throw new InvalidArgumentException('Cannot update with empty data set.');
        }

        $table = $this->sanitizeIdentifier($this->table);
        $primaryKey = $this->sanitizeIdentifier($this->primaryKey);
        $columns = array_map([$this, 'sanitizeIdentifier'], array_keys($data));
        $set = implode(', ', array_map(fn($k) => "{$k} = :{$k}", $columns));

        $data[$primaryKey] = $id;
        $stmt = $this->db->prepare("UPDATE {$table} SET {$set} WHERE {$primaryKey} = :{$primaryKey}");
        return $stmt->execute($data);
    }

    // Supprimer un enregistrement
    public function delete(int $id): bool {
        $table = $this->sanitizeIdentifier($this->table);
        $primaryKey = $this->sanitizeIdentifier($this->primaryKey);
        $stmt = $this->db->prepare("DELETE FROM {$table} WHERE {$primaryKey} = ?");
        return $stmt->execute([$id]);
    }

    // Requête personnalisée avec paramètres
    public function query(string $sql, array $params = []): array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Requête qui retourne une seule ligne
    public function queryOne(string $sql, array $params = []): ?array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // Exécuter une requête sans retour (INSERT, UPDATE, DELETE complexe)
    public function execute(string $sql, array $params = []): bool {
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    // Démarrer une transaction SQL
    public function beginTransaction(): void  { $this->db->beginTransaction(); }
// Méthode commit : gère commit. 
    public function commit(): void            { if ($this->db->inTransaction()) { $this->db->commit(); } }
// Méthode rollback : gère rollback. 
    public function rollback(): void          { if ($this->db->inTransaction()) { $this->db->rollBack(); } }
}
