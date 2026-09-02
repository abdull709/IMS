<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class UserController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function index(): void
    {
        $this->requireAdmin();
        $search = trim((string) input('search', ''));
        $page = max(1, (int) input('page', 1));
        $result = $this->users->paginate($search, $page);

        $this->view('users/index', [
            'title' => 'Users',
            'users' => $result['rows'],
            'search' => $search,
            'page' => $page,
            'pages' => $result['pages'],
        ]);
    }

    public function create(): void
    {
        $this->requireAdmin();
        $this->view('users/form', ['title' => 'Add User', 'user' => null, 'errors' => []]);
    }

    public function store(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        [$payload, $errors] = $this->validateUser($_POST);
        if ($errors !== []) {
            set_old($_POST);
            $this->view('users/form', ['title' => 'Add User', 'user' => null, 'errors' => $errors]);
            return;
        }

        $payload['password'] = password_hash((string) $_POST['password'], PASSWORD_DEFAULT);
        $this->users->create($payload);
        clear_old();
        set_flash('success', 'User created successfully.');
        redirect(url('users'));
    }

    public function edit(): void
    {
        $this->requireAdmin();
        $user = $this->users->find((int) input('id'));
        if (!$user) {
            set_flash('danger', 'User not found.');
            redirect(url('users'));
        }

        $this->view('users/form', ['title' => 'Edit User', 'user' => $user, 'errors' => []]);
    }

    public function update(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $id = (int) input('id');
        $user = $this->users->find($id);
        if (!$user) {
            set_flash('danger', 'User not found.');
            redirect(url('users'));
        }

        [$payload, $errors] = $this->validateUser($_POST, $id);
        if ($id === (int) current_user()['id'] && ($payload['status'] !== 'active' || $payload['role'] !== 'admin')) {
            $errors['status'] = 'You cannot demote or deactivate your own administrator account while logged in.';
        }
        if ($errors !== []) {
            set_old($_POST);
            $this->view('users/form', ['title' => 'Edit User', 'user' => $user, 'errors' => $errors]);
            return;
        }

        $this->users->update($id, $payload);

        if (trim((string) input('password', '')) !== '') {
            if ((string) input('password') !== (string) input('confirm_password')) {
                set_flash('danger', 'Password confirmation does not match.');
                redirect(url('users/edit', ['id' => $id]));
            }
            $this->users->updatePassword($id, password_hash((string) input('password'), PASSWORD_DEFAULT));
        }

        if ($id === (int) current_user()['id']) {
            $_SESSION['user'] = array_merge($_SESSION['user'], [
                'full_name' => $payload['full_name'],
                'username' => $payload['username'],
                'email' => $payload['email'],
                'role' => $payload['role'],
                'status' => $payload['status'],
            ]);
        }

        clear_old();
        set_flash('success', 'User updated successfully.');
        redirect(url('users'));
    }

    public function delete(): void
    {
        $this->requireAdmin();
        $this->validateCsrf();

        $id = (int) input('id');
        if ($id === (int) current_user()['id']) {
            set_flash('danger', 'You cannot deactivate your own account while logged in.');
            redirect(url('users'));
        }

        $this->users->deactivate($id);
        set_flash('success', 'User deactivated successfully.');
        redirect(url('users'));
    }

    private function validateUser(array $data, ?int $id = null): array
    {
        $errors = [];
        $payload = [
            'full_name' => trim((string) ($data['full_name'] ?? '')),
            'username' => trim((string) ($data['username'] ?? '')),
            'email' => trim((string) ($data['email'] ?? '')) ?: null,
            'role' => in_array($data['role'] ?? 'staff', ['admin', 'staff'], true) ? $data['role'] : 'staff',
            'status' => in_array($data['status'] ?? 'active', ['active', 'inactive'], true) ? $data['status'] : 'active',
        ];

        if ($payload['full_name'] === '') {
            $errors['full_name'] = 'Full name is required.';
        }
        if ($payload['username'] === '') {
            $errors['username'] = 'Username is required.';
        } elseif ($this->users->usernameExists($payload['username'], $id)) {
            $errors['username'] = 'Username is already used.';
        }
        if ($payload['email'] !== null && !filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        } elseif ($payload['email'] !== null && $this->users->emailExists($payload['email'], $id)) {
            $errors['email'] = 'Email is already used.';
        }

        if ($id === null || trim((string) ($data['password'] ?? '')) !== '') {
            if (strlen((string) ($data['password'] ?? '')) < 6) {
                $errors['password'] = 'Password must be at least 6 characters.';
            }
            if (($data['password'] ?? '') !== ($data['confirm_password'] ?? '')) {
                $errors['confirm_password'] = 'Password confirmation does not match.';
            }
        }

        return [$payload, $errors];
    }
}
