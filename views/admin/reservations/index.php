<?php
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-clipboard-list mr-3 text-primary-600"></i>
                    Gerenciar Reservas
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Visualize e gerencie todas as reservas de rifas
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="/admin/reservations/export" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Exportar CSV
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Status
                </label>
                <select id="status" name="status"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Todos</option>
                    <option value="reserved" <?= ($filters['status'] ?? '') === 'reserved' ? 'selected' : '' ?>>
                        Reservado
                    </option>
                    <option value="paid" <?= ($filters['status'] ?? '') === 'paid' ? 'selected' : '' ?>>
                        Pago
                    </option>
                    <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>
                        Cancelado
                    </option>
                </select>
            </div>
            
            <div>
                <label for="raffle_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Rifa
                </label>
                <select id="raffle_id" name="raffle_id"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Todas as rifas</option>
                    <?php foreach ($raffles as $raffle): ?>
                        <option value="<?= $raffle['id'] ?>" <?= ($filters['raffle_id'] ?? '') == $raffle['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($raffle['title']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="customer_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Email do Cliente
                </label>
                <input type="email" id="customer_email" name="customer_email" 
                       value="<?= htmlspecialchars($filters['customer_email'] ?? '') ?>"
                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                       placeholder="Digite o email">
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-search mr-2"></i>Filtrar
                </button>
                <a href="/admin/reservations" 
                   class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Reservations List -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
        <?php if (empty($reservations)): ?>
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-medium text-gray-500 dark:text-gray-400 mb-2">
                    Nenhuma reserva encontrada
                </h3>
                <p class="text-gray-400 mb-6">
                    Não há reservas com os filtros selecionados.
                </p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Rifa
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
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($reservations as $reservation): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        #<?= $reservation['id'] ?>
                                    </div>
                                </td>
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
                                        <?= htmlspecialchars($reservation['raffle_title'] ?? 'Rifa removida') ?>
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
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        R$ <?= number_format($reservation['total_amount'], 2, ',', '.') ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($reservation['status'] === 'paid'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>Pago
                                        </span>
                                    <?php elseif ($reservation['status'] === 'reserved'): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>Reservado
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fas fa-times-circle mr-1"></i>Cancelado
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <?= date('d/m/Y H:i', strtotime($reservation['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="/admin/reservations/<?= $reservation['id'] ?>" 
                                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                           title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <?php if ($reservation['status'] === 'reserved'): ?>
                                            <form method="POST" action="/admin/reservations/<?= $reservation['id'] ?>/confirm" 
                                                  style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                                                <button type="submit" 
                                                        class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                                        title="Confirmar pagamento"
                                                        onclick="return confirm('Confirmar pagamento desta reserva?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" action="/admin/reservations/<?= $reservation['id'] ?>/cancel" 
                                                  style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?? '' ?>">
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                                                        title="Cancelar reserva"
                                                        onclick="return confirm('Cancelar esta reserva?')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Statistics -->
    <?php if (!empty($reservations)): ?>
        <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
            <?php
            $stats = [
                'total' => count($reservations),
                'reserved' => count(array_filter($reservations, fn($r) => $r['status'] === 'reserved')),
                'paid' => count(array_filter($reservations, fn($r) => $r['status'] === 'paid')),
                'cancelled' => count(array_filter($reservations, fn($r) => $r['status'] === 'cancelled'))
            ];
            $totalAmount = array_sum(array_map(fn($r) => $r['status'] === 'paid' ? $r['total_amount'] : 0, $reservations));
            ?>
            
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-list text-gray-400 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Total de Reservas
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    <?= $stats['total'] ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-yellow-400 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Pendentes
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    <?= $stats['reserved'] ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Pagas
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    <?= $stats['paid'] ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-money-bill-wave text-green-400 text-2xl"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">
                                    Total Arrecadado
                                </dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                    R$ <?= number_format($totalAmount, 2, ',', '.') ?>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
