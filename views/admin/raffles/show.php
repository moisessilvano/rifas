<?php
ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-ticket-alt mr-3 text-primary-600"></i>
                    <?= htmlspecialchars($raffle['title']) ?>
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Detalhes e estatísticas da rifa
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="/admin/raffles" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Voltar
                </a>
                <a href="/admin/raffles/<?= $raffle['id'] ?>/edit"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Editar
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
        <!-- Informações Básicas -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                Informações Básicas
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Imagem e Descrição -->
                <div>
                    <?php if ($raffle['image_path']): ?>
                        <img src="<?= htmlspecialchars($raffle['image_path']) ?>" 
                             alt="<?= htmlspecialchars($raffle['title']) ?>"
                             class="w-full h-48 object-cover rounded-lg mb-4">
                    <?php endif; ?>
                    <div class="prose dark:prose-invert max-w-none">
                        <?= nl2br(htmlspecialchars($raffle['description'])) ?>
                    </div>
                </div>

                <!-- Detalhes -->
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Números</h4>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white">
                            <?= number_format($raffle['total_numbers'], 0, ',', '.') ?> números
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor por Número</h4>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white">
                            R$ <?= number_format($raffle['price_per_number'], 2, ',', '.') ?>
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Data do Sorteio</h4>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white">
                            <?= $raffle['draw_date'] ? date('d/m/Y \à\s H:i', strtotime($raffle['draw_date'])) : 'Não definida' ?>
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Local do Sorteio</h4>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white">
                            <?= htmlspecialchars($raffle['draw_location'] ?: 'Não definido') ?>
                        </p>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Contato</h4>
                        <p class="mt-1 text-lg text-gray-900 dark:text-white">
                            <?= htmlspecialchars($raffle['contact_phone'] ?: 'Não definido') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-pie mr-2 text-green-600"></i>
                Estatísticas
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total de Números</h4>
                    <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                        <?= number_format($stats['total_numbers'], 0, ',', '.') ?>
                    </p>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Disponíveis</h4>
                    <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                        <?= number_format($stats['available'], 0, ',', '.') ?>
                    </p>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Reservados</h4>
                    <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                        <?= number_format($stats['reserved'], 0, ',', '.') ?>
                    </p>
                </div>
                
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Pagos</h4>
                    <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                        <?= number_format($stats['paid'], 0, ',', '.') ?>
                    </p>
                </div>
            </div>

            <div class="mt-4 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Receita Total</h4>
                <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">
                    R$ <?= number_format($revenue, 2, ',', '.') ?>
                </p>
            </div>
        </div>

        <!-- Reservas -->
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                <i class="fas fa-list mr-2 text-purple-600"></i>
                Últimas Reservas
            </h3>

            <?php if (empty($reservations)): ?>
                <div class="text-center py-8">
                    <p class="text-gray-500 dark:text-gray-400">
                        Nenhuma reserva realizada ainda.
                    </p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Cliente
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Números
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Valor
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Data
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <?php foreach ($reservations as $reservation): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            <?= htmlspecialchars($reservation['customer_name']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <?= htmlspecialchars($reservation['customer_email']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            <?php 
                                            $numbers = json_decode($reservation['numbers'], true);
                                            echo implode(', ', array_slice($numbers, 0, 5));
                                            if (count($numbers) > 5) echo '...';
                                            ?>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            <?= count($numbers) ?> números
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">
                                            R$ <?= number_format($reservation['total_amount'], 2, ',', '.') ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($reservation['status'] === 'paid'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Pago
                                            </span>
                                        <?php elseif ($reservation['status'] === 'reserved'): ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Reservado
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Cancelado
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <?= date('d/m/Y H:i', strtotime($reservation['created_at'])) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
