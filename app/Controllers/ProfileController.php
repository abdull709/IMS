<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class ProfileController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function index(): void
    {
        $this->requireLogin();
        $user = $this->users->find((int) current_user()['id']);
        $this->view('profile/index', ['title' => 'Profile', 'user' => $user, 'errors' => []]);
    }

    public function update(): void
    {
        $this->requireLogin();
        $this->validateCsrf();

        $id = (int) current_user()['id'];
        $payload = [
            'full_name' => trim((string) input('full_name', '')),
            'email' => trim((string) input('email', '')) ?: null,
        ];

        $errors = [];
        if ($payload['full_name'] === '') {
            $errors['full_name'] = 'Full name is required.';
        }
        if ($payload['email'] !== null && !filter_var($payload['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Enter a valid email address.';
        } elseif ($payload['email'] !== null && $this->users->emailExists($payload['email'], $id)) {
            $errors['email'] = 'Email is already used.';
        }

        if ($errors !== []) {
            $user = $this->users->find($id);
            $this->view('profile/index', ['title' => 'Profile', 'user' => $user, 'errors' => $errors]);
            return;
        }

        $this->users->updateProfile($id, $payload);
        $_SESSION['user']['full_name'] = $payload['full_name'];
        $_SESSION['user']['email'] = $payload['email'];
        set_flash('success', 'Profile updated successfully.');
        redirect(url('profile'));
    }

    public function password(): void
    {
        $this->requireLogin();
        $this->validateCsrf();

        $user = $this->users->find((int) current_user()['id']);
        $current = (string) input('current_password', '');
        $new = (string) input('new_password', '');
        $confirm = (string) input('confirm_password', '');

        if (!$user || !password_verify($current, $user['password'])) {
            set_flash('danger', 'Current password is incorrect.');
            redirect(url('profile'));
        }
        if (strlen($new) < 6) {
            set_flash('danger', 'New password must be at least 6 characters.');
            redirect(url('profile'));
        }
        if ($new !== $confirm) {
            set_flash('danger', 'Password confirmation does not match.');
            redirect(url('profile'));
        }

        $this->users->updatePassword((int) $user['id'], password_hash($new, PASSWORD_DEFAULT));
        set_flash('success', 'Password changed successfully.');
        redirect(url('profile'));
    }
}
