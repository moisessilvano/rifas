<?php

namespace App\Core;

abstract class Controller
{
    protected function view(string $view, array $data = []): void
    {
        extract($data);
        include __DIR__ . "/../../views/{$view}.php";
    }

    protected function redirect(string $path): void
    {
        $baseUrl = Environment::get('APP_URL', '');
        header("Location: {$baseUrl}{$path}");
        exit;
    }

    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function requireAuth(): void
    {
        if (!Session::isAuthenticated()) {
            Session::flash('error', 'Você precisa estar logado para acessar esta página.');
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();
        
        if (!Session::isAdmin()) {
            Session::flash('error', 'Acesso negado. Apenas administradores podem acessar esta página.');
            $this->redirect('/');
        }
    }

    protected function validateCsrf(): bool
    {
        $token = $this->getInput('csrf_token');
        $sessionToken = Session::get('csrf_token');
        
        return $token && $sessionToken && hash_equals($sessionToken, $token);
    }

    protected function generateCsrf(): string
    {
        $token = bin2hex(random_bytes(32));
        Session::set('csrf_token', $token);
        return $token;
    }

    protected function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    protected function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    protected function getInput(string $key, $default = null)
    {
        // Primeiro verifica $_POST e $_GET
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        
        // Se não encontrou, tenta ler do JSON body
        static $jsonData = null;
        
        if ($jsonData === null) {
            $input = file_get_contents('php://input');
            if ($input) {
                $jsonData = json_decode($input, true) ?? [];
            } else {
                $jsonData = [];
            }
        }
        
        return $jsonData[$key] ?? $default;
    }
}
