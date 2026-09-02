<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (is_logged_in()) {
            redirect(url('dashboard'));
        }

        $this->view('auth/login', ['title' => 'Login'], 'auth');
    }

    public function login(): void
    {
        $this->validateCsrf();

        $login = trim((string) input('login', ''));
        $password = (string) input('password', '');

        if ($login === '' || $password === '') {
            set_flash('danger', 'Enter your username/email and password.');
            set_old($_POST);
            redirect(url('login'));
        }

        if ((new AuthService())->attempt($login, $password)) {
            clear_old();
            set_flash('success', 'Welcome back.');
            redirect(url('dashboard'));
        }

        set_flash('danger', 'Invalid credentials or inactive account.');
        set_old(['login' => $login]);
        redirect(url('login'));
    }

    public function logout(): void
    {
        (new AuthService())->logout();
        redirect(url('login'));
    }
}
