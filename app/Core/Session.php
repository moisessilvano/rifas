<?php

namespace App\Core;

class Session
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public static function destroy(): void
    {
        session_destroy();
    }

    public static function flash(string $key, $value = null)
    {
        if ($value !== null) {
            self::set("flash_{$key}", $value);
            return;
        }

        $flashKey = "flash_{$key}";
        $value = self::get($flashKey);
        self::remove($flashKey);
        return $value;
    }

    public static function isAuthenticated(): bool
    {
        return self::has('user_id');
    }

    public static function isAdmin(): bool
    {
        return self::get('user_role') === 'admin';
    }

    public static function getUserId(): ?int
    {
        return self::get('user_id');
    }

    public static function getUser(): ?array
    {
        $userId = self::getUserId();
        if (!$userId) {
            return null;
        }

        return [
            'id' => $userId,
            'name' => self::get('user_name'),
            'email' => self::get('user_email'),
            'role' => self::get('user_role')
        ];
    }

    public static function login(array $user): void
    {
        self::set('user_id', $user['id']);
        self::set('user_name', $user['name']);
        self::set('user_email', $user['email']);
        self::set('user_role', $user['role']);
    }

    public static function logout(): void
    {
        self::remove('user_id');
        self::remove('user_name');
        self::remove('user_email');
        self::remove('user_role');
    }
}
