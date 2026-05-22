<?php
class Utilisateur extends Model {
    protected string $table      = 'utilisateur';
    protected string $primaryKey = 'id_user';

    public function findByEmail(string $email): ?array {
        return $this->queryOne("SELECT * FROM utilisateur WHERE email = ?", [$email]);
    }

    public function existsByEmail(string $email, int $excludeId = 0): bool {
        if ($excludeId > 0) {
            $user = $this->queryOne("SELECT id_user FROM utilisateur WHERE email = ? AND id_user != ?", [$email, $excludeId]);
        } else {
            $user = $this->queryOne("SELECT id_user FROM utilisateur WHERE email = ?", [$email]);
        }
        return !empty($user);
    }

    public function authenticate(string $email, string $password): ?array {
        $user = $this->findByEmail($email);
        if ($user && password_verify($password, $user['mot_de_passe']) && $user['actif']) {
            return $user;
        }
        return null;
    }

    public function createUser(array $data): int {
        $data['mot_de_passe'] = password_hash($data['mot_de_passe'], PASSWORD_BCRYPT);
        return $this->create($data);
    }
}
