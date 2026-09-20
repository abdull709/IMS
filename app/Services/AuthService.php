<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

class AuthService
{
    private const DEFAULT_PASSWORD_HASH = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi.';

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

        $storedHash = (string) ($user['password'] ?? '');
        $usesBundledDefault = $password === 'password'
            && hash_equals(self::DEFAULT_PASSWORD_HASH, $storedHash);

        if (!password_verify($password, $storedHash) && !$usesBundledDefault) {
            return false;
        }

        if ($usesBundledDefault || password_needs_rehash($storedHash, PASSWORD_DEFAULT)) {
            try {
                $storedHash = password_hash($password, PASSWORD_DEFAULT);
                $this->users->updatePassword((int) $user['id'], $storedHash);
                $user['password'] = $storedHash;
            } catch (\Throwable $exception) {
                app_log_exception($exception);
            }
        }

        session_regenerate_id(true);

        $fresh = $user;
        try {
            $this->users->updateLastLogin((int) $user['id']);
            $fresh = $this->users->find((int) $user['id']) ?? $user;
        } catch (\Throwable $exception) {
            app_log_exception($exception);
        }

        $_SESSION['user'] = [
            'id' => (int) $fresh['id'],
            'full_name' => $fresh['full_name'],
            'username' => $fresh['username'],
            'email' => $fresh['email'] ?? null,
            'role' => $fresh['role'],
            'status' => $fresh['status'],
            'last_login' => $fresh['last_login'] ?? null,
            'created_at' => $fresh['created_at'] ?? null,
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
