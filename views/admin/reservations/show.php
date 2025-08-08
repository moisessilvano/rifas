<?php
ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-clipboard-list mr-3 text-primary-600"></i>
                    Reserva #<?= $reservation['id'] ?>
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Detalhes da reserva
                </p>
            </div>
            <div class="flex items-center space-x-2">
                <a href="/admin/reservations" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Voltar
                </a>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
        <!-- Status Banner -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <?php if ($reservation['status'] === 'paid'): ?>
                <div class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                            Pagamento Confirmado
                        </h3>
                        <p class="text-sm text-green-700 dark:text-green-300">
                            Esta reserva foi paga em <?= date('d/m/Y \à\s H:i', strtotime($reservation['paid_at'])) ?>
                        </p>
                    </div>
                </div>
            <?php elseif ($reservation['status'] === 'reserved'): ?>
                <div class="flex items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                    <i class="fas fa-clock text-yellow-600 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                            Aguardando Pagamento
                        </h3>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">
                            Reserva feita em <?= date('d/m/Y \à\s H:i', strtotime($reservation['created_at'])) ?>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="flex items-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                    <i class="fas fa-times-circle text-red-600 text-xl mr-3"></i>
                    <div>
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                            Reserva Cancelada
                        </h3>
                        <p class="text-sm text-red-700 dark:text-red-300">
                            Esta reserva foi cancelada
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Informações da Reserva -->
        <div class="px-6 py-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Dados do Cliente -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-user mr-2 text-blue-600"></i>
                        Dados do Cliente
                    </h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Nome</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <?= htmlspecialchars($reservation['customer_name']) ?>
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Email</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <?= htmlspecialchars($reservation['customer_email']) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Dados da Rifa -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-ticket-alt mr-2 text-purple-600"></i>
                        Dados da Rifa
                    </h3>
                    
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Título</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                <?= htmlspecialchars($reservation['raffle_title'] ?? 'Rifa removida') ?>
                            </p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 dark:text-gray-400">Valor Total</label>
                            <p class="mt-1 text-lg font-bold text-gray-900 dark:text-white">
                                R$ <?= number_format($reservation['total_amount'], 2, ',', '.') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Números Selecionados -->
            <div class="mt-8">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-hashtag mr-2 text-green-600"></i>
                    Números Selecionados
                </h3>
                
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <div class="flex flex-wrap gap-2">
                        <?php 
                        $numbers = json_decode($reservation['numbers'], true);
                        foreach ($numbers as $number): 
                        ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800">
                                <?= $number ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        Total: <?= count($numbers) ?> números
                    </p>
                </div>
            </div>

            <!-- Comprovante de Pagamento -->
            <?php if ($reservation['payment_proof_path']): ?>
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-receipt mr-2 text-orange-600"></i>
                        Comprovante de Pagamento
                    </h3>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <img src="<?= htmlspecialchars($reservation['payment_proof_path']) ?>" 
                             alt="Comprovante de Pagamento" 
                             class="max-w-full h-auto rounded-lg shadow-lg">
                    </div>
                </div>
            <?php endif; ?>

            <!-- Observações -->
            <?php if ($reservation['notes']): ?>
                <div class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>
                        Observações
                    </h3>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm text-gray-900 dark:text-white whitespace-pre-line">
                            <?= nl2br(htmlspecialchars($reservation['notes'])) ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Ações -->
            <?php if ($reservation['status'] === 'reserved'): ?>
                <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                    <div class="flex flex-col sm:flex-row gap-4">
                        <form method="POST" action="/admin/reservations/<?= $reservation['id'] ?>/confirm" class="flex-1">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <button type="submit" 
                                    onclick="return confirm('Confirmar pagamento desta reserva?')"
                                    class="w-full px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-check mr-2"></i>
                                Confirmar Pagamento
                            </button>
                        </form>
                        
                        <form method="POST" action="/admin/reservations/<?= $reservation['id'] ?>/cancel" class="flex-1">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            <button type="submit" 
                                    onclick="return confirm('Cancelar esta reserva? Esta ação não pode ser desfeita.')"
                                    class="w-full px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Cancelar Reserva
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
