<?php
ob_start();
?>

<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full text-center">
        <div class="mb-8">
            <i class="fas fa-search text-gray-400 text-8xl mb-4"></i>
            <h1 class="text-6xl font-bold text-gray-900 dark:text-white mb-4">404</h1>
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-300 mb-4">
                Página não encontrada
            </h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8">
                A página que você está procurando não existe ou foi movida.
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
            
            <button onclick="history.back()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Voltar à página anterior
            </button>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
$title = 'Página não encontrada - 404';
include __DIR__ . '/../layouts/app.php';
?>
