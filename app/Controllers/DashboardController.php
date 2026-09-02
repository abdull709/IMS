<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Report;
use App\Models\Sale;
use App\Models\StockMovement;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();

        $reports = new Report();
        $products = new Product();

        $this->view('dashboard/index', [
            'title' => 'Dashboard',
            'stats' => $reports->dashboardStats(),
            'salesChart' => $reports->salesLastDays(7),
            'recentSales' => (new Sale())->recent(6),
            'lowProducts' => $products->paginate(['stock' => 'low', 'status' => 'active'], 1, 6)['rows'],
            'topProducts' => $reports->topProducts(5),
            'recentMovements' => (new StockMovement())->recent(6),
        ]);
    }
}
