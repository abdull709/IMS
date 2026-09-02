<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    private Category $categories;
    private CategoryService $service;

    public function __construct()
    {
        $this->categories = new Category();
        $this->service = new CategoryService();
    }

    public function index(): void
    {
        $this->requireAdmin();
        $search = trim((string) input('search', ''));
        $page = max(1, (int) input('page', 1));
        $result = $this->categories->paginate($search, $page);

        $this->view('categories/index', [
            'title' => 'Categories',
            'categories' => $result['rows'],
            'page' => $page,
            'pages' => $result['pages'],
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->requireAdmin();
        $this->view('categories/form', [
            'title' => 'Add Category',
            'category' => null,
            'errors' => [],
        ]);
    }

    public function store(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $payload = $this->service->payload($_POST);
        $errors = $this->service->validate($payload);

        if ($errors !== []) {
            set_old($_POST);
            $this->view('categories/form', ['title' => 'Add Category', 'category' => null, 'errors' => $errors]);
            return;
        }

        $this->categories->create($payload);
        clear_old();
        set_flash('success', 'Category created successfully.');
        redirect(url('categories'));
    }

    public function edit(): void
    {
        $this->requireAdmin();
        $category = $this->categories->find((int) input('id'));
        if (!$category) {
            set_flash('danger', 'Category not found.');
            redirect(url('categories'));
        }

        $this->view('categories/form', [
            'title' => 'Edit Category',
            'category' => $category,
            'errors' => [],
        ]);
    }

    public function update(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $id = (int) input('id');
        $category = $this->categories->find($id);
        if (!$category) {
            set_flash('danger', 'Category not found.');
            redirect(url('categories'));
        }

        $payload = $this->service->payload($_POST);
        $errors = $this->service->validate($payload, $id);
        if ($errors !== []) {
            set_old($_POST);
            $this->view('categories/form', ['title' => 'Edit Category', 'category' => $category, 'errors' => $errors]);
            return;
        }

        $this->categories->update($id, $payload);
        clear_old();
        set_flash('success', 'Category updated successfully.');
        redirect(url('categories'));
    }

    public function delete(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $result = $this->categories->deleteOrDeactivate((int) input('id'));
        set_flash('success', $result === 'deleted' ? 'Category deleted safely.' : 'Category contains products and was deactivated.');
        redirect(url('categories'));
    }
}
