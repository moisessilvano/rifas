<?php
/**
 * Script de instalação automática da Plataforma de Rifas
 * Execute este arquivo no navegador para configurar automaticamente o sistema
 */

// Verificar se já foi instalado
if (file_exists(__DIR__ . '/config.env') && file_exists(__DIR__ . '/.installed')) {
    die('<h1>Sistema já instalado!</h1><p>Se deseja reinstalar, delete os arquivos config.env e .installed</p>');
}

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

// Processar formulários
if ($_POST) {
    switch ($step) {
        case 2:
            // Verificar conexão com banco
            try {
                $pdo = new PDO(
                    "mysql:host={$_POST['db_host']};charset=utf8mb4",
                    $_POST['db_user'],
                    $_POST['db_password']
                );
                
                // Criar banco se não existir
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$_POST['db_name']}`");
                $pdo->exec("USE `{$_POST['db_name']}`");
                
                // Executar schema
                $schema = file_get_contents(__DIR__ . '/database/schema.sql');
                $schema = str_replace('CREATE DATABASE IF NOT EXISTS rifa_platform;', '', $schema);
                $schema = str_replace('USE rifa_platform;', '', $schema);
                
                $statements = explode(';', $schema);
                foreach ($statements as $statement) {
                    $statement = trim($statement);
                    if (!empty($statement)) {
                        $pdo->exec($statement);
                    }
                }
                
                $_SESSION['db_config'] = $_POST;
                $step = 3;
                
            } catch (Exception $e) {
                $error = 'Erro de conexão: ' . $e->getMessage();
            }
            break;
            
        case 3:
            // Salvar configurações
            $config = "# Database Configuration
DB_HOST={$_SESSION['db_config']['db_host']}
DB_NAME={$_SESSION['db_config']['db_name']}
DB_USER={$_SESSION['db_config']['db_user']}
DB_PASSWORD={$_SESSION['db_config']['db_password']}

# Application Configuration
APP_URL={$_POST['app_url']}
APP_ENV=production
APP_DEBUG=false

# Email Configuration
MAIL_HOST={$_POST['mail_host']}
MAIL_PORT={$_POST['mail_port']}
MAIL_USERNAME={$_POST['mail_username']}
MAIL_PASSWORD={$_POST['mail_password']}
MAIL_FROM={$_POST['mail_from']}
MAIL_FROM_NAME=\"{$_POST['mail_from_name']}\"

# PIX Configuration
PIX_KEY={$_POST['pix_key']}
PIX_OWNER_NAME=\"{$_POST['pix_owner_name']}\"

# Security
SESSION_SECRET=" . bin2hex(random_bytes(32));

            file_put_contents(__DIR__ . '/config.env', $config);
            file_put_contents(__DIR__ . '/.installed', date('Y-m-d H:i:s'));
            
            $step = 4;
            break;
    }
}

session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalação - RifasPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <i class="fas fa-ticket text-blue-600 text-6xl mb-4"></i>
            <h2 class="text-3xl font-extrabold text-gray-900">Instalação RifasPro</h2>
            <p class="mt-2 text-sm text-gray-600">Configure sua plataforma de rifas</p>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <?php for ($i = 1; $i <= 4; $i++): ?>
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 <?= $i <= $step ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600' ?> rounded-full text-sm font-medium">
                            <?= $i ?>
                        </div>
                        <?php if ($i < 4): ?>
                            <div class="w-12 h-1 <?= $i < $step ? 'bg-blue-600' : 'bg-gray-300' ?> mx-2"></div>
                        <?php endif; ?>
                    </div>
                <?php endfor; ?>
            </div>
            <div class="text-xs text-gray-500 text-center">
                Passo <?= $step ?> de 4
            </div>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow p-6">
            <?php switch ($step): 
                case 1: ?>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bem-vindo!</h3>
                    <p class="text-gray-600 mb-6">
                        Este assistente irá configurar sua plataforma de rifas. 
                        Certifique-se de ter as seguintes informações:
                    </p>
                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1 mb-6">
                        <li>Dados de conexão MySQL</li>
                        <li>Configurações de email SMTP</li>
                        <li>Chave PIX para recebimentos</li>
                    </ul>
                    <a href="?step=2" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-center block">
                        Começar Instalação
                    </a>
                <?php break; 
                
                case 2: ?>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Configuração do Banco de Dados</h3>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Host do Banco</label>
                            <input type="text" name="db_host" value="localhost" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome do Banco</label>
                            <input type="text" name="db_name" value="rifa_platform" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Usuário</label>
                            <input type="text" name="db_user" value="root" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Senha</label>
                            <input type="password" name="db_password"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                            Testar Conexão e Criar Tabelas
                        </button>
                    </form>
                <?php break; 
                
                case 3: ?>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Configurações da Aplicação</h3>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">URL da Aplicação</label>
                            <input type="url" name="app_url" value="<?= 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) ?>" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <hr>
                        <h4 class="font-medium text-gray-900">Configurações de Email</h4>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Host SMTP</label>
                            <input type="text" name="mail_host" value="smtp.gmail.com" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Porta SMTP</label>
                            <input type="number" name="mail_port" value="587" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email/Usuário SMTP</label>
                            <input type="email" name="mail_username" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Senha SMTP</label>
                            <input type="password" name="mail_password" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email Remetente</label>
                            <input type="email" name="mail_from" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome do Remetente</label>
                            <input type="text" name="mail_from_name" value="RifasPro" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <hr>
                        <h4 class="font-medium text-gray-900">Configurações PIX</h4>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Chave PIX</label>
                            <input type="text" name="pix_key" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="seu-email@exemplo.com ou CPF">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nome do Titular PIX</label>
                            <input type="text" name="pix_owner_name" required
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                            Salvar Configurações
                        </button>
                    </form>
                <?php break; 
                
                case 4: ?>
                    <div class="text-center">
                        <i class="fas fa-check-circle text-green-600 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Instalação Concluída!</h3>
                        <p class="text-gray-600 mb-6">
                            Sua plataforma de rifas foi configurada com sucesso!
                        </p>
                        
                        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left">
                            <h4 class="font-medium text-gray-900 mb-2">Credenciais de Administrador:</h4>
                            <p class="text-sm text-gray-600">
                                <strong>Email:</strong> admin@rifas.com<br>
                                <strong>Senha:</strong> password
                            </p>
                            <p class="text-xs text-yellow-600 mt-2">
                                ⚠️ Altere essas credenciais após o primeiro login!
                            </p>
                        </div>
                        
                        <div class="space-y-3">
                            <a href="/admin/login" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded block">
                                <i class="fas fa-user-shield mr-2"></i>Acessar Painel Admin
                            </a>
                            <a href="/" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded block">
                                <i class="fas fa-home mr-2"></i>Ver Site Público
                            </a>
                        </div>
                        
                        <p class="text-xs text-gray-500 mt-4">
                            Você pode deletar o arquivo install.php por segurança.
                        </p>
                    </div>
                <?php break; 
            endswitch; ?>
        </div>
    </div>
</body>
</html>
