<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Raffle;
use App\Models\Reservation;
use App\Services\EmailService;

class RaffleController extends Controller
{
    public function index(): void
    {
        $filters = [
            'title' => $this->getInput('search')
        ];

        $raffles = Raffle::getPublished();

        $this->view('raffles/index', [
            'title' => 'Todas as Rifas',
            'raffles' => $raffles,
            'filters' => $filters
        ]);
    }

    public function show(int $id): void
    {
        $raffle = Raffle::findById($id);
        
        if (!$raffle || !$raffle['is_published']) {
            Session::flash('error', 'Rifa não encontrada ou não está disponível.');
            $this->redirect('/');
        }

        $stats = Raffle::getStats($id);
        $numbers = Raffle::getNumbersWithStatus($id);

        $this->view('raffles/show', [
            'title' => $raffle['title'],
            'raffle' => $raffle,
            'stats' => $stats,
            'numbers' => $numbers,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    public function reserve(int $id): void
    {
        // Debug: log dos dados recebidos
        error_log('Reserve method called for raffle ID: ' . $id);
        error_log('POST data: ' . print_r($_POST, true));
        error_log('JSON input: ' . file_get_contents('php://input'));
        
        if (!$this->validateCsrf()) {
            error_log('CSRF validation failed');
            error_log('Received token: ' . $this->getInput('csrf_token'));
            error_log('Session token: ' . Session::get('csrf_token'));
            $this->json(['success' => false, 'message' => 'Token de segurança inválido.'], 400);
        }

        $raffle = Raffle::findById($id);
        if (!$raffle || !$raffle['is_published']) {
            $this->json(['success' => false, 'message' => 'Rifa não encontrada.'], 404);
        }

        $customerName = $this->sanitize($this->getInput('customer_name'));
        $customerEmail = $this->sanitize($this->getInput('customer_email'));
        $selectedNumbers = $this->getInput('numbers');

        // Validações
        if (empty($customerName) || empty($customerEmail)) {
            $this->json(['success' => false, 'message' => 'Nome e email são obrigatórios.'], 400);
        }

        if (!$this->validateEmail($customerEmail)) {
            $this->json(['success' => false, 'message' => 'Email inválido.'], 400);
        }

        if (empty($selectedNumbers) || !is_array($selectedNumbers)) {
            $this->json(['success' => false, 'message' => 'Selecione pelo menos um número.'], 400);
        }

        // Converter para inteiros e validar
        $numbers = array_map('intval', $selectedNumbers);
        $numbers = array_unique($numbers);

        foreach ($numbers as $number) {
            if ($number < 1 || $number > $raffle['total_numbers']) {
                $this->json(['success' => false, 'message' => 'Número inválido selecionado.'], 400);
            }
        }

        try {
            $reservationId = Reservation::createReservation($id, $customerEmail, $customerName, $numbers);
            
            if (!$reservationId) {
                $this->json(['success' => false, 'message' => 'Um ou mais números já foram reservados. Atualize a página e tente novamente.'], 400);
            }

            // Enviar email de confirmação
            $reservation = Reservation::findById($reservationId);
            $emailService = new EmailService();
            $emailService->sendReservationConfirmation($reservation, $raffle);

            // Notificar administradores
            $emailService->sendAdminNotification(
                'Nova Reserva - ' . $raffle['title'],
                "Nova reserva recebida:\n" .
                "Cliente: {$customerName} ({$customerEmail})\n" .
                "Números: " . implode(', ', $numbers) . "\n" .
                "Valor: R$ " . number_format($raffle['price_per_number'] * count($numbers), 2, ',', '.')
            );

            $this->json([
                'success' => true,
                'message' => 'Números reservados com sucesso!',
                'reservation_id' => $reservationId,
                'total_amount' => $raffle['price_per_number'] * count($numbers)
            ]);

        } catch (\Exception $e) {
            $this->json(['success' => false, 'message' => 'Erro interno. Tente novamente.'], 500);
        }
    }
}
