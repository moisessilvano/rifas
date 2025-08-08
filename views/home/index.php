<?php
ob_start();
?>

<!-- Hero Section -->
<div class="bg-gradient-to-r from-primary-600 to-primary-800 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
                <i class="fas fa-ticket mr-4"></i>
                RifasPro
            </h1>
            <p class="text-xl md:text-2xl text-primary-100 mb-8 max-w-3xl mx-auto">
                A plataforma mais moderna e segura para rifas online. 
                Participe de rifas incríveis com pagamento via Pix!
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/raffles" 
                   class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-primary-600 bg-white hover:bg-gray-50 transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Ver Todas as Rifas
                </a>
                <?php if (!App\Core\Session::isAuthenticated()): ?>
                    <a href="/register" 
                       class="inline-flex items-center px-8 py-3 border border-white text-base font-medium rounded-lg text-white bg-transparent hover:bg-white hover:text-primary-600 transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>
                        Criar Conta Grátis
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="py-16 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Por que escolher a RifasPro?
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Tecnologia moderna para uma experiência segura e transparente
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full mb-4">
                    <i class="fas fa-shield-alt text-2xl text-primary-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    100% Seguro
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Pagamentos via Pix com comprovação e sistema totalmente criptografado
                </p>
            </div>
            
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full mb-4">
                    <i class="fas fa-clock text-2xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    Rápido e Fácil
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Reserve seus números em segundos e receba confirmação por email
                </p>
            </div>
            
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full mb-4">
                    <i class="fas fa-mobile-alt text-2xl text-purple-600"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    Responsivo
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Funciona perfeitamente em qualquer dispositivo - celular, tablet ou desktop
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Available Raffles -->
<div class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-fire mr-3 text-red-500"></i>
                Rifas em Destaque
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400">
                Participe das rifas mais populares e concorra a prêmios incríveis
            </p>
        </div>
        
        <?php if (empty($raffles)): ?>
            <div class="text-center py-12">
                <i class="fas fa-ticket text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-medium text-gray-500 dark:text-gray-400 mb-2">
                    Nenhuma rifa disponível no momento
                </h3>
                <p class="text-gray-400 mb-6">
                    Novas rifas serão publicadas em breve. Volte sempre!
                </p>
                <?php if (!App\Core\Session::isAuthenticated()): ?>
                    <a href="/register" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700">
                        <i class="fas fa-bell mr-2"></i>
                        Cadastre-se para ser notificado
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($raffles as $raffle): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Image -->
                        <div class="aspect-w-16 aspect-h-9 bg-gray-200 dark:bg-gray-700">
                            <?php if ($raffle['image_path']): ?>
                                <img src="<?= htmlspecialchars($raffle['image_path']) ?>" 
                                     alt="<?= htmlspecialchars($raffle['title']) ?>"
                                     class="w-full h-48 object-cover">
                            <?php else: ?>
                                <div class="flex items-center justify-center h-48">
                                    <i class="fas fa-image text-gray-400 text-4xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                <?= htmlspecialchars($raffle['title']) ?>
                            </h3>
                            
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">
                                <?= htmlspecialchars(substr($raffle['description'], 0, 120)) ?>
                                <?php if (strlen($raffle['description']) > 120): ?>...<?php endif; ?>
                            </p>
                            
                            <!-- Stats -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-primary-600">
                                        <?= $raffle['total_numbers'] ?>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        números
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">
                                        R$ <?= number_format($raffle['price_per_number'], 2, ',', '.') ?>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        por número
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-orange-600">
                                        <?= $raffle['sold_count'] ?>
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        vendidos
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Progress Bar -->
                            <?php 
                            $soldPercentage = ($raffle['sold_count'] / $raffle['total_numbers']) * 100;
                            $reservedPercentage = ($raffle['reserved_count'] / $raffle['total_numbers']) * 100;
                            ?>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-4">
                                <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full relative" 
                                     style="width: <?= $soldPercentage ?>%">
                                    <div class="bg-yellow-500 h-2 rounded-full absolute right-0" 
                                         style="width: <?= $reservedPercentage ?>%"></div>
                                </div>
                            </div>
                            
                            <!-- Action Button -->
                            <a href="/raffles/<?= $raffle['id'] ?>" 
                               class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                                <i class="fas fa-eye mr-2"></i>
                                Ver Rifa
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- View All Button -->
            <div class="text-center mt-12">
                <a href="/raffles" 
                   class="inline-flex items-center px-8 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    <i class="fas fa-th-large mr-2"></i>
                    Ver Todas as Rifas
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-primary-50 dark:bg-gray-800 py-16">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
            Pronto para participar?
        </h2>
        <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">
            Crie sua conta gratuitamente e comece a participar das rifas mais emocionantes!
        </p>
        
        <?php if (!App\Core\Session::isAuthenticated()): ?>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/register" 
                   class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                    <i class="fas fa-user-plus mr-2"></i>
                    Criar Conta Grátis
                </a>
                <a href="/login" 
                   class="inline-flex items-center px-8 py-3 border border-primary-600 text-base font-medium rounded-lg text-primary-600 bg-white hover:bg-primary-50 transition-colors">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Já tenho conta
                </a>
            </div>
        <?php else: ?>
            <a href="/raffles" 
               class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-primary-600 hover:bg-primary-700 transition-colors">
                <i class="fas fa-ticket mr-2"></i>
                Explorar Rifas
            </a>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>
