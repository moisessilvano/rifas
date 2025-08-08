<?php
$title = 'Confirmar Reserva - ' . $raffle['title'];
require_once __DIR__ . '/../layouts/app.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">
            Confirmar Reserva
        </h1>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                <?= htmlspecialchars($raffle['title']) ?>
            </h2>

            <div class="space-y-4">
                <!-- Detalhes da Reserva -->
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Números Selecionados
                    </h3>
                    <div class="flex flex-wrap gap-2 mb-4">
                        <?php foreach ($numbers as $number): ?>
                            <span class="px-2 py-1 bg-primary-100 text-primary-800 rounded text-sm">
                                <?= $number ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Nome:</span>
                        <span class="text-gray-900 dark:text-white ml-2"><?= htmlspecialchars($customer_name) ?></span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Email:</span>
                        <span class="text-gray-900 dark:text-white ml-2"><?= htmlspecialchars($customer_email) ?></span>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 dark:text-gray-400">Valor por número:</span>
                        <span class="text-gray-900 dark:text-white">
                            R$ <?= number_format($raffle['price_per_number'], 2, ',', '.') ?>
                        </span>
                    </div>
                    <div class="flex justify-between items-center font-medium mt-2">
                        <span class="text-gray-700 dark:text-gray-300">Total:</span>
                        <span class="text-lg text-gray-900 dark:text-white">
                            R$ <?= number_format($raffle['price_per_number'] * count($numbers), 2, ',', '.') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulário de Confirmação -->
        <div class="flex justify-between">
            <a href="/raffles/<?= $raffle['id'] ?>" 
               class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded transition-colors inline-flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Voltar
            </a>

            <form action="/raffles/<?= $raffle['id'] ?>/reserve" method="POST" class="inline">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                <input type="hidden" name="customer_name" value="<?= htmlspecialchars($customer_name) ?>">
                <input type="hidden" name="customer_email" value="<?= htmlspecialchars($customer_email) ?>">
                <input type="hidden" name="confirmed" value="1">
                <?php foreach ($numbers as $number): ?>
                    <input type="hidden" name="numbers[]" value="<?= $number ?>">
                <?php endforeach; ?>
                <button type="submit" 
                        class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded transition-colors">
                    <i class="fas fa-check mr-2"></i>
                    Confirmar Reserva
                </button>
            </form>
        </div>
    </div>
</div>
