<?php

declare(strict_types=1);

namespace App\Models;

class Sale extends Model
{
    public function paginate(array $filters, int $page, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        [$where, $params] = $this->filterSql($filters);

        $count = $this->db->prepare(
            "SELECT COUNT(*)
             FROM sales s
             JOIN users u ON u.id = s.user_id
             {$where}"
        );
        $count->execute($params);
        $total = (int) $count->fetchColumn();

        $stmt = $this->db->prepare(
            "SELECT s.*, u.full_name AS cashier,
                    COALESCE(SUM(si.quantity), 0) AS item_count
             FROM sales s
             JOIN users u ON u.id = s.user_id
             LEFT JOIN sale_items si ON si.sale_id = s.id
             {$where}
             GROUP BY s.id
             ORDER BY s.sale_date DESC, s.id DESC
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

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.full_name AS cashier, u.username
             FROM sales s
             JOIN users u ON u.id = s.user_id
             WHERE s.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $sale = $stmt->fetch();
        return $sale ?: null;
    }

    public function items(int $saleId): array
    {
        $stmt = $this->db->prepare(
            'SELECT si.*, p.product_name, p.product_code, p.unit
             FROM sale_items si
             JOIN products p ON p.id = si.product_id
             WHERE si.sale_id = :sale_id
             ORDER BY si.id'
        );
        $stmt->execute(['sale_id' => $saleId]);
        return $stmt->fetchAll();
    }

    public function recent(int $limit = 8): array
    {
        $stmt = $this->db->prepare(
            'SELECT s.*, u.full_name AS cashier
             FROM sales s
             JOIN users u ON u.id = s.user_id
             ORDER BY s.sale_date DESC, s.id DESC
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

        if (($filters['search'] ?? '') !== '') {
            $parts[] = 's.invoice_number LIKE :search';
            $params['search'] = '%' . $filters['search'] . '%';
        }
        if (($filters['user_id'] ?? '') !== '') {
            $parts[] = 's.user_id = :user_id';
            $params['user_id'] = (int) $filters['user_id'];
        }
        if (($filters['start_date'] ?? '') !== '') {
            $parts[] = 'DATE(s.sale_date) >= :start_date';
            $params['start_date'] = $filters['start_date'];
        }
        if (($filters['end_date'] ?? '') !== '') {
            $parts[] = 'DATE(s.sale_date) <= :end_date';
            $params['end_date'] = $filters['end_date'];
        }

        return [$parts === [] ? '' : 'WHERE ' . implode(' AND ', $parts), $params];
    }
}
