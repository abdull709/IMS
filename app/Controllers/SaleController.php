<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use App\Services\SalesService;

class SaleController extends Controller
{
    public function index(): void
    {
        $this->requireLogin();
        $filters = [
            'search' => trim((string) input('search', '')),
            'user_id' => input('user_id', ''),
            'start_date' => input('start_date', ''),
            'end_date' => input('end_date', ''),
        ];
        $page = max(1, (int) input('page', 1));
        $result = (new Sale())->paginate($filters, $page);

        $this->view('sales/index', [
            'title' => 'Sales History',
            'sales' => $result['rows'],
            'users' => is_admin() ? (new User())->paginate('', 1, 100)['rows'] : [],
            'filters' => $filters,
            'page' => $page,
            'pages' => $result['pages'],
        ]);
    }

    public function create(): void
    {
        $this->requireLogin();
        $this->view('sales/create', [
            'title' => 'New Sale',
            'products' => (new Product())->allActive(),
        ]);
    }

    public function store(): void
    {
        $this->requireLogin();
        $this->validateCsrf();

        $itemsJson = (string) input('items_json', '[]');
        $items = json_decode($itemsJson, true);
        if (!is_array($items)) {
            $items = [];
        }

        try {
            $saleId = (new SalesService())->createSale(
                $items,
                (float) input('discount', 0),
                (float) input('amount_paid', 0),
                trim((string) input('notes', '')),
                (int) current_user()['id']
            );
            set_flash('success', 'Sale recorded successfully.');
            redirect(url('sales/view', ['id' => $saleId]));
        } catch (\Throwable $exception) {
            set_flash('danger', $exception->getMessage());
            redirect(url('sales/create'));
        }
    }

    public function view(): void
    {
        $this->requireLogin();
        $id = (int) input('id');
        $saleModel = new Sale();
        $sale = $saleModel->find($id);
        if (!$sale) {
            set_flash('danger', 'Sale not found.');
            redirect(url('sales'));
        }

        $this->view('sales/view', [
            'title' => 'Sale Receipt',
            'sale' => $sale,
            'items' => $saleModel->items($id),
        ]);
    }

    public function searchProducts(): void
    {
        $this->requireLogin();
        $term = trim((string) input('q', ''));
        $results = $term === '' ? [] : (new Product())->searchActive($term, 12);
        $this->json(['products' => $results]);
    }
}
