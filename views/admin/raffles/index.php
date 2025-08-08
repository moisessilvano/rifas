<?php
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-ticket mr-3 text-primary-600"></i>
                    Gerenciar Rifas
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Crie, edite e gerencie todas as suas rifas
                </p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="/admin/raffles/create" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Nova Rifa
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Buscar por título
                </label>
                <input type="text" id="title" name="title" 
                       value="<?= htmlspecialchars($filters['title'] ?? '') ?>"
                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                       placeholder="Digite o título da rifa">
            </div>
            
            <div>
                <label for="published" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Status
                </label>
                <select id="published" name="published"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="">Todos</option>
                    <option value="true" <?= ($filters['published'] ?? '') === 'true' ? 'selected' : '' ?>>
                        Publicadas
                    </option>
                    <option value="false" <?= ($filters['published'] ?? '') === 'false' ? 'selected' : '' ?>>
                        Rascunhos
                    </option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" 
                        class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-search mr-2"></i>Filtrar
                </button>
                <a href="/admin/raffles" 
                   class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Raffles List -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
        <?php if (empty($raffles)): ?>
            <div class="text-center py-12">
                <i class="fas fa-ticket text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-medium text-gray-500 dark:text-gray-400 mb-2">
                    Nenhuma rifa encontrada
                </h3>
                <p class="text-gray-400 mb-6">
                    Crie sua primeira rifa para começar a vender números!
                </p>
                <a href="/admin/raffles/create" 
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700">
                    <i class="fas fa-plus mr-2"></i>
                    Criar Primeira Rifa
                </a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Rifa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Números/Valor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Criada em
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach ($raffles as $raffle): ?>
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-12">
                                            <?php if ($raffle['image_path']): ?>
                                                <img class="h-12 w-12 rounded-lg object-cover" 
                                                     src="<?= htmlspecialchars($raffle['image_path']) ?>" 
                                                     alt="<?= htmlspecialchars($raffle['title']) ?>">
                                            <?php else: ?>
                                                <div class="h-12 w-12 rounded-lg bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                                                    <i class="fas fa-image text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                <?= htmlspecialchars($raffle['title']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                por <?= htmlspecialchars($raffle['creator_name']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        <?= $raffle['total_numbers'] ?> números
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        R$ <?= number_format($raffle['price_per_number'], 2, ',', '.') ?> cada
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($raffle['is_published']): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-eye mr-1"></i>Publicada
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-eye-slash mr-1"></i>Rascunho
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    <?= date('d/m/Y H:i', strtotime($raffle['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="/admin/raffles/<?= $raffle['id'] ?>" 
                                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300"
                                           title="Ver detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="/admin/raffles/<?= $raffle['id'] ?>/edit" 
                                           class="text-yellow-600 hover:text-yellow-900 dark:text-yellow-400 dark:hover:text-yellow-300"
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/raffles/<?= $raffle['id'] ?>" target="_blank"
                                           class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300"
                                           title="Ver página pública">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
