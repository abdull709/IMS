<?php

declare(strict_types=1);

namespace App\Models;

class Report extends Model
{
    public function dashboardStats(): array
    {
        return [
            'total_products' => (int) $this->db->query('SELECT COUNT(*) FROM products WHERE status = "active"')->fetchColumn(),
            'total_categories' => (int) $this->db->query('SELECT COUNT(*) FROM categories')->fetchColumn(),
            'current_stock' => (int) $this->db->query('SELECT COALESCE(SUM(quantity), 0) FROM products WHERE status = "active"')->fetchColumn(),
            'low_stock' => (int) $this->db->query('SELECT COUNT(*) FROM products WHERE status = "active" AND quantity <= reorder_level')->fetchColumn(),
            'today_sales' => (float) $this->db->query('SELECT COALESCE(SUM(total_amount), 0) FROM sales WHERE DATE(sale_date) = CURDATE()')->fetchColumn(),
            'today_transactions' => (int) $this->db->query('SELECT COUNT(*) FROM sales WHERE DATE(sale_date) = CURDATE()')->fetchColumn(),
            'monthly_sales' => (float) $this->db->query('SELECT COALESCE(SUM(total_amount), 0) FROM sales WHERE YEAR(sale_date) = YEAR(CURDATE()) AND MONTH(sale_date) = MONTH(CURDATE())')->fetchColumn(),
        ];
    }

    public function salesLastDays(int $days = 7): array
    {
        $days = max(1, min(31, $days));
        $interval = $days - 1;
        $stmt = $this->db->prepare(
            'SELECT DATE(sale_date) AS sale_day, COALESCE(SUM(total_amount), 0) AS total
             FROM sales
             WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' DAY)
             GROUP BY DATE(sale_date)
             ORDER BY sale_day'
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function topProducts(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.product_name, p.product_code, SUM(si.quantity) AS quantity_sold, SUM(si.subtotal) AS sales_amount
             FROM sale_items si
             JOIN products p ON p.id = si.product_id
             GROUP BY p.id, p.product_name, p.product_code
             ORDER BY quantity_sold DESC
             LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function inventory(array $filters = []): array
    {
        $params = [];
        $where = '';
        if (($filters['search'] ?? '') !== '') {
            $where = 'WHERE p.product_name LIKE :search_name OR p.product_code LIKE :search_code OR c.category_name LIKE :search_category';
            $params['search_name'] = '%' . $filters['search'] . '%';
            $params['search_code'] = '%' . $filters['search'] . '%';
            $params['search_category'] = '%' . $filters['search'] . '%';
        }

        $stmt = $this->db->prepare(
            "SELECT p.*, c.category_name, (p.cost_price * p.quantity) AS stock_value
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             {$where}
             ORDER BY c.category_name, p.product_name"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function lowStock(): array
    {
        return $this->db->query(
            'SELECT p.*, c.category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE p.status = "active" AND p.quantity <= p.reorder_level
             ORDER BY p.quantity ASC, p.product_name'
        )->fetchAll();
    }

    public function sales(array $filters): array
    {
        [$where, $params] = $this->dateWhere('s.sale_date', $filters);
        $stmt = $this->db->prepare(
            "SELECT DATE(s.sale_date) AS sale_day, s.invoice_number, u.full_name AS user_name,
                    COALESCE(SUM(si.quantity), 0) AS item_count, s.total_amount
             FROM sales s
             JOIN users u ON u.id = s.user_id
             LEFT JOIN sale_items si ON si.sale_id = s.id
             {$where}
             GROUP BY s.id, s.sale_date, s.invoice_number, u.full_name, s.total_amount
             ORDER BY s.sale_date DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function salesSummary(array $filters): array
    {
        [$where, $params] = $this->dateWhere('sale_date', $filters);
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) AS transactions,
                    COALESCE(SUM(total_amount), 0) AS total_sales,
                    COALESCE(AVG(total_amount), 0) AS average_transaction
             FROM sales
             {$where}"
        );
        $stmt->execute($params);
        return $stmt->fetch();
    }

    public function productSales(array $filters): array
    {
        [$where, $params] = $this->dateWhere('s.sale_date', $filters, true);
        $stmt = $this->db->prepare(
            "SELECT p.product_name, p.product_code, c.category_name,
                    SUM(si.quantity) AS quantity_sold, SUM(si.subtotal) AS sales_amount
             FROM sale_items si
             JOIN sales s ON s.id = si.sale_id
             JOIN products p ON p.id = si.product_id
             LEFT JOIN categories c ON c.id = p.category_id
             {$where}
             GROUP BY p.id, p.product_name, p.product_code, c.category_name
             ORDER BY quantity_sold DESC, sales_amount DESC"
        );
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    private function dateWhere(string $column, array $filters, bool $prefixWhere = false): array
    {
        $parts = [];
        $params = [];

        if (($filters['start_date'] ?? '') !== '') {
            $parts[] = "DATE({$column}) >= :start_date";
            $params['start_date'] = $filters['start_date'];
        }
        if (($filters['end_date'] ?? '') !== '') {
            $parts[] = "DATE({$column}) <= :end_date";
            $params['end_date'] = $filters['end_date'];
        }

        return [$parts === [] ? '' : 'WHERE ' . implode(' AND ', $parts), $params];
    }
}
