<?php
ob_start();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-plus-circle mr-3 text-primary-600"></i>
                    Nova Rifa
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Preencha os dados para criar uma nova rifa
                </p>
            </div>
            <a href="/admin/raffles" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg">
        <form method="POST" action="/admin/raffles" enctype="multipart/form-data" class="space-y-6 p-6">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <!-- Basic Information -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Informações Básicas
                </h3>
                
                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Título da Rifa *
                        </label>
                        <input type="text" id="title" name="title" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               placeholder="Ex: Rifa do iPhone 15 Pro">
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Descrição *
                        </label>
                        <textarea id="description" name="description" rows="4" required
                                  class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                  placeholder="Descreva os detalhes da rifa, prêmio, condições, etc."></textarea>
                    </div>
                    
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Imagem da Rifa
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-600 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl"></i>
                                <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                    <label for="image" class="relative cursor-pointer bg-white dark:bg-gray-700 rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                        <span>Upload de arquivo</span>
                                        <input id="image" name="image" type="file" accept="image/*" class="sr-only">
                                    </label>
                                    <p class="pl-1">ou arraste e solte</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF até 5MB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Raffle Configuration -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-cogs mr-2 text-green-600"></i>
                    Configuração da Rifa
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="total_numbers" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Quantidade de Números *
                        </label>
                        <input type="number" id="total_numbers" name="total_numbers" min="1" max="10000" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               placeholder="Ex: 100">
                    </div>
                    
                    <div>
                        <label for="price_per_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Valor por Número (R$) *
                        </label>
                        <input type="number" id="price_per_number" name="price_per_number" min="0.01" step="0.01" required
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               placeholder="Ex: 5.00">
                    </div>
                </div>
            </div>
            
            <!-- Draw Information -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-calendar-alt mr-2 text-purple-600"></i>
                    Informações do Sorteio
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="draw_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Data e Hora do Sorteio
                        </label>
                        <input type="datetime-local" id="draw_date" name="draw_date"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               min="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                    
                    <div>
                        <label for="draw_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Local do Sorteio
                        </label>
                        <input type="text" id="draw_location" name="draw_location"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               placeholder="Ex: Sede do clube, Facebook live, etc.">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            Telefone de Contato
                        </label>
                        <input type="tel" id="contact_phone" name="contact_phone"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                               placeholder="Ex: (11) 99999-9999">
                    </div>
                </div>
            </div>
            
            <!-- Publication Settings -->
            <div class="pb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-globe mr-2 text-red-600"></i>
                    Configurações de Publicação
                </h3>
                
                <div class="flex items-center">
                    <input id="is_published" name="is_published" type="checkbox" value="1"
                           class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                    <label for="is_published" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                        Publicar rifa imediatamente (se desmarcado, ficará como rascunho)
                    </label>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                <a href="/admin/raffles" 
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Criar Rifa
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Preview da imagem
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Adicionar preview se necessário
        }
        reader.readAsDataURL(file);
    }
});

// Máscara para telefone
document.getElementById('contact_phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 11) {
        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (value.length >= 7) {
        value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    } else if (value.length >= 3) {
        value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
    }
    e.target.value = value;
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layouts/app.php';
?>
