<?php

declare(strict_types=1);

namespace App\Models;

class User extends Model
{
    public function findByLogin(string $login): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username OR email = :email LIMIT 1');
        $stmt->execute(['username' => $login, 'email' => $login]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function paginate(string $search, int $page, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = '';

        if ($search !== '') {
            $where = 'WHERE full_name LIKE :search_name OR username LIKE :search_username OR email LIKE :search_email';
            $params['search_name'] = "%{$search}%";
            $params['search_username'] = "%{$search}%";
            $params['search_email'] = "%{$search}%";
        }

        $count = $this->db->prepare("SELECT COUNT(*) FROM users {$where}");
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $stmt = $this->db->prepare("SELECT * FROM users {$where} ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'rows' => $stmt->fetchAll(),
            'total' => $total,
            'pages' => (int) ceil($total / $perPage),
        ];
    }

    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        return $this->existsByField('username', $username, $excludeId);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        if ($email === '') {
            return false;
        }
        return $this->existsByField('email', $email, $excludeId);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (full_name, username, email, password, role, status)
             VALUES (:full_name, :username, :email, :password, :role, :status)'
        );
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users
             SET full_name = :full_name, username = :username, email = :email,
                 role = :role, status = :status, updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public function updateProfile(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users
             SET full_name = :full_name, email = :email, updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public function updatePassword(int $id, string $hash): void
    {
        $stmt = $this->db->prepare('UPDATE users SET password = :password, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        $stmt->execute(['password' => $hash, 'id' => $id]);
    }

    public function updateLastLogin(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function deactivate(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE users SET status = "inactive", updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    private function existsByField(string $field, string $value, ?int $excludeId): bool
    {
        $sql = "SELECT COUNT(*) FROM users WHERE {$field} = :value";
        $params = ['value' => $value];
        if ($excludeId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }
}
