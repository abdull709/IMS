<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class SalesService
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function createSale(array $items, float $discount, float $amountPaid, string $notes, int $userId): int
    {
        if ($items === []) {
            throw new \InvalidArgumentException('Sale must contain at least one product.');
        }
        if ($discount < 0) {
            throw new \InvalidArgumentException('Discount cannot be negative.');
        }
        if ($amountPaid < 0) {
            throw new \InvalidArgumentException('Amount paid cannot be negative.');
        }

        $this->db->beginTransaction();

        try {
            $invoiceNumber = $this->nextInvoiceNumber();
            $validated = [];
            $subtotal = 0.0;

            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);

                if ($productId <= 0 || $quantity <= 0) {
                    throw new \InvalidArgumentException('Each sale item must have a valid product and quantity.');
                }

                $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id FOR UPDATE');
                $stmt->execute(['id' => $productId]);
                $product = $stmt->fetch();

                if (!$product) {
                    throw new \RuntimeException('Selected product no longer exists.');
                }
                if ($product['status'] !== 'active') {
                    throw new \RuntimeException($product['product_name'] . ' is inactive and cannot be sold.');
                }

                $before = (int) $product['quantity'];
                if ($quantity > $before) {
                    throw new \RuntimeException("Insufficient stock. Only {$before} units are currently available for {$product['product_name']}.");
                }

                $unitPrice = (float) $product['selling_price'];
                $lineSubtotal = $unitPrice * $quantity;
                $subtotal += $lineSubtotal;
                $validated[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $lineSubtotal,
                    'quantity_before' => $before,
                    'quantity_after' => $before - $quantity,
                ];
            }

            if ($discount > $subtotal) {
                throw new \InvalidArgumentException('Discount cannot exceed subtotal.');
            }

            $total = $subtotal - $discount;
            $balance = max($total - $amountPaid, 0);
            $paymentStatus = $balance <= 0 ? 'paid' : ($amountPaid > 0 ? 'partial' : 'unpaid');

            $sale = $this->db->prepare(
                'INSERT INTO sales
                    (invoice_number, user_id, sale_date, subtotal, discount, total_amount, amount_paid, balance, payment_status, notes)
                 VALUES
                    (:invoice_number, :user_id, CURRENT_TIMESTAMP, :subtotal, :discount, :total_amount, :amount_paid, :balance, :payment_status, :notes)'
            );
            $sale->execute([
                'invoice_number' => $invoiceNumber,
                'user_id' => $userId,
                'subtotal' => number_format($subtotal, 2, '.', ''),
                'discount' => number_format($discount, 2, '.', ''),
                'total_amount' => number_format($total, 2, '.', ''),
                'amount_paid' => number_format($amountPaid, 2, '.', ''),
                'balance' => number_format($balance, 2, '.', ''),
                'payment_status' => $paymentStatus,
                'notes' => $notes,
            ]);
            $saleId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare(
                'INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal)
                 VALUES (:sale_id, :product_id, :quantity, :unit_price, :subtotal)'
            );
            $stockStmt = $this->db->prepare(
                'UPDATE products
                 SET quantity = quantity - :quantity_deduct, updated_at = CURRENT_TIMESTAMP
                 WHERE id = :product_id AND quantity >= :quantity_check'
            );
            $movementStmt = $this->db->prepare(
                'INSERT INTO stock_movements
                    (product_id, movement_type, quantity, quantity_before, quantity_after, reference_type, reference_id, remarks, user_id)
                 VALUES
                    (:product_id, "sale", :quantity, :quantity_before, :quantity_after, "sale", :reference_id, :remarks, :user_id)'
            );

            foreach ($validated as $line) {
                $product = $line['product'];
                $itemStmt->execute([
                    'sale_id' => $saleId,
                    'product_id' => $product['id'],
                    'quantity' => $line['quantity'],
                    'unit_price' => number_format($line['unit_price'], 2, '.', ''),
                    'subtotal' => number_format($line['subtotal'], 2, '.', ''),
                ]);

                $stockStmt->execute([
                    'quantity_deduct' => $line['quantity'],
                    'quantity_check' => $line['quantity'],
                    'product_id' => $product['id'],
                ]);

                if ($stockStmt->rowCount() !== 1) {
                    throw new \RuntimeException('Stock changed while processing the sale. Please retry.');
                }

                $movementStmt->execute([
                    'product_id' => $product['id'],
                    'quantity' => $line['quantity'],
                    'quantity_before' => $line['quantity_before'],
                    'quantity_after' => $line['quantity_after'],
                    'reference_id' => $saleId,
                    'remarks' => 'Sale recorded on invoice ' . $invoiceNumber,
                    'user_id' => $userId,
                ]);
            }

            $this->db->commit();
            return $saleId;
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    private function nextInvoiceNumber(): string
    {
        $year = date('Y');
        $stmt = $this->db->prepare('SELECT invoice_number FROM sales WHERE invoice_number LIKE :prefix ORDER BY id DESC LIMIT 1');
        $stmt->execute(['prefix' => "INV-{$year}-%"]);
        $last = (string) ($stmt->fetchColumn() ?: '');
        $next = 1;

        if (preg_match('/^INV-' . $year . '-(\d+)$/', $last, $matches)) {
            $next = (int) $matches[1] + 1;
        }

        return 'INV-' . $year . '-' . str_pad((string) $next, 6, '0', STR_PAD_LEFT);
    }
}
