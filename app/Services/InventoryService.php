<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class InventoryService
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function stockIn(int $productId, int $quantity, string $remarks, int $userId): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity to add must be greater than zero.');
        }

        $this->changeStock($productId, 'stock_in', $quantity, $remarks, $userId);
    }

    public function adjust(int $productId, string $type, int $quantity, string $reason, int $userId): void
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Adjustment quantity must be greater than zero.');
        }
        if (!in_array($type, ['increase', 'decrease'], true)) {
            throw new \InvalidArgumentException('Invalid adjustment type.');
        }

        $movementType = $type === 'increase' ? 'adjustment_in' : 'adjustment_out';
        $signedQuantity = $type === 'increase' ? $quantity : -$quantity;
        $this->changeStock($productId, $movementType, $signedQuantity, $reason, $userId);
    }

    private function changeStock(int $productId, string $movementType, int $signedQuantity, string $remarks, int $userId): void
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $productId]);
            $product = $stmt->fetch();

            if (!$product) {
                throw new \RuntimeException('Product not found.');
            }
            if ($product['status'] !== 'active') {
                throw new \RuntimeException('Inactive products cannot be adjusted. Activate the product before changing stock.');
            }

            $before = (int) $product['quantity'];
            $after = $before + $signedQuantity;
            if ($after < 0) {
                throw new \RuntimeException("Insufficient stock. Only {$before} units are currently available.");
            }

            $update = $this->db->prepare('UPDATE products SET quantity = :quantity, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
            $update->execute(['quantity' => $after, 'id' => $productId]);

            $movement = $this->db->prepare(
                'INSERT INTO stock_movements
                    (product_id, movement_type, quantity, quantity_before, quantity_after, reference_type, reference_id, remarks, user_id)
                 VALUES
                    (:product_id, :movement_type, :quantity, :quantity_before, :quantity_after, :reference_type, :reference_id, :remarks, :user_id)'
            );
            $movement->execute([
                'product_id' => $productId,
                'movement_type' => $movementType,
                'quantity' => abs($signedQuantity),
                'quantity_before' => $before,
                'quantity_after' => $after,
                'reference_type' => 'manual',
                'reference_id' => null,
                'remarks' => $remarks,
                'user_id' => $userId,
            ]);

            $this->db->commit();
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }
}
