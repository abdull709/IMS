<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\InventoryService;

class InventoryController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();

        $products = new Product();
        $filters = [
            'search' => trim((string) input('search', '')),
            'status' => 'active',
            'stock' => input('stock', ''),
        ];
        $page = max(1, (int) input('page', 1));
        $result = $products->paginate($filters, $page, 12);

        $this->view('inventory/index', [
            'title' => 'Stock Management',
            'products' => $result['rows'],
            'allProducts' => $products->allActive(),
            'filters' => $filters,
            'page' => $page,
            'pages' => $result['pages'],
        ]);
    }

    public function stockIn(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        try {
            (new InventoryService())->stockIn(
                (int) input('product_id'),
                (int) input('quantity'),
                trim((string) input('remarks', 'Stock added manually.')),
                (int) current_user()['id']
            );
            set_flash('success', 'Stock added successfully.');
        } catch (\Throwable $exception) {
            set_flash('danger', $exception->getMessage());
        }

        redirect(url('inventory'));
    }

    public function adjust(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        try {
            (new InventoryService())->adjust(
                (int) input('product_id'),
                (string) input('adjustment_type'),
                (int) input('quantity'),
                trim((string) input('reason', 'Manual stock adjustment.')),
                (int) current_user()['id']
            );
            set_flash('success', 'Stock adjusted successfully.');
        } catch (\Throwable $exception) {
            set_flash('danger', $exception->getMessage());
        }

        redirect(url('inventory'));
    }

    public function movements(): void
    {
        $this->requireLogin();
        $filters = [
            'product_id' => input('product_id', ''),
            'movement_type' => input('movement_type', ''),
            'start_date' => input('start_date', ''),
            'end_date' => input('end_date', ''),
        ];
        $page = max(1, (int) input('page', 1));
        $result = (new StockMovement())->paginate($filters, $page);

        $this->view('inventory/movements', [
            'title' => 'Stock Movements',
            'movements' => $result['rows'],
            'products' => (new Product())->allActive(),
            'filters' => $filters,
            'page' => $page,
            'pages' => $result['pages'],
        ]);
    }

    public function lowStock(): void
    {
        $this->requireLogin();
        $filters = ['stock' => input('stock', 'low'), 'status' => 'active', 'search' => trim((string) input('search', ''))];
        $page = max(1, (int) input('page', 1));
        $result = (new Product())->paginate($filters, $page, 12);

        $this->view('inventory/low-stock', [
            'title' => 'Low Stock',
            'products' => $result['rows'],
            'filters' => $filters,
            'page' => $page,
            'pages' => $result['pages'],
        ]);
    }
}
