<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;

class ProductService
{
    private \PDO $db;
    private Product $products;
    private Category $categories;
    private StockMovement $movements;

    public function __construct()
    {
        $this->db = Database::connection();
        $this->products = new Product();
        $this->categories = new Category();
        $this->movements = new StockMovement();
    }

    public function validate(array $data, ?int $id = null): array
    {
        $errors = [];
        $code = trim((string) ($data['product_code'] ?? ''));
        $name = trim((string) ($data['product_name'] ?? ''));
        $categoryId = (int) ($data['category_id'] ?? 0);
        $costRaw = $data['cost_price'] ?? null;
        $sellingRaw = $data['selling_price'] ?? null;
        $quantityRaw = $data['quantity'] ?? null;
        $reorderRaw = $data['reorder_level'] ?? null;
        $costPrice = is_numeric($costRaw) ? (float) $costRaw : -1;
        $sellingPrice = is_numeric($sellingRaw) ? (float) $sellingRaw : -1;
        $quantity = filter_var($quantityRaw, FILTER_VALIDATE_INT);
        $reorderLevel = filter_var($reorderRaw, FILTER_VALIDATE_INT);

        if ($code === '') {
            $errors['product_code'] = 'Product code is required.';
        } elseif ($this->products->codeExists($code, $id)) {
            $errors['product_code'] = 'This product code already exists.';
        }

        if ($name === '') {
            $errors['product_name'] = 'Product name is required.';
        }

        if (!$this->categories->find($categoryId)) {
            $errors['category_id'] = 'Select a valid category.';
        }

        if ($costPrice < 0) {
            $errors['cost_price'] = 'Cost price cannot be negative.';
        }
        if ($sellingPrice < 0) {
            $errors['selling_price'] = 'Selling price cannot be negative.';
        }
        if ($quantity === false || $quantity < 0) {
            $errors['quantity'] = 'Quantity must be zero or greater.';
        }
        if ($reorderLevel === false || $reorderLevel < 0) {
            $errors['reorder_level'] = 'Reorder level must be zero or greater.';
        }
        if (!in_array($data['status'] ?? 'active', ['active', 'inactive'], true)) {
            $errors['status'] = 'Invalid product status.';
        }

        return $errors;
    }

    public function payload(array $data): array
    {
        return [
            'product_code' => strtoupper(trim((string) ($data['product_code'] ?? ''))),
            'product_name' => trim((string) ($data['product_name'] ?? '')),
            'category_id' => (int) ($data['category_id'] ?? 0),
            'description' => trim((string) ($data['description'] ?? '')),
            'cost_price' => number_format((float) ($data['cost_price'] ?? 0), 2, '.', ''),
            'selling_price' => number_format((float) ($data['selling_price'] ?? 0), 2, '.', ''),
            'quantity' => (int) ($data['quantity'] ?? 0),
            'reorder_level' => (int) ($data['reorder_level'] ?? setting('low_stock_default', 5)),
            'unit' => trim((string) ($data['unit'] ?? 'pcs')) ?: 'pcs',
            'status' => in_array($data['status'] ?? 'active', ['active', 'inactive'], true) ? $data['status'] : 'active',
        ];
    }

    public function create(array $payload, int $userId): int
    {
        $this->db->beginTransaction();
        try {
            $id = $this->products->create($payload);

            if ($payload['quantity'] > 0) {
                $this->movements->create([
                    'product_id' => $id,
                    'movement_type' => 'opening_stock',
                    'quantity' => $payload['quantity'],
                    'quantity_before' => 0,
                    'quantity_after' => $payload['quantity'],
                    'reference_type' => 'product',
                    'reference_id' => $id,
                    'remarks' => 'Opening stock recorded during product creation.',
                    'user_id' => $userId,
                ]);
            }

            $this->db->commit();
            return $id;
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    public function update(int $id, array $payload, int $userId): void
    {
        $this->db->beginTransaction();
        try {
            $existing = $this->products->find($id);
            if (!$existing) {
                throw new \RuntimeException('Product not found.');
            }

            $before = (int) $existing['quantity'];
            $after = (int) $payload['quantity'];
            $this->products->update($id, $payload);

            if ($before !== $after) {
                $this->movements->create([
                    'product_id' => $id,
                    'movement_type' => $after > $before ? 'adjustment_in' : 'adjustment_out',
                    'quantity' => abs($after - $before),
                    'quantity_before' => $before,
                    'quantity_after' => $after,
                    'reference_type' => 'product',
                    'reference_id' => $id,
                    'remarks' => 'Quantity correction from product edit.',
                    'user_id' => $userId,
                ]);
            }

            $this->db->commit();
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }
}
