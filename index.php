<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Router;
use App\Core\Database;
use App\Core\Session;
use App\Core\Environment;

// Carregar variáveis de ambiente
Environment::load();

// Inicializar sessão
Session::start();

// Configurar tratamento de erros
error_reporting(E_ALL);
ini_set('display_errors', Environment::get('APP_DEBUG', false) ? 1 : 0);

// Configurar timezone
date_default_timezone_set('America/Sao_Paulo');

try {
    // Inicializar roteador
    $router = new Router();
    
    // Definir rotas
    require_once __DIR__ . '/routes/web.php';
    
    // Processar requisição
    $router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
    
} catch (Exception $e) {
    if (Environment::get('APP_DEBUG', false)) {
        echo "<h1>Erro:</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    } else {
        http_response_code(500);
        include __DIR__ . '/views/errors/500.php';
    }
}
