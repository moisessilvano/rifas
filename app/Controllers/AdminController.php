<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Raffle;
use App\Models\Reservation;
use App\Models\User;
use App\Services\EmailService;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->requireAdmin();
    }

    public function dashboard(): void
    {
        $raffleStats = Raffle::getAdminStats();
        $userStats = User::getUsersCount();
        $recentRaffles = Raffle::findAll(['limit' => 5]);
        $pendingReservations = Reservation::getPending(['limit' => 10]);

        $this->view('admin/dashboard', [
            'title' => 'Painel Administrativo',
            'raffleStats' => $raffleStats,
            'userStats' => $userStats,
            'recentRaffles' => $recentRaffles,
            'pendingReservations' => $pendingReservations
        ]);
    }

    public function raffles(): void
    {
        $filters = [
            'title' => $this->getInput('title'),
            'published' => $this->getInput('published')
        ];

        $raffles = Raffle::findAll($filters);

        $this->view('admin/raffles/index', [
            'title' => 'Gerenciar Rifas',
            'raffles' => $raffles,
            'filters' => $filters
        ]);
    }

    public function createRaffle(): void
    {
        $this->view('admin/raffles/create', [
            'title' => 'Nova Rifa',
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    public function storeRaffle(): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('error', 'Token de segurança inválido.');
            $this->redirect('/admin/raffles/create');
        }

        $data = [
            'title' => $this->sanitize($this->getInput('title')),
            'description' => $this->sanitize($this->getInput('description')),
            'total_numbers' => (int) $this->getInput('total_numbers'),
            'price_per_number' => (float) $this->getInput('price_per_number'),
            'draw_date' => $this->getInput('draw_date') ? date('Y-m-d H:i:s', strtotime($this->getInput('draw_date'))) : null,
            'draw_location' => $this->sanitize($this->getInput('draw_location')),
            'contact_phone' => $this->sanitize($this->getInput('contact_phone')),
            'is_published' => $this->getInput('is_published') ? 1 : 0,
            'created_by' => Session::getUserId()
        ];

        // Validações
        if (empty($data['title']) || empty($data['description'])) {
            Session::flash('error', 'Título e descrição são obrigatórios.');
            $this->redirect('/admin/raffles/create');
        }

        if ($data['total_numbers'] < 1 || $data['total_numbers'] > 10000) {
            Session::flash('error', 'Número total de cotas deve ser entre 1 e 10.000.');
            $this->redirect('/admin/raffles/create');
        }

        if ($data['price_per_number'] <= 0) {
            Session::flash('error', 'Valor por cota deve ser maior que zero.');
            $this->redirect('/admin/raffles/create');
        }

        try {
            $raffleId = Raffle::create($data);

            // Upload de imagem se fornecida
            if (!empty($_FILES['image']['name'])) {
                $imagePath = Raffle::uploadImage($_FILES['image'], $raffleId);
                if ($imagePath) {
                    Raffle::update($raffleId, ['image_path' => $imagePath]);
                } else {
                    Session::flash('warning', 'Rifa criada, mas houve erro no upload da imagem.');
                }
            }

            Session::flash('success', 'Rifa criada com sucesso!');
            $this->redirect('/admin/raffles/' . $raffleId);

        } catch (\Exception $e) {
            Session::flash('error', 'Erro ao criar rifa: ' . $e->getMessage());
            $this->redirect('/admin/raffles/create');
        }
    }

    public function showRaffle(int $id): void
    {
        $raffle = Raffle::findById($id);
        if (!$raffle) {
            Session::flash('error', 'Rifa não encontrada.');
            $this->redirect('/admin/raffles');
        }

        $stats = Raffle::getStats($id);
        $revenue = Raffle::getTotalRevenue($id);
        $reservations = Reservation::getByRaffle($id);

        $this->view('admin/raffles/show', [
            'title' => 'Rifa: ' . $raffle['title'],
            'raffle' => $raffle,
            'stats' => $stats,
            'revenue' => $revenue,
            'reservations' => $reservations
        ]);
    }

    public function editRaffle(int $id): void
    {
        $raffle = Raffle::findById($id);
        if (!$raffle) {
            Session::flash('error', 'Rifa não encontrada.');
            $this->redirect('/admin/raffles');
        }

        $this->view('admin/raffles/edit', [
            'title' => 'Editar Rifa',
            'raffle' => $raffle,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    public function updateRaffle(int $id): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('error', 'Token de segurança inválido.');
            $this->redirect('/admin/raffles/' . $id . '/edit');
        }

        $raffle = Raffle::findById($id);
        if (!$raffle) {
            Session::flash('error', 'Rifa não encontrada.');
            $this->redirect('/admin/raffles');
        }

        $data = [
            'title' => $this->sanitize($this->getInput('title')),
            'description' => $this->sanitize($this->getInput('description')),
            'price_per_number' => (float) $this->getInput('price_per_number'),
            'draw_date' => $this->getInput('draw_date') ? date('Y-m-d H:i:s', strtotime($this->getInput('draw_date'))) : null,
            'draw_location' => $this->sanitize($this->getInput('draw_location')),
            'contact_phone' => $this->sanitize($this->getInput('contact_phone')),
            'is_published' => $this->getInput('is_published') ? 1 : 0
        ];

        // Validações
        if (empty($data['title']) || empty($data['description'])) {
            Session::flash('error', 'Título e descrição são obrigatórios.');
            $this->redirect('/admin/raffles/' . $id . '/edit');
        }

        if ($data['price_per_number'] <= 0) {
            Session::flash('error', 'Valor por cota deve ser maior que zero.');
            $this->redirect('/admin/raffles/' . $id . '/edit');
        }

        try {
            // Upload de nova imagem se fornecida
            if (!empty($_FILES['image']['name'])) {
                $imagePath = Raffle::uploadImage($_FILES['image'], $id);
                if ($imagePath) {
                    // Deletar imagem anterior se existir
                    if ($raffle['image_path']) {
                        Raffle::deleteImage($raffle['image_path']);
                    }
                    $data['image_path'] = $imagePath;
                } else {
                    Session::flash('warning', 'Houve erro no upload da nova imagem.');
                }
            }

            Raffle::update($id, $data);
            Session::flash('success', 'Rifa atualizada com sucesso!');
            $this->redirect('/admin/raffles/' . $id);

        } catch (\Exception $e) {
            Session::flash('error', 'Erro ao atualizar rifa: ' . $e->getMessage());
            $this->redirect('/admin/raffles/' . $id . '/edit');
        }
    }

    public function deleteRaffle(int $id): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('error', 'Token de segurança inválido.');
            $this->redirect('/admin/raffles');
        }

        $raffle = Raffle::findById($id);
        if (!$raffle) {
            Session::flash('error', 'Rifa não encontrada.');
            $this->redirect('/admin/raffles');
        }

        try {
            // Deletar imagem se existir
            if ($raffle['image_path']) {
                Raffle::deleteImage($raffle['image_path']);
            }

            Raffle::delete($id);
            Session::flash('success', 'Rifa excluída com sucesso!');

        } catch (\Exception $e) {
            Session::flash('error', 'Erro ao excluir rifa: ' . $e->getMessage());
        }

        $this->redirect('/admin/raffles');
    }

    public function publishRaffle(int $id): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('error', 'Token de segurança inválido.');
            $this->redirect('/admin/raffles');
        }

        if (Raffle::publish($id)) {
            Session::flash('success', 'Rifa publicada com sucesso!');
        } else {
            Session::flash('error', 'Erro ao publicar rifa.');
        }

        $this->redirect('/admin/raffles/' . $id);
    }

    public function unpublishRaffle(int $id): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('error', 'Token de segurança inválido.');
            $this->redirect('/admin/raffles');
        }

        if (Raffle::unpublish($id)) {
            Session::flash('success', 'Rifa despublicada com sucesso!');
        } else {
            Session::flash('error', 'Erro ao despublicar rifa.');
        }

        $this->redirect('/admin/raffles/' . $id);
    }

    public function reservations(): void
    {
        $filters = [
            'status' => $this->getInput('status'),
            'raffle_id' => $this->getInput('raffle_id'),
            'customer_email' => $this->getInput('customer_email')
        ];

        $reservations = Reservation::findAll($filters);
        $raffles = Raffle::findAll();

        $this->view('admin/reservations/index', [
            'title' => 'Gerenciar Reservas',
            'reservations' => $reservations,
            'raffles' => $raffles,
            'filters' => $filters
        ]);
    }

    public function showReservation(int $id): void
    {
        $reservation = Reservation::findById($id);
        if (!$reservation) {
            Session::flash('error', 'Reserva não encontrada.');
            $this->redirect('/admin/reservations');
        }

        $this->view('admin/reservations/show', [
            'title' => 'Reserva #' . $id,
            'reservation' => $reservation,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    public function confirmPayment(int $id): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('error', 'Token de segurança inválido.');
            $this->redirect('/admin/reservations');
        }

        try {
            $reservation = Reservation::findById($id);
            if (!$reservation) {
                Session::flash('error', 'Reserva não encontrada.');
                $this->redirect('/admin/reservations');
            }

            if (Reservation::confirmPayment($id)) {
                // Enviar email de confirmação de pagamento
                $raffle = Raffle::findById($reservation['raffle_id']);
                $updatedReservation = Reservation::findById($id);
                
                $emailService = new EmailService();
                $emailService->sendPaymentConfirmation($updatedReservation, $raffle);

                Session::flash('success', 'Pagamento confirmado com sucesso!');
            } else {
                Session::flash('error', 'Erro ao confirmar pagamento.');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Erro: ' . $e->getMessage());
        }

        $this->redirect('/admin/reservations/' . $id);
    }

    public function cancelReservation(int $id): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('error', 'Token de segurança inválido.');
            $this->redirect('/admin/reservations');
        }

        try {
            if (Reservation::cancel($id)) {
                Session::flash('success', 'Reserva cancelada com sucesso!');
            } else {
                Session::flash('error', 'Erro ao cancelar reserva.');
            }
        } catch (\Exception $e) {
            Session::flash('error', 'Erro: ' . $e->getMessage());
        }

        $this->redirect('/admin/reservations/' . $id);
    }

    public function exportReservations(): void
    {
        $filters = [
            'status' => $this->getInput('status'),
            'raffle_id' => $this->getInput('raffle_id'),
            'start_date' => $this->getInput('start_date'),
            'end_date' => $this->getInput('end_date')
        ];

        $reservations = Reservation::findAll($filters);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="reservas_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        
        // Cabeçalho CSV
        fputcsv($output, [
            'ID', 'Rifa', 'Cliente', 'Email', 'Números', 
            'Valor Total', 'Status', 'Data Reserva', 'Data Pagamento'
        ]);

        // Dados
        foreach ($reservations as $reservation) {
            fputcsv($output, [
                $reservation['id'],
                $reservation['raffle_title'],
                $reservation['customer_name'],
                $reservation['customer_email'],
                implode(', ', json_decode($reservation['numbers'])),
                'R$ ' . number_format($reservation['total_amount'], 2, ',', '.'),
                $reservation['status'],
                date('d/m/Y H:i', strtotime($reservation['reserved_at'])),
                $reservation['paid_at'] ? date('d/m/Y H:i', strtotime($reservation['paid_at'])) : ''
            ]);
        }

        fclose($output);
        exit;
    }
}
