<?php

declare(strict_types=1);

namespace App\Models;

class Product extends Model
{
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, c.category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function allActive(): array
    {
        return $this->db->query(
            'SELECT p.*, c.category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.status = "active"
             ORDER BY p.product_name'
        )->fetchAll();
    }

    public function searchActive(string $term, int $limit = 10): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.id, p.product_code, p.product_name, p.selling_price, p.quantity, p.reorder_level, p.unit, c.category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.status = "active"
               AND (p.product_name LIKE :term_name OR p.product_code LIKE :term_code OR c.category_name LIKE :term_category)
             ORDER BY p.product_name
             LIMIT :limit'
        );
        $stmt->bindValue('term_name', "%{$term}%");
        $stmt->bindValue('term_code', "%{$term}%");
        $stmt->bindValue('term_category', "%{$term}%");
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function paginate(array $filters, int $page, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        [$where, $params] = $this->filterSql($filters);

        $count = $this->db->prepare(
            "SELECT COUNT(*)
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             {$where}"
        );
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT p.*, c.category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             {$where}
             ORDER BY p.created_at DESC
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

    public function codeExists(string $code, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM products WHERE product_code = :code';
        $params = ['code' => $code];
        if ($excludeId !== null) {
            $sql .= ' AND id <> :id';
            $params['id'] = $excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function generateCode(): string
    {
        $last = $this->db->query('SELECT id FROM products ORDER BY id DESC LIMIT 1')->fetchColumn();
        return 'PRD-' . str_pad((string) ((int) $last + 1), 4, '0', STR_PAD_LEFT);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO products
                (product_code, product_name, category_id, description, cost_price, selling_price, quantity, reorder_level, unit, status)
             VALUES
                (:product_code, :product_name, :category_id, :description, :cost_price, :selling_price, :quantity, :reorder_level, :unit, :status)'
        );
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE products
             SET product_code = :product_code, product_name = :product_name, category_id = :category_id,
                 description = :description, cost_price = :cost_price, selling_price = :selling_price,
                 quantity = :quantity, reorder_level = :reorder_level, unit = :unit,
                 status = :status, updated_at = CURRENT_TIMESTAMP
             WHERE id = :id'
        );
        $data['id'] = $id;
        $stmt->execute($data);
    }

    public function usedInSales(int $id): bool
    {
        $stmt = $this->db->prepare(
            'SELECT
                (SELECT COUNT(*) FROM sale_items WHERE product_id = :sale_item_product_id) +
                (SELECT COUNT(*) FROM stock_movements WHERE product_id = :movement_product_id) AS usage_count'
        );
        $stmt->execute(['sale_item_product_id' => $id, 'movement_product_id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function deleteOrDeactivate(int $id): string
    {
        if ($this->usedInSales($id)) {
            $stmt = $this->db->prepare('UPDATE products SET status = "inactive", updated_at = CURRENT_TIMESTAMP WHERE id = :id');
            $stmt->execute(['id' => $id]);
            return 'deactivated';
        }

        $stmt = $this->db->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return 'deleted';
    }

    public function updateQuantity(int $id, int $quantity): void
    {
        $stmt = $this->db->prepare('UPDATE products SET quantity = :quantity, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        $stmt->execute(['quantity' => $quantity, 'id' => $id]);
    }

    private function filterSql(array $filters): array
    {
        $parts = [];
        $params = [];

        if (($filters['search'] ?? '') !== '') {
            $parts[] = '(p.product_name LIKE :search_name OR p.product_code LIKE :search_code OR c.category_name LIKE :search_category)';
            $params['search_name'] = '%' . $filters['search'] . '%';
            $params['search_code'] = '%' . $filters['search'] . '%';
            $params['search_category'] = '%' . $filters['search'] . '%';
        }
        if (($filters['category_id'] ?? '') !== '') {
            $parts[] = 'p.category_id = :category_id';
            $params['category_id'] = (int) $filters['category_id'];
        }
        if (($filters['status'] ?? '') !== '') {
            $parts[] = 'p.status = :status';
            $params['status'] = $filters['status'];
        }
        if (($filters['stock'] ?? '') === 'low') {
            $parts[] = 'p.quantity <= p.reorder_level';
        }
        if (($filters['stock'] ?? '') === 'out') {
            $parts[] = 'p.quantity = 0';
        }

        return [$parts === [] ? '' : 'WHERE ' . implode(' AND ', $parts), $params];
    }
}
