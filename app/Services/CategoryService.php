<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    private Category $categories;

    public function __construct()
    {
        $this->categories = new Category();
    }

    public function validate(array $data, ?int $id = null): array
    {
        $errors = [];
        if (trim($data['category_name'] ?? '') === '') {
            $errors['category_name'] = 'Category name is required.';
        } elseif ($this->categories->nameExists(trim($data['category_name']), $id)) {
            $errors['category_name'] = 'This category name already exists.';
        }

        if (!in_array($data['status'] ?? 'active', ['active', 'inactive'], true)) {
            $errors['status'] = 'Invalid category status.';
        }

        return $errors;
    }

    public function payload(array $data): array
    {
        return [
            'category_name' => trim((string) ($data['category_name'] ?? '')),
            'description' => trim((string) ($data['description'] ?? '')),
            'status' => in_array($data['status'] ?? 'active', ['active', 'inactive'], true) ? $data['status'] : 'active',
        ];
    }
}
