<?php
ob_start();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="raffleApp()" x-init="init()">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-ticket-alt mr-3 text-primary-600"></i>
                    <?= htmlspecialchars($raffle['title']) ?>
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Escolha seus números da sorte
                </p>
            </div>
            <a href="/raffles" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Informações da Rifa -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Imagem -->
            <?php if ($raffle['image_path']): ?>
                <img src="<?= htmlspecialchars($raffle['image_path']) ?>" 
                     alt="<?= htmlspecialchars($raffle['title']) ?>"
                     class="w-full rounded-lg shadow-lg">
            <?php endif; ?>

            <!-- Detalhes -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-info-circle mr-2 text-blue-600"></i>
                    Informações
                </h2>

                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Descrição</h3>
                        <p class="mt-1 text-gray-900 dark:text-white whitespace-pre-line">
                            <?= nl2br(htmlspecialchars($raffle['description'])) ?>
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Valor por Número</h3>
                        <p class="mt-1 text-2xl font-bold text-primary-600">
                            R$ <?= number_format($raffle['price_per_number'], 2, ',', '.') ?>
                        </p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Total de Números</h3>
                        <p class="mt-1 text-gray-900 dark:text-white">
                            <?= number_format($raffle['total_numbers'], 0, ',', '.') ?> números
                        </p>
                    </div>

                    <?php if ($raffle['draw_date']): ?>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Data do Sorteio</h3>
                        <p class="mt-1 text-gray-900 dark:text-white">
                            <?= date('d/m/Y \à\s H:i', strtotime($raffle['draw_date'])) ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if ($raffle['draw_location']): ?>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Local do Sorteio</h3>
                        <p class="mt-1 text-gray-900 dark:text-white">
                            <?= htmlspecialchars($raffle['draw_location']) ?>
                        </p>
                    </div>
                    <?php endif; ?>

                    <?php if ($raffle['contact_phone']): ?>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Contato</h3>
                        <p class="mt-1 text-gray-900 dark:text-white">
                            <?= htmlspecialchars($raffle['contact_phone']) ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Estatísticas -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-chart-pie mr-2 text-green-600"></i>
                    Estatísticas
                </h2>

                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            <?= number_format($stats['available'], 0, ',', '.') ?>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Disponíveis
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            <?= number_format($stats['reserved'], 0, ',', '.') ?>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Reservados
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">
                            <?= number_format($stats['paid'], 0, ',', '.') ?>
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Pagos
                        </div>
                    </div>
                </div>

                <?php
                $progress = ($stats['reserved'] + $stats['paid']) / $stats['total_numbers'] * 100;
                ?>
                <div class="mt-4">
                    <div class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-full">
                        <div class="h-full bg-primary-600 rounded-full" 
                             style="width: <?= $progress ?>%"></div>
                    </div>
                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400 text-center">
                        <?= number_format($progress, 1) ?>% ocupado
                    </div>
                </div>
            </div>
        </div>

        <!-- Seleção de Números -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                    <i class="fas fa-th mr-2 text-purple-600"></i>
                    Escolha seus Números
                </h2>

                <!-- Ferramentas de Seleção -->
                <div class="mb-6 space-y-4">
                    <!-- Seleção Rápida -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Seleção Rápida</h3>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" @click="selectRandomNumbers(1)" 
                                    class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded transition-colors">
                                1 Aleatório
                            </button>
                            <button type="button" @click="selectRandomNumbers(5)" 
                                    class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded transition-colors">
                                5 Aleatórios
                            </button>
                            <button type="button" @click="selectRandomNumbers(10)" 
                                    class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded transition-colors">
                                10 Aleatórios
                            </button>
                            <button type="button" @click="clearSelection()" 
                                    class="px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs rounded transition-colors">
                                Limpar Tudo
                            </button>
                        </div>
                    </div>

                    <!-- Busca de Número -->
                    <div class="flex items-center space-x-2">
                        <input type="number" x-model="searchNumber" 
                               placeholder="Digite um número..." 
                               min="1" max="<?= $raffle['total_numbers'] ?>"
                               class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <button type="button" @click="goToNumber()" 
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                            Buscar
                        </button>
                    </div>

                    <!-- Filtros -->
                    <div class="flex flex-wrap gap-2">
                        <button type="button" @click="filter = 'all'" 
                                :class="filter === 'all' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'"
                                class="px-3 py-1 text-xs rounded transition-colors">
                            Todos
                        </button>
                        <button type="button" @click="filter = 'available'" 
                                :class="filter === 'available' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'"
                                class="px-3 py-1 text-xs rounded transition-colors">
                            Disponíveis
                        </button>
                        <button type="button" @click="filter = 'selected'" 
                                :class="filter === 'selected' ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-700'"
                                class="px-3 py-1 text-xs rounded transition-colors">
                            Selecionados
                        </button>
                    </div>
                </div>

                <!-- Legenda -->
                <div class="flex flex-wrap gap-4 mb-4">
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-white border border-gray-300 rounded mr-2"></div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Disponível</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-yellow-100 border border-yellow-300 rounded mr-2"></div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Reservado</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-green-100 border border-green-300 rounded mr-2"></div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Pago</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-4 h-4 bg-primary-100 border border-primary-300 rounded mr-2"></div>
                        <span class="text-xs text-gray-600 dark:text-gray-400">Selecionado</span>
                    </div>
                </div>

                <!-- Legenda -->
                <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700 mb-4">
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Legenda</h3>
                    <div class="flex flex-wrap gap-4 text-xs">
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-white border border-gray-300 rounded text-gray-700 flex items-center justify-center">•</div>
                            <span class="text-gray-600 dark:text-gray-400">Disponível</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-primary-100 border border-primary-300 rounded text-primary-800 flex items-center justify-center font-bold">•</div>
                            <span class="text-gray-600 dark:text-gray-400">Selecionado</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-yellow-100 border border-yellow-400 rounded text-yellow-800 opacity-75 line-through flex items-center justify-center">•</div>
                            <span class="text-gray-600 dark:text-gray-400">Reservado</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-6 h-6 bg-green-100 border border-green-400 rounded text-green-800 opacity-75 line-through font-bold flex items-center justify-center">•</div>
                            <span class="text-gray-600 dark:text-gray-400">Pago</span>
                        </div>
                    </div>
                </div>

                <!-- Grid de Números com Scroll -->
                <div class="border rounded-lg overflow-hidden" style="max-height: 400px; overflow-y: auto;">
                    <div class="grid grid-cols-10 gap-1 p-4" id="numbers-grid">
                        <template x-for="number in filteredNumbers" :key="number.number">
                            <button type="button"
                                    :id="`number-${number.number}`"
                                    class="aspect-square rounded border flex items-center justify-center text-xs font-medium transition-all duration-200"
                                    :class="getButtonClass(number)"
                                    :disabled="number.status !== 'available'"
                                    @click="toggleNumber(number.number)">
                                <span x-text="number.number"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Números Selecionados -->
                <div class="mt-6">
                    <h3 class="text-md font-medium text-gray-900 dark:text-white mb-3">
                        Números Selecionados
                    </h3>
                    <div class="flex flex-wrap gap-2 mb-4" x-show="selectedNumbers.length > 0">
                        <template x-for="number in selectedNumbers" :key="number">
                            <span class="px-2 py-1 bg-primary-100 text-primary-800 rounded text-sm flex items-center">
                                <span x-text="number"></span>
                                <button type="button" @click="removeNumber(number)" 
                                        class="ml-1 text-primary-600 hover:text-primary-800">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </span>
                        </template>
                    </div>
                    <p x-show="selectedNumbers.length === 0" 
                       class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                        Nenhum número selecionado
                    </p>

                    <!-- Formulário de Reserva -->
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Seu Nome *
                                </label>
                                <input type="text" id="customer_name" name="customer_name" required
                                       x-model="formData.customer_name"
                                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       placeholder="Digite seu nome completo">
                            </div>

                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Seu Email *
                                </label>
                                <input type="email" id="customer_email" name="customer_email" required
                                       x-model="formData.customer_email"
                                       class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       placeholder="Digite seu email">
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span x-text="selectedNumbers.length"></span> números selecionados
                                    </p>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        Total: R$ <span x-text="formatCurrency(totalAmount)"></span>
                                    </p>
                                </div>
                                <button type="button"
                                        @click="showConfirmModal()"
                                        :disabled="selectedNumbers.length === 0 || !formData.customer_name || !formData.customer_email"
                                        class="px-6 py-2 bg-primary-600 hover:bg-primary-700 disabled:bg-gray-400 text-white rounded-lg transition-colors">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Reservar Números
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Formulário oculto para envio -->
                    <form x-ref="reservationForm" action="/raffles/<?= $raffle['id'] ?>/reserve" method="POST" style="display: none;">
                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                        <input type="hidden" name="customer_name" x-bind:value="formData.customer_name">
                        <input type="hidden" name="customer_email" x-bind:value="formData.customer_email">
                        <input type="hidden" name="confirmed" value="1">
                        <template x-for="number in selectedNumbers" :key="number">
                            <input type="hidden" name="numbers[]" x-bind:value="number">
                        </template>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function raffleApp() {
    return {
        numbers: <?= json_encode($numbers) ?>,
        selectedNumbers: [],
        filter: 'all',
        searchNumber: '',
        formData: {
            customer_name: '',
            customer_email: ''
        },
        isSubmitting: false,
        pricePerNumber: <?= $raffle['price_per_number'] ?>,

        init() {
            console.log('Raffle app initialized');
        },

        get filteredNumbers() {
            let filtered = this.numbers;
            
            if (this.filter === 'available') {
                filtered = filtered.filter(n => n.status === 'available');
            } else if (this.filter === 'selected') {
                filtered = filtered.filter(n => this.isSelected(n.number));
            }
            
            return filtered;
        },

        get totalAmount() {
            return this.selectedNumbers.length * this.pricePerNumber;
        },

        isSelected(number) {
            return this.selectedNumbers.includes(number);
        },

        getButtonClass(number) {
            if (this.isSelected(number.number)) {
                return 'bg-primary-100 border-primary-300 text-primary-800 ring-2 ring-primary-300 font-bold';
            }
            
            switch(number.status) {
                case 'available':
                    return 'bg-white hover:bg-primary-50 border-gray-300 cursor-pointer text-gray-700 hover:border-primary-300 hover:shadow-md transition-all';
                case 'reserved':
                    return 'bg-yellow-100 border-yellow-400 cursor-not-allowed text-yellow-800 opacity-75 line-through';
                case 'paid':
                    return 'bg-green-100 border-green-400 cursor-not-allowed text-green-800 opacity-75 line-through font-bold';
                default:
                    return 'bg-gray-100 border-gray-300 cursor-not-allowed text-gray-500 opacity-50';
            }
        },

        toggleNumber(number) {
            // Verificar se o número está disponível
            const numberData = this.numbers.find(n => n.number === number);
            if (numberData && numberData.status !== 'available') {
                return; // Não permite selecionar números reservados ou pagos
            }
            
            const index = this.selectedNumbers.indexOf(number);
            if (index === -1) {
                this.selectedNumbers.push(number);
            } else {
                this.selectedNumbers.splice(index, 1);
            }
            this.selectedNumbers.sort((a, b) => a - b);
        },

        removeNumber(number) {
            const index = this.selectedNumbers.indexOf(number);
            if (index !== -1) {
                this.selectedNumbers.splice(index, 1);
            }
        },

        selectRandomNumbers(count) {
            const available = this.numbers.filter(n => 
                n.status === 'available' && !this.isSelected(n.number)
            );
            
            const toSelect = Math.min(count, available.length);
            
            for (let i = 0; i < toSelect; i++) {
                const randomIndex = Math.floor(Math.random() * available.length);
                const number = available.splice(randomIndex, 1)[0];
                this.selectedNumbers.push(number.number);
            }
            
            this.selectedNumbers.sort((a, b) => a - b);
        },

        clearSelection() {
            this.selectedNumbers = [];
        },

        goToNumber() {
            if (this.searchNumber) {
                const element = document.getElementById(`number-${this.searchNumber}`);
                if (element) {
                    element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    element.classList.add('ring-4', 'ring-blue-500');
                    setTimeout(() => {
                        element.classList.remove('ring-4', 'ring-blue-500');
                    }, 2000);
                }
            }
        },

        formatCurrency(value) {
            return value.toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        },

        async submitReservation() {
            if (this.selectedNumbers.length === 0) return;
            
            this.isSubmitting = true;
            
            try {
                try {
                    const response = await fetch('/raffles/<?= $raffle['id'] ?>/reserve', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            csrf_token: document.querySelector('input[name="csrf_token"]').value,
                            customer_name: this.formData.customer_name,
                            customer_email: this.formData.customer_email,
                            numbers: this.selectedNumbers
                        })
                    });
                    
                    let data;
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        data = await response.json();
                    } else {
                        throw new Error('Resposta inválida do servidor');
                    }
                    
                    if (response.ok) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Números Reservados!',
                            text: 'Enviamos as instruções de pagamento para seu email.',
                            confirmButtonText: 'Ok',
                            confirmButtonColor: '#2563eb'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        throw new Error(data.message || 'Erro ao reservar números');
                    }
                } catch (error) {
                    if (error.name === 'SyntaxError') {
                        error = new Error('Erro ao processar resposta do servidor');
                    }
                    throw error;
                }
                
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Ops!',
                    text: error.message || 'Erro ao processar sua reserva. Tente novamente.',
                    confirmButtonText: 'Ok',
                    confirmButtonColor: '#2563eb'
                });
            } finally {
                this.isSubmitting = false;
            }
        },

        showConfirmModal() {
            // Validações básicas
            if (this.selectedNumbers.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selecione números',
                    text: 'Por favor, selecione pelo menos um número.',
                    confirmButtonText: 'Ok',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            if (!this.formData.customer_name || !this.formData.customer_email) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Dados obrigatórios',
                    text: 'Por favor, preencha seu nome e email.',
                    confirmButtonText: 'Ok',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            // Validar email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(this.formData.customer_email)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Email inválido',
                    text: 'Por favor, digite um email válido.',
                    confirmButtonText: 'Ok',
                    confirmButtonColor: '#2563eb'
                });
                return;
            }

            // Mostrar modal de confirmação
            const numbersText = this.selectedNumbers.sort((a, b) => a - b).join(', ');
            const totalValue = this.formatCurrency(this.totalAmount);

            Swal.fire({
                title: 'Confirmar Reserva',
                html: `
                    <div class="text-left space-y-3">
                        <div>
                            <strong>Nome:</strong> ${this.formData.customer_name}
                        </div>
                        <div>
                            <strong>Email:</strong> ${this.formData.customer_email}
                        </div>
                        <div>
                            <strong>Números:</strong> ${numbersText}
                        </div>
                        <div class="border-t pt-3 mt-3">
                            <strong>Total: R$ ${totalValue}</strong>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Sim, Reservar!',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#6b7280'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.$refs.reservationForm.submit();
                }
            });
        }
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>