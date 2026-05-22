<?php
// ============================================================
//  core/Model.php — Classe de base pour tous les modèles
// ============================================================
abstract class Model {

    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Trouver un enregistrement par son ID
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // Récupérer tous les enregistrements
    public function all(string $orderBy = ''): array {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) $sql .= " ORDER BY {$orderBy}";
        return $this->db->query($sql)->fetchAll();
    }

    // Insérer un enregistrement
    public function create(array $data): int {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})");
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    // Mettre à jour un enregistrement
    public function update(int $id, array $data): bool {
        $set = implode(', ', array_map(fn($k) => "{$k} = :{$k}", array_keys($data)));
        $data[$this->primaryKey] = $id;
        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$set} WHERE {$this->primaryKey} = :{$this->primaryKey}");
        return $stmt->execute($data);
    }

    // Supprimer un enregistrement
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
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
    public function commit(): void            { $this->db->commit(); }
    public function rollback(): void          { $this->db->rollBack(); }
}
