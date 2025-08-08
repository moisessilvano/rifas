<?php

namespace App\Models;

use App\Core\Database;

class Reservation
{
    public static function create(array $data): int
    {
        return Database::insert('reservations', $data);
    }

    public static function findById(int $id): ?array
    {
        return Database::fetch(
            'SELECT r.*, rf.title as raffle_title, rf.price_per_number
             FROM reservations r 
             LEFT JOIN raffles rf ON r.raffle_id = rf.id 
             WHERE r.id = ?',
            [$id]
        );
    }

    public static function findAll(array $filters = []): array
    {
        $sql = 'SELECT r.*, rf.title as raffle_title, rf.price_per_number
                FROM reservations r 
                LEFT JOIN raffles rf ON r.raffle_id = rf.id 
                WHERE 1=1';
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= ' AND r.status = ?';
            $params[] = $filters['status'];
        }

        if (!empty($filters['raffle_id'])) {
            $sql .= ' AND r.raffle_id = ?';
            $params[] = $filters['raffle_id'];
        }

        if (!empty($filters['customer_email'])) {
            $sql .= ' AND r.customer_email LIKE ?';
            $params[] = '%' . $filters['customer_email'] . '%';
        }

        if (!empty($filters['start_date'])) {
            $sql .= ' AND DATE(r.reserved_at) >= ?';
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= ' AND DATE(r.reserved_at) <= ?';
            $params[] = $filters['end_date'];
        }

        $sql .= ' ORDER BY r.reserved_at DESC';

        if (!empty($filters['limit'])) {
            $sql .= ' LIMIT ?';
            $params[] = (int) $filters['limit'];
        }

        return Database::fetchAll($sql, $params);
    }

    public static function getByRaffle(int $raffleId): array
    {
        return Database::fetchAll(
            'SELECT * FROM reservations WHERE raffle_id = ? ORDER BY reserved_at DESC',
            [$raffleId]
        );
    }

    public static function getByEmail(string $email): array
    {
        return Database::fetchAll(
            'SELECT r.*, rf.title as raffle_title, rf.image_path
             FROM reservations r 
             LEFT JOIN raffles rf ON r.raffle_id = rf.id 
             WHERE r.customer_email = ? 
             ORDER BY r.reserved_at DESC',
            [$email]
        );
    }

    public static function getPending(array $filters = []): array
    {
        $sql = 'SELECT r.*, rf.title as raffle_title
                FROM reservations r 
                LEFT JOIN raffles rf ON r.raffle_id = rf.id 
                WHERE r.status = "reserved"';
        $params = [];

        if (!empty($filters['limit'])) {
            $sql .= ' LIMIT ?';
            $params[] = (int) $filters['limit'];
        }

        return Database::fetchAll($sql, $params);
    }

    public static function update(int $id, array $data): bool
    {
        return Database::update('reservations', $data, 'id = ?', [$id]) > 0;
    }

    public static function confirmPayment(int $id): bool
    {
        try {
            Database::connect()->beginTransaction();

            $reservation = self::findById($id);
            if (!$reservation || $reservation['status'] !== 'reserved') {
                Database::connect()->rollBack();
                return false;
            }

            // Atualizar status da reserva
            $updated = self::update($id, [
                'status' => 'paid',
                'paid_at' => date('Y-m-d H:i:s')
            ]);

            if (!$updated) {
                Database::connect()->rollBack();
                return false;
            }

            // Marcar números como pagos
            $numbers = json_decode($reservation['numbers'], true);
            foreach ($numbers as $number) {
                Database::update(
                    'raffle_numbers',
                    ['status' => 'paid', 'paid_at' => date('Y-m-d H:i:s')],
                    'raffle_id = ? AND number = ?',
                    [$reservation['raffle_id'], $number]
                );
            }

            Database::connect()->commit();
            return true;

        } catch (\Exception $e) {
            Database::connect()->rollBack();
            return false;
        }
    }

    public static function cancel(int $id): bool
    {
        try {
            Database::connect()->beginTransaction();

            $reservation = self::findById($id);
            if (!$reservation) {
                Database::connect()->rollBack();
                return false;
            }

            // Atualizar status da reserva
            $updated = self::update($id, ['status' => 'cancelled']);

            if (!$updated) {
                Database::connect()->rollBack();
                return false;
            }

            // Liberar números reservados
            if ($reservation['status'] === 'reserved') {
                $numbers = json_decode($reservation['numbers'], true);
                foreach ($numbers as $number) {
                    Database::update(
                        'raffle_numbers',
                        ['status' => 'available', 'reserved_at' => null, 'paid_at' => null],
                        'raffle_id = ? AND number = ?',
                        [$reservation['raffle_id'], $number]
                    );
                }
            }

            Database::connect()->commit();
            return true;

        } catch (\Exception $e) {
            Database::connect()->rollBack();
            return false;
        }
    }

    public static function createReservation(int $raffleId, string $customerEmail, string $customerName, array $numbers): ?int
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
                return null;
            }

            // Buscar preço da rifa
            $raffle = Raffle::findById($raffleId);
            if (!$raffle) {
                Database::connect()->rollBack();
                return null;
            }

            // Criar reserva
            $reservationId = self::create([
                'raffle_id' => $raffleId,
                'customer_email' => $customerEmail,
                'customer_name' => $customerName,
                'numbers' => json_encode($numbers),
                'total_amount' => $raffle['price_per_number'] * count($numbers),
                'status' => 'reserved'
            ]);

            // Reservar números
            foreach ($numbers as $number) {
                Database::update(
                    'raffle_numbers',
                    ['status' => 'reserved', 'reserved_at' => date('Y-m-d H:i:s')],
                    'raffle_id = ? AND number = ?',
                    [$raffleId, $number]
                );
            }

            Database::connect()->commit();
            return $reservationId;

        } catch (\Exception $e) {
            Database::connect()->rollBack();
            return null;
        }
    }

    public static function getStats(): array
    {
        return Database::fetch(
            'SELECT 
                COUNT(*) as total_reservations,
                SUM(CASE WHEN status = "reserved" THEN 1 ELSE 0 END) as pending_payment,
                SUM(CASE WHEN status = "paid" THEN 1 ELSE 0 END) as paid,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status = "paid" THEN total_amount ELSE 0 END) as total_revenue
             FROM reservations'
        );
    }

    public static function uploadPaymentProof(array $file, int $reservationId): ?string
    {
        $uploadDir = __DIR__ . '/../../public/uploads/payment-proofs/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/pdf'];
        if (!in_array($file['type'], $allowedTypes)) {
            return null;
        }

        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($file['size'] > $maxSize) {
            return null;
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'proof_' . $reservationId . '_' . time() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return '/uploads/payment-proofs/' . $filename;
        }

        return null;
    }

    public static function deletePaymentProof(string $proofPath): bool
    {
        $fullPath = __DIR__ . '/../../public' . $proofPath;
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }
        return false;
    }
}
