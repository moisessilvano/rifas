<?php
ob_start();
?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
        <div class="mb-8">
            <i class="fas fa-exclamation-triangle text-red-500 text-8xl mb-4"></i>
            <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-4">500</h1>
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">
                Erro interno do servidor
            </h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8">
                Ocorreu um erro inesperado. Nossa equipe foi notificada e está trabalhando para resolver o problema.
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="/" 
               class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                <i class="fas fa-home mr-2"></i>
                Voltar ao Início
            </a>
            
            <div class="text-sm text-gray-500 dark:text-gray-400">
                <p>ou</p>
            </div>
            
            <button onclick="location.reload()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                <i class="fas fa-redo mr-2"></i>
                Tentar novamente
            </button>
        </div>
        
        <div class="mt-8 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                <strong>Erro ID:</strong> <?= uniqid() ?><br>
                <strong>Horário:</strong> <?= date('d/m/Y H:i:s') ?>
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Erro interno do servidor - 500';
include __DIR__ . '/../layouts/app.php';
?>
