<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Report;
use App\Models\StockMovement;

class ReportController extends Controller
{
    private Report $reports;

    public function __construct()
    {
        $this->reports = new Report();
    }

    public function index(): void
    {
        $this->requireAdmin();
        $this->view('reports/index', ['title' => 'Reports']);
    }

    public function inventory(): void
    {
        $this->requireAdmin();
        $filters = ['search' => trim((string) input('search', ''))];
        $rows = $this->reports->inventory($filters);
        $this->view('reports/inventory', ['title' => 'Inventory Report', 'rows' => $rows, 'filters' => $filters]);
    }

    public function sales(): void
    {
        $this->requireAdmin();
        $filters = ['start_date' => input('start_date', ''), 'end_date' => input('end_date', '')];
        $this->view('reports/sales', [
            'title' => 'Sales Report',
            'rows' => $this->reports->sales($filters),
            'summary' => $this->reports->salesSummary($filters),
            'filters' => $filters,
        ]);
    }

    public function lowStock(): void
    {
        $this->requireAdmin();
        $this->view('reports/low-stock', ['title' => 'Low Stock Report', 'rows' => $this->reports->lowStock()]);
    }

    public function stockMovements(): void
    {
        $this->requireAdmin();
        $filters = [
            'product_id' => input('product_id', ''),
            'movement_type' => input('movement_type', ''),
            'start_date' => input('start_date', ''),
            'end_date' => input('end_date', ''),
        ];
        $result = (new StockMovement())->paginate($filters, 1, 500);
        $this->view('reports/stock-movements', [
            'title' => 'Stock Movement Report',
            'rows' => $result['rows'],
            'products' => (new Product())->allActive(),
            'filters' => $filters,
        ]);
    }

    public function productSales(): void
    {
        $this->requireAdmin();
        $filters = ['start_date' => input('start_date', ''), 'end_date' => input('end_date', '')];
        $this->view('reports/product-sales', [
            'title' => 'Product Sales Report',
            'rows' => $this->reports->productSales($filters),
            'filters' => $filters,
        ]);
    }
}
