<?php

declare(strict_types=1);

namespace App\Models;

class StockMovement extends Model
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO stock_movements
                (product_id, movement_type, quantity, quantity_before, quantity_after, reference_type, reference_id, remarks, user_id)
             VALUES
                (:product_id, :movement_type, :quantity, :quantity_before, :quantity_after, :reference_type, :reference_id, :remarks, :user_id)'
        );
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function paginate(array $filters, int $page, int $perPage = 15): array
    {
        $offset = ($page - 1) * $perPage;
        [$where, $params] = $this->filterSql($filters);

        $count = $this->db->prepare(
            "SELECT COUNT(*)
             FROM stock_movements sm
             JOIN products p ON p.id = sm.product_id
             LEFT JOIN users u ON u.id = sm.user_id
             {$where}"
        );
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT sm.*, p.product_name, p.product_code, u.full_name AS user_name
             FROM stock_movements sm
             JOIN products p ON p.id = sm.product_id
             LEFT JOIN users u ON u.id = sm.user_id
             {$where}
             ORDER BY sm.created_at DESC
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

    public function recent(int $limit = 8): array
    {
        $stmt = $this->db->prepare(
            'SELECT sm.*, p.product_name, p.product_code, u.full_name AS user_name
             FROM stock_movements sm
             JOIN products p ON p.id = sm.product_id
             LEFT JOIN users u ON u.id = sm.user_id
             ORDER BY sm.created_at DESC
             LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function filterSql(array $filters): array
    {
        $parts = [];
        $params = [];

        if (($filters['product_id'] ?? '') !== '') {
            $parts[] = 'sm.product_id = :product_id';
            $params['product_id'] = (int) $filters['product_id'];
        }
        if (($filters['movement_type'] ?? '') !== '') {
            $parts[] = 'sm.movement_type = :movement_type';
            $params['movement_type'] = $filters['movement_type'];
        }
        if (($filters['start_date'] ?? '') !== '') {
            $parts[] = 'DATE(sm.created_at) >= :start_date';
            $params['start_date'] = $filters['start_date'];
        }
        if (($filters['end_date'] ?? '') !== '') {
            $parts[] = 'DATE(sm.created_at) <= :end_date';
            $params['end_date'] = $filters['end_date'];
        }

        return [$parts === [] ? '' : 'WHERE ' . implode(' AND ', $parts), $params];
    }
}
