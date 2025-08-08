<?php

namespace App\Models;

use App\Core\Database;

class Raffle
{
    public static function create(array $data): int
    {
        return Database::insert('raffles', $data);
    }

    public static function findById(int $id): ?array
    {
        return Database::fetch(
            'SELECT r.*, u.name as creator_name 
             FROM raffles r 
             LEFT JOIN users u ON r.created_by = u.id 
             WHERE r.id = ?',
            [$id]
        );
    }

    public static function findAll(array $filters = []): array
    {
        $sql = 'SELECT r.*, u.name as creator_name 
                FROM raffles r 
                LEFT JOIN users u ON r.created_by = u.id 
                WHERE 1=1';
        $params = [];

        if (!empty($filters['published'])) {
            $sql .= ' AND r.is_published = ?';
            $params[] = $filters['published'] === 'true' ? 1 : 0;
        }

        if (!empty($filters['title'])) {
            $sql .= ' AND r.title LIKE ?';
            $params[] = '%' . $filters['title'] . '%';
        }

        if (!empty($filters['created_by'])) {
            $sql .= ' AND r.created_by = ?';
            $params[] = $filters['created_by'];
        }

        $sql .= ' ORDER BY r.created_at DESC';

        if (!empty($filters['limit'])) {
            $sql .= ' LIMIT ?';
            $params[] = (int) $filters['limit'];
        }

        return Database::fetchAll($sql, $params);
    }

    public static function getPublished(int $limit = null): array
    {
        $sql = 'SELECT r.*, u.name as creator_name,
                (SELECT COUNT(*) FROM raffle_numbers WHERE raffle_id = r.id AND status = "paid") as sold_count,
                (SELECT COUNT(*) FROM raffle_numbers WHERE raffle_id = r.id AND status = "reserved") as reserved_count
                FROM raffles r 
                LEFT JOIN users u ON r.created_by = u.id 
                WHERE r.is_published = 1 
                ORDER BY r.created_at DESC';
        
        $params = [];
        if ($limit) {
            $sql .= ' LIMIT ?';
            $params[] = $limit;
        }

        return Database::fetchAll($sql, $params);
    }

    public static function update(int $id, array $data): bool
    {
        return Database::update('raffles', $data, 'id = ?', [$id]) > 0;
    }

    public static function delete(int $id): bool
    {
        return Database::delete('raffles', 'id = ?', [$id]) > 0;
    }

    public static function publish(int $id): bool
    {
        return self::update($id, ['is_published' => 1]);
    }

    public static function unpublish(int $id): bool
    {
        return self::update($id, ['is_published' => 0]);
    }

    public static function getNumbers(int $raffleId): array
    {
        return Database::fetchAll(
            'SELECT * FROM raffle_numbers WHERE raffle_id = ? ORDER BY number',
            [$raffleId]
        );
    }

    public static function getNumbersWithStatus(int $raffleId): array
    {
        return Database::fetchAll(
            'SELECT number, status FROM raffle_numbers WHERE raffle_id = ? ORDER BY number',
            [$raffleId]
        );
    }

    public static function reserveNumbers(int $raffleId, array $numbers): bool
    {
        try {
            Database::connect()->beginTransaction();

            // Verificar se os números estão disponíveis
            $placeholders = str_repeat('?,', count($numbers) - 1) . '?';
            $params = array_merge([$raffleId], $numbers);
            
            $unavailable = Database::fetchAll(
                "SELECT number FROM raffle_numbers 
                 WHERE raffle_id = ? AND number IN ({$placeholders}) AND status != 'available'",
                $params
            );

            if (!empty($unavailable)) {
                Database::connect()->rollBack();
                return false;
            }

            // Reservar os números
            foreach ($numbers as $number) {
                Database::update(
                    'raffle_numbers',
                    ['status' => 'reserved', 'reserved_at' => date('Y-m-d H:i:s')],
                    'raffle_id = ? AND number = ?',
                    [$raffleId, $number]
                );
            }

            Database::connect()->commit();
            return true;

        } catch (\Exception $e) {
            Database::connect()->rollBack();
            return false;
        }
    }

    public static function markNumbersAsPaid(int $raffleId, array $numbers): bool
    {
        try {
            Database::connect()->beginTransaction();

            foreach ($numbers as $number) {
                Database::update(
                    'raffle_numbers',
                    ['status' => 'paid', 'paid_at' => date('Y-m-d H:i:s')],
                    'raffle_id = ? AND number = ?',
                    [$raffleId, $number]
                );
            }

            Database::connect()->commit();
            return true;

        } catch (\Exception $e) {
            Database::connect()->rollBack();
            return false;
        }
    }

    public static function getStats(int $raffleId): array
    {
        return Database::fetch(
            'SELECT 
                COUNT(*) as total_numbers,
                SUM(CASE WHEN status = "available" THEN 1 ELSE 0 END) as available,
                SUM(CASE WHEN status = "reserved" THEN 1 ELSE 0 END) as reserved,
                SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid
             FROM raffle_numbers 
             WHERE raffle_id = ?',
            [$raffleId]
        );
    }

    public static function getTotalRevenue(int $raffleId): float
    {
        $raffle = self::findById($raffleId);
        if (!$raffle) return 0;

        $stats = self::getStats($raffleId);
        return (float) $raffle['price_per_number'] * $stats['paid'];
    }

    public static function getAdminStats(): array
    {
        return Database::fetch(
            'SELECT 
                COUNT(*) as total_raffles,
                SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published_raffles,
                SUM(CASE WHEN is_published = 0 THEN 1 ELSE 0 END) as draft_raffles
             FROM raffles'
        );
    }

    public static function uploadImage(array $file, int $raffleId): ?string
    {
        try {
            $uploadDir = __DIR__ . '/../../uploads/raffles/';
            
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (!is_writable($uploadDir)) {
                error_log("Upload directory is not writable: " . $uploadDir);
                return null;
            }

            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                error_log("Invalid file type: " . $file['type']);
                return null;
            }

            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($file['size'] > $maxSize) {
                error_log("File too large: " . $file['size']);
                return null;
            }

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = 'raffle_' . $raffleId . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;

            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                error_log("Failed to move uploaded file to: " . $filepath);
                error_log("Upload error: " . error_get_last()['message']);
                return null;
            }

            chmod($filepath, 0644); // Permissões corretas para o arquivo
            return '/uploads/raffles/' . $filename;

        } catch (\Exception $e) {
            error_log("Error uploading image: " . $e->getMessage());
            return null;
        }
    }

    public static function deleteImage(string $imagePath): bool
    {
        $fullPath = __DIR__ . '/../..' . $imagePath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}
