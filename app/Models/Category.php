<?php

declare(strict_types=1);

namespace App\Models;

class Category extends Model
{
    public function allActive(): array
    {
        return $this->db->query('SELECT * FROM categories WHERE status = "active" ORDER BY category_name')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function paginate(string $search, int $page, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = '';

        if ($search !== '') {
            $where = 'WHERE c.category_name LIKE :search_name OR c.description LIKE :search_description';
            $params['search_name'] = "%{$search}%";
            $params['search_description'] = "%{$search}%";
        }

        $count = $this->db->prepare("SELECT COUNT(*) FROM categories c {$where}");
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT c.*, COUNT(p.id) AS product_count
             FROM categories c
             LEFT JOIN products p ON p.category_id = c.id
             {$where}
             GROUP BY c.id
             ORDER BY c.created_at DESC
             LIMIT :limit OFFSET :offset"
        );
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

    public function nameExists(string $name, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM categories WHERE category_name = :name';
        $params = ['name' => $name];
        if ($excludeId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO categories (category_name, description, status)
             VALUES (:category_name, :description, :status)'
        );
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE categories
             SET category_name = :category_name, description = :description,
                 status = :status, updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public function productCount(int $id): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE category_id = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn();
    }

    public function deleteOrDeactivate(int $id): string
    {
        if ($this->productCount($id) > 0) {
            $stmt = $this->db->prepare('UPDATE categories SET status = "inactive", updated_at = CURRENT_TIMESTAMP WHERE id = :id');
            $stmt->execute(['id' => $id]);
            return 'deactivated';
        }

        $stmt = $this->db->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return 'deleted';
    }
}
