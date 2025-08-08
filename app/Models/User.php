<?php

namespace App\Models;

use App\Core\Database;

class User
{
    public static function create(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return Database::insert('users', $data);
    }

    public static function findById(int $id): ?array
    {
        return Database::fetch(
            'SELECT id, name, email, role, created_at FROM users WHERE id = ?',
            [$id]
        );
    }

    public static function findByEmail(string $email): ?array
    {
        return Database::fetch(
            'SELECT id, name, email, password, role, created_at FROM users WHERE email = ?',
            [$email]
        );
    }

    public static function findAll(): array
    {
        return Database::fetchAll(
            'SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC'
        );
    }

    public static function update(int $id, array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        return Database::update('users', $data, 'id = ?', [$id]) > 0;
    }

    public static function delete(int $id): bool
    {
        return Database::delete('users', 'id = ?', [$id]) > 0;
    }

    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public static function emailExists(string $email, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) as count FROM users WHERE email = ?';
        $params = [$email];
        
        if ($excludeId) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        
        $result = Database::fetch($sql, $params);
        return $result['count'] > 0;
    }

    public static function createAdmin(string $name, string $email, string $password): int
    {
        return self::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'admin'
        ]);
    }

    public static function isAdmin(int $userId): bool
    {
        $user = self::findById($userId);
        return $user && $user['role'] === 'admin';
    }

    public static function getAdmins(): array
    {
        return Database::fetchAll(
            'SELECT id, name, email, created_at FROM users WHERE role = "admin" ORDER BY name'
        );
    }

    public static function getUsersCount(): array
    {
        return Database::fetch(
            'SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN role = "admin" THEN 1 ELSE 0 END) as admins,
                SUM(CASE WHEN role = "user" THEN 1 ELSE 0 END) as users
             FROM users'
        );
    }
}
