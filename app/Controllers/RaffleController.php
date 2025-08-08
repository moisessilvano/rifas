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

    public function confirm(int $id): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('error', 'Token de segurança inválido.');
            $this->redirect("/raffles/{$id}");
        }

        $raffle = Raffle::findById($id);
        if (!$raffle || !$raffle['is_published']) {
            Session::flash('error', 'Rifa não encontrada.');
            $this->redirect('/');
        }

        $customerName = $this->sanitize($this->getInput('customer_name'));
        $customerEmail = $this->sanitize($this->getInput('customer_email'));
        $selectedNumbers = $this->getInput('numbers');

        // Validações básicas antes de mostrar a confirmação
        if (empty($customerName) || empty($customerEmail)) {
            Session::flash('error', 'Nome e email são obrigatórios.');
            $this->redirect("/raffles/{$id}");
        }

        if (!$this->validateEmail($customerEmail)) {
            Session::flash('error', 'Email inválido.');
            $this->redirect("/raffles/{$id}");
        }

        if (empty($selectedNumbers) || !is_array($selectedNumbers)) {
            Session::flash('error', 'Selecione pelo menos um número.');
            $this->redirect("/raffles/{$id}");
        }

        $this->view('raffles/confirm', [
            'raffle' => $raffle,
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'numbers' => $selectedNumbers,
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    public function reserve(int $id): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('error', 'Token de segurança inválido.');
            $this->redirect("/raffles/{$id}");
        }

        $raffle = Raffle::findById($id);
        if (!$raffle || !$raffle['is_published']) {
            Session::flash('error', 'Rifa não encontrada.');
            $this->redirect('/');
        }

        $customerName = $this->sanitize($this->getInput('customer_name'));
        $customerEmail = $this->sanitize($this->getInput('customer_email'));
        $selectedNumbers = $this->getInput('numbers');

        // Validações básicas antes de mostrar confirmação
        if (empty($customerName) || empty($customerEmail)) {
            Session::flash('error', 'Nome e email são obrigatórios.');
            $this->redirect("/raffles/{$id}");
        }

        if (!$this->validateEmail($customerEmail)) {
            Session::flash('error', 'Email inválido.');
            $this->redirect("/raffles/{$id}");
        }

        if (empty($selectedNumbers) || !is_array($selectedNumbers)) {
            Session::flash('error', 'Selecione pelo menos um número.');
            $this->redirect("/raffles/{$id}");
        }

        // Processar reserva diretamente

        // Converter para inteiros e validar
        $numbers = array_map('intval', $selectedNumbers);
        $numbers = array_unique($numbers);

        foreach ($numbers as $number) {
            if ($number < 1 || $number > $raffle['total_numbers']) {
                Session::flash('error', 'Número inválido selecionado.');
                $this->redirect("/raffles/{$id}");
            }
        }

        try {
            $reservationId = Reservation::createReservation($id, $customerEmail, $customerName, $numbers);
            
            if (!$reservationId) {
                Session::flash('error', 'Um ou mais números já foram reservados. Atualize a página e tente novamente.');
                $this->redirect("/raffles/{$id}");
            }

            // TODO: Enviar email de confirmação (desabilitado temporariamente)
            // $reservation = Reservation::findById($reservationId);
            // $emailService = new EmailService();
            // $emailService->sendReservationConfirmation($reservation, $raffle);

            // TODO: Notificar administradores (desabilitado temporariamente)
            // $emailService->sendAdminNotification(
            //     'Nova Reserva - ' . $raffle['title'],
            //     "Nova reserva recebida:\n" .
            //     "Cliente: {$customerName} ({$customerEmail})\n" .
            //     "Números: " . implode(', ', $numbers) . "\n" .
            //     "Valor: R$ " . number_format($raffle['price_per_number'] * count($numbers), 2, ',', '.')
            // );

            Session::flash('success', 'Números reservados com sucesso! Sua reserva foi confirmada.');
            $this->redirect("/raffles/{$id}");

        } catch (\Exception $e) {
            Session::flash('error', 'Erro interno. Tente novamente.');
            $this->redirect("/raffles/{$id}");
        }
    }

    public function cancel(int $id): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('error', 'Token de segurança inválido.');
            $this->redirect("/raffles/{$id}");
        }

        $this->redirect("/raffles/{$id}");
    }
}
