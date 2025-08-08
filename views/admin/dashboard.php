<?php
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
            <i class="fas fa-chart-line mr-3 text-primary-600"></i>
            Painel Administrativo
        </h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Visão geral da plataforma de rifas
        </p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Raffles -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-ticket text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Total de Rifas
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                <?= $raffleStats['total_raffles'] ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Published Raffles -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-eye text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Rifas Publicadas
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                <?= $raffleStats['published_raffles'] ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Draft Raffles -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-eye-slash text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="ml-4 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Rascunhos
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                <?= $raffleStats['draft_raffles'] ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-users text-purple-600 text-2xl"></i>
                    </div>
                    <div class="ml-4 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                Total de Usuários
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900 dark:text-white">
                                <?= $userStats['total'] ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                <i class="fas fa-lightning-bolt mr-2 text-yellow-500"></i>
                Ações Rápidas
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="/admin/raffles/create" 
                   class="flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Nova Rifa
                </a>
                
                <a href="/admin/reservations?status=reserved" 
                   class="flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-clock mr-2"></i>
                    Pagamentos Pendentes
                </a>
                
                <a href="/admin/export/reservations" 
                   class="flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Exportar Relatório
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Raffles -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    <i class="fas fa-ticket mr-2 text-blue-600"></i>
                    Rifas Recentes
                </h3>
            </div>
            <div class="p-6">
                <?php if (empty($recentRaffles)): ?>
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                        Nenhuma rifa encontrada
                    </p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recentRaffles as $raffle): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?= htmlspecialchars($raffle['title']) ?>
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <?= $raffle['total_numbers'] ?> números • 
                                        R$ <?= number_format($raffle['price_per_number'], 2, ',', '.') ?>
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <?php if ($raffle['is_published']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Publicada
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Rascunho
                                        </span>
                                    <?php endif; ?>
                                    <a href="/admin/raffles/<?= $raffle['id'] ?>" 
                                       class="text-primary-600 hover:text-primary-500">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="/admin/raffles" 
                           class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                            Ver todas as rifas →
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pending Reservations -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    <i class="fas fa-clock mr-2 text-orange-600"></i>
                    Pagamentos Pendentes
                </h3>
            </div>
            <div class="p-6">
                <?php if (empty($pendingReservations)): ?>
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4">
                        Nenhum pagamento pendente
                    </p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($pendingReservations as $reservation): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?= htmlspecialchars($reservation['customer_email']) ?>
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <?= htmlspecialchars($reservation['raffle_title']) ?> • 
                                        R$ <?= number_format($reservation['total_amount'], 2, ',', '.') ?>
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Pendente
                                    </span>
                                    <a href="/admin/reservations/<?= $reservation['id'] ?>" 
                                       class="text-primary-600 hover:text-primary-500">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 text-center">
                        <a href="/admin/reservations?status=reserved" 
                           class="text-primary-600 hover:text-primary-500 text-sm font-medium">
                            Ver todas as reservas →
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
