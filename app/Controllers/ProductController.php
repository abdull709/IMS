<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;

class ProductController extends Controller
{
    private Product $products;
    private Category $categories;
    private ProductService $service;

    public function __construct()
    {
        $this->products = new Product();
        $this->categories = new Category();
        $this->service = new ProductService();
    }

    public function index(): void
    {
        $this->requireLogin();
        $filters = [
            'search' => trim((string) input('search', '')),
            'category_id' => input('category_id', ''),
            'status' => input('status', ''),
            'stock' => input('stock', ''),
        ];
        $page = max(1, (int) input('page', 1));
        $result = $this->products->paginate($filters, $page);

        $this->view('products/index', [
            'title' => 'Products',
            'products' => $result['rows'],
            'categories' => $this->categories->allActive(),
            'filters' => $filters,
            'page' => $page,
            'pages' => $result['pages'],
        ]);
    }

    public function create(): void
    {
        $this->requireAdmin();
        $this->view('products/form', [
            'title' => 'Add Product',
            'product' => ['product_code' => $this->products->generateCode()],
            'categories' => $this->categories->allActive(),
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $errors = $this->service->validate($_POST);

        if ($errors !== []) {
            set_old($_POST);
            $this->view('products/form', ['title' => 'Add Product', 'product' => $_POST, 'categories' => $this->categories->allActive(), 'errors' => $errors]);
            return;
        }

        $payload = $this->service->payload($_POST);
        $this->service->create($payload, (int) current_user()['id']);
        clear_old();
        set_flash('success', 'Product created successfully.');
        redirect(url('products'));
    }

    public function view(): void
    {
        $this->requireLogin();
        $product = $this->products->find((int) input('id'));
        if (!$product) {
            set_flash('danger', 'Product not found.');
            redirect(url('products'));
        }

        $this->view('products/view', ['title' => 'Product Details', 'product' => $product]);
    }

    public function edit(): void
    {
        $this->requireAdmin();
        $product = $this->products->find((int) input('id'));
        if (!$product) {
            set_flash('danger', 'Product not found.');
            redirect(url('products'));
        }

        $this->view('products/form', [
            'title' => 'Edit Product',
            'product' => $product,
            'categories' => $this->categories->allActive(),
            'errors' => [],
        ]);
    }

    public function update(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $id = (int) input('id');
        $product = $this->products->find($id);
        if (!$product) {
            set_flash('danger', 'Product not found.');
            redirect(url('products'));
        }

        $errors = $this->service->validate($_POST, $id);
        if ($errors !== []) {
            set_old($_POST);
            $this->view('products/form', ['title' => 'Edit Product', 'product' => $product, 'categories' => $this->categories->allActive(), 'errors' => $errors]);
            return;
        }

        $payload = $this->service->payload($_POST);
        $this->service->update($id, $payload, (int) current_user()['id']);
        clear_old();
        set_flash('success', 'Product updated successfully.');
        redirect(url('products'));
    }

    public function delete(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $result = $this->products->deleteOrDeactivate((int) input('id'));
        set_flash('success', $result === 'deleted' ? 'Product deleted safely.' : 'Product has sales history and was deactivated.');
        redirect(url('products'));
    }
}
