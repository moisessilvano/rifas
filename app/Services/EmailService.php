<?php

namespace App\Services;

use App\Core\Environment;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    private function configure(): void
    {
        $this->mailer->isSMTP();
        $this->mailer->Host = Environment::get('MAIL_HOST', 'smtp.gmail.com');
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = Environment::get('MAIL_USERNAME');
        $this->mailer->Password = Environment::get('MAIL_PASSWORD');
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = Environment::get('MAIL_PORT', 587);
        $this->mailer->CharSet = 'UTF-8';

        $this->mailer->setFrom(
            Environment::get('MAIL_FROM'),
            Environment::get('MAIL_FROM_NAME', 'Plataforma de Rifas')
        );
    }

    public function sendReservationConfirmation(array $reservation, array $raffle): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($reservation['customer_email'], $reservation['customer_name']);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Reserva Confirmada - ' . $raffle['title'];

            $numbers = json_decode($reservation['numbers'], true);
            $numbersText = implode(', ', array_map(fn($n) => str_pad($n, 2, '0', STR_PAD_LEFT), $numbers));

            $pixKey = Environment::get('PIX_KEY');
            $pixOwner = Environment::get('PIX_OWNER_NAME');

            $body = $this->getEmailTemplate('reservation_confirmation', [
                'customer_name' => $reservation['customer_name'],
                'raffle_title' => $raffle['title'],
                'numbers' => $numbersText,
                'total_amount' => $reservation['total_amount'],
                'reservation_id' => $reservation['id'],
                'pix_key' => $pixKey,
                'pix_owner' => $pixOwner,
                'app_url' => Environment::get('APP_URL')
            ]);

            $this->mailer->Body = $body;

            return $this->mailer->send();

        } catch (Exception $e) {
            error_log("Erro ao enviar email de confirmação de reserva: " . $e->getMessage());
            return false;
        }
    }

    public function sendPaymentConfirmation(array $reservation, array $raffle): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($reservation['customer_email'], $reservation['customer_name']);

            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Pagamento Confirmado - ' . $raffle['title'];

            $numbers = json_decode($reservation['numbers'], true);
            $numbersText = implode(', ', array_map(fn($n) => str_pad($n, 2, '0', STR_PAD_LEFT), $numbers));

            $body = $this->getEmailTemplate('payment_confirmation', [
                'customer_name' => $reservation['customer_name'],
                'raffle_title' => $raffle['title'],
                'numbers' => $numbersText,
                'total_amount' => $reservation['total_amount'],
                'reservation_id' => $reservation['id'],
                'draw_date' => $raffle['draw_date'],
                'draw_location' => $raffle['draw_location'],
                'contact_phone' => $raffle['contact_phone'],
                'app_url' => Environment::get('APP_URL')
            ]);

            $this->mailer->Body = $body;

            return $this->mailer->send();

        } catch (Exception $e) {
            error_log("Erro ao enviar email de confirmação de pagamento: " . $e->getMessage());
            return false;
        }
    }

    public function sendAdminNotification(string $subject, string $message): bool
    {
        try {
            $admins = \App\Models\User::getAdmins();
            
            foreach ($admins as $admin) {
                $this->mailer->clearAddresses();
                $this->mailer->addAddress($admin['email'], $admin['name']);

                $this->mailer->isHTML(true);
                $this->mailer->Subject = '[Admin] ' . $subject;

                $body = $this->getEmailTemplate('admin_notification', [
                    'admin_name' => $admin['name'],
                    'subject' => $subject,
                    'message' => $message,
                    'app_url' => Environment::get('APP_URL')
                ]);

                $this->mailer->Body = $body;
                $this->mailer->send();
            }

            return true;

        } catch (Exception $e) {
            error_log("Erro ao enviar notificação para admins: " . $e->getMessage());
            return false;
        }
    }

    private function getEmailTemplate(string $template, array $data): string
    {
        $templates = [
            'reservation_confirmation' => '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Reserva Confirmada</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
                        .header { background: #3b82f6; color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; }
                        .info-box { background: #f8f9fa; border-left: 4px solid #3b82f6; padding: 15px; margin: 20px 0; }
                        .pix-box { background: #dcfce7; border: 1px solid #16a34a; border-radius: 8px; padding: 20px; margin: 20px 0; }
                        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
                        .btn { display: inline-block; background: #3b82f6; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>🎫 Reserva Confirmada!</h1>
                    </div>
                    
                    <div class="content">
                        <p>Olá <strong>' . htmlspecialchars($data['customer_name']) . '</strong>,</p>
                        
                        <p>Sua reserva foi confirmada com sucesso! Veja os detalhes abaixo:</p>
                        
                        <div class="info-box">
                            <h3>📋 Detalhes da Reserva</h3>
                            <p><strong>Rifa:</strong> ' . htmlspecialchars($data['raffle_title']) . '</p>
                            <p><strong>Números:</strong> ' . htmlspecialchars($data['numbers']) . '</p>
                            <p><strong>Valor Total:</strong> R$ ' . number_format($data['total_amount'], 2, ',', '.') . '</p>
                            <p><strong>ID da Reserva:</strong> #' . $data['reservation_id'] . '</p>
                        </div>
                        
                        <div class="pix-box">
                            <h3>💳 Instruções de Pagamento PIX</h3>
                            <p><strong>Para concluir sua participação, realize o pagamento via PIX:</strong></p>
                            <p><strong>Chave PIX:</strong> ' . htmlspecialchars($data['pix_key']) . '</p>
                            <p><strong>Favorecido:</strong> ' . htmlspecialchars($data['pix_owner']) . '</p>
                            <p><strong>Valor:</strong> R$ ' . number_format($data['total_amount'], 2, ',', '.') . '</p>
                            
                            <p><strong>⚠️ IMPORTANTE:</strong> Após realizar o pagamento, envie o comprovante para confirmarmos sua participação.</p>
                        </div>
                        
                        <p>Seus números ficam reservados por um período limitado. Complete o pagamento o quanto antes!</p>
                        
                        <p>Em caso de dúvidas, entre em contato conosco.</p>
                        
                        <p>Boa sorte! 🍀</p>
                    </div>
                    
                    <div class="footer">
                        <p>© ' . date('Y') . ' RifasPro - Plataforma de Rifas Online</p>
                        <p><a href="' . htmlspecialchars($data['app_url']) . '">Acessar Plataforma</a></p>
                    </div>
                </body>
                </html>
            ',

            'payment_confirmation' => '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Pagamento Confirmado</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
                        .header { background: #16a34a; color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; }
                        .success-box { background: #dcfce7; border: 1px solid #16a34a; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
                        .info-box { background: #f8f9fa; border-left: 4px solid #16a34a; padding: 15px; margin: 20px 0; }
                        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>✅ Pagamento Confirmado!</h1>
                    </div>
                    
                    <div class="content">
                        <div class="success-box">
                            <h2>🎉 Parabéns!</h2>
                            <p><strong>Seu pagamento foi confirmado e você está oficialmente participando da rifa!</strong></p>
                        </div>
                        
                        <p>Olá <strong>' . htmlspecialchars($data['customer_name']) . '</strong>,</p>
                        
                        <p>Confirmamos o recebimento do seu pagamento. Agora você está concorrendo aos prêmios!</p>
                        
                        <div class="info-box">
                            <h3>🎫 Seus Números da Sorte</h3>
                            <p><strong>Rifa:</strong> ' . htmlspecialchars($data['raffle_title']) . '</p>
                            <p><strong>Números:</strong> ' . htmlspecialchars($data['numbers']) . '</p>
                            <p><strong>Valor Pago:</strong> R$ ' . number_format($data['total_amount'], 2, ',', '.') . '</p>
                            <p><strong>ID da Reserva:</strong> #' . $data['reservation_id'] . '</p>
                        </div>
                        
                        ' . ($data['draw_date'] ? '
                        <div class="info-box">
                            <h3>📅 Informações do Sorteio</h3>
                            <p><strong>Data:</strong> ' . htmlspecialchars($data['draw_date']) . '</p>
                            ' . ($data['draw_location'] ? '<p><strong>Local:</strong> ' . htmlspecialchars($data['draw_location']) . '</p>' : '') . '
                            ' . ($data['contact_phone'] ? '<p><strong>Contato:</strong> ' . htmlspecialchars($data['contact_phone']) . '</p>' : '') . '
                        </div>
                        ' : '') . '
                        
                        <p><strong>Guarde este email!</strong> Ele serve como comprovante da sua participação.</p>
                        
                        <p>Boa sorte no sorteio! 🍀</p>
                    </div>
                    
                    <div class="footer">
                        <p>© ' . date('Y') . ' RifasPro - Plataforma de Rifas Online</p>
                        <p><a href="' . htmlspecialchars($data['app_url']) . '">Acessar Plataforma</a></p>
                    </div>
                </body>
                </html>
            ',

            'admin_notification' => '
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset="UTF-8">
                    <title>Notificação Administrativa</title>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
                        .header { background: #dc2626; color: white; padding: 20px; text-align: center; }
                        .content { padding: 20px; }
                        .alert-box { background: #fef2f2; border: 1px solid #dc2626; border-radius: 8px; padding: 15px; margin: 20px 0; }
                        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
                        .btn { display: inline-block; background: #dc2626; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 10px 0; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>🔔 Notificação Administrativa</h1>
                    </div>
                    
                    <div class="content">
                        <p>Olá <strong>' . htmlspecialchars($data['admin_name']) . '</strong>,</p>
                        
                        <div class="alert-box">
                            <h3>' . htmlspecialchars($data['subject']) . '</h3>
                            <p>' . nl2br(htmlspecialchars($data['message'])) . '</p>
                        </div>
                        
                        <p>Acesse o painel administrativo para mais detalhes:</p>
                        
                        <a href="' . htmlspecialchars($data['app_url']) . '/admin" class="btn">Acessar Painel Admin</a>
                    </div>
                    
                    <div class="footer">
                        <p>© ' . date('Y') . ' RifasPro - Sistema Administrativo</p>
                    </div>
                </body>
                </html>
            '
        ];

        return $templates[$template] ?? '';
    }
}
