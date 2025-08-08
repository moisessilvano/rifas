<?php
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-ticket-alt mr-3 text-primary-600"></i>
                    Rifas Disponíveis
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Escolha sua rifa e boa sorte!
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <form method="GET" class="flex items-center space-x-2">
                    <input type="text" name="search" 
                           value="<?= htmlspecialchars($filters['title'] ?? '') ?>"
                           class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="Buscar rifas...">
                    <button type="submit" 
                            class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Rifas Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($raffles)): ?>
            <div class="col-span-full text-center py-12">
                <i class="fas fa-ticket-alt text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-medium text-gray-500 dark:text-gray-400 mb-2">
                    Nenhuma rifa disponível no momento
                </h3>
                <p class="text-gray-400">
                    Volte mais tarde para novas oportunidades!
                </p>
            </div>
        <?php else: ?>
            <?php foreach ($raffles as $raffle): ?>
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden transition-transform hover:scale-[1.02]">
                    <!-- Imagem -->
                    <div class="relative h-48">
                        <?php if ($raffle['image_path']): ?>
                            <img src="<?= htmlspecialchars($raffle['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($raffle['title']) ?>"
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-4xl"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Badge de Progresso -->
                        <div class="absolute top-2 right-2 px-2 py-1 bg-black/50 rounded text-white text-sm">
                            <?php
                            $total = $raffle['total_numbers'];
                            $sold = $raffle['sold_count'] ?? 0;
                            $reserved = $raffle['reserved_count'] ?? 0;
                            $available = $total - $sold - $reserved;
                            $progress = ($sold + $reserved) / $total * 100;
                            ?>
                            <div class="flex items-center space-x-1">
                                <i class="fas fa-chart-pie text-xs"></i>
                                <span><?= number_format($progress, 0) ?>% ocupado</span>
                            </div>
                        </div>
                    </div>

                    <!-- Conteúdo -->
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            <?= htmlspecialchars($raffle['title']) ?>
                        </h3>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-hashtag w-5"></i>
                                <span><?= number_format($total, 0, ',', '.') ?> números</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-tag w-5"></i>
                                <span>R$ <?= number_format($raffle['price_per_number'], 2, ',', '.') ?> por número</span>
                            </div>
                            <?php if ($raffle['draw_date']): ?>
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-calendar w-5"></i>
                                <span>Sorteio: <?= date('d/m/Y \à\s H:i', strtotime($raffle['draw_date'])) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Status -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                <?= number_format($available, 0, ',', '.') ?> disponíveis
                            </span>
                            <?php if ($reserved > 0): ?>
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">
                                <?= number_format($reserved, 0, ',', '.') ?> reservados
                            </span>
                            <?php endif; ?>
                            <?php if ($sold > 0): ?>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                <?= number_format($sold, 0, ',', '.') ?> vendidos
                            </span>
                            <?php endif; ?>
                        </div>

                        <!-- Barra de Progresso -->
                        <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full mb-4">
                            <div class="h-full bg-primary-600 rounded-full" 
                                 style="width: <?= $progress ?>%"></div>
                        </div>

                        <!-- Ação -->
                        <a href="/raffles/<?= $raffle['id'] ?>" 
                           class="block w-full text-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-ticket-alt mr-2"></i>
                            Escolher Números
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
