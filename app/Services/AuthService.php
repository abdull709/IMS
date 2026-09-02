<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

class AuthService
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function attempt(string $login, string $password): bool
    {
        $user = $this->users->findByLogin($login);

        if (!$user || $user['status'] !== 'active') {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        session_regenerate_id(true);
        $this->users->updateLastLogin((int) $user['id']);
        $fresh = $this->users->find((int) $user['id']);

        $_SESSION['user'] = [
            'id' => (int) $fresh['id'],
            'full_name' => $fresh['full_name'],
            'username' => $fresh['username'],
            'email' => $fresh['email'],
            'role' => $fresh['role'],
            'status' => $fresh['status'],
            'last_login' => $fresh['last_login'],
            'created_at' => $fresh['created_at'],
        ];

        return true;
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
}
