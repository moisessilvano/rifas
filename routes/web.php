<?php

use App\Core\Router;

// Rotas públicas
$router->get('/', 'HomeController@index');

// Rotas de autenticação
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');

// Rotas de admin
$router->get('/admin/login', 'AuthController@showAdminLogin');
$router->post('/admin/login', 'AuthController@adminLogin');

// Rotas protegidas - Admin
$router->get('/admin', 'AdminController@dashboard');
$router->get('/admin/raffles', 'AdminController@raffles');
$router->get('/admin/raffles/create', 'AdminController@createRaffle');
$router->post('/admin/raffles', 'AdminController@storeRaffle');
$router->get('/admin/raffles/{id}', 'AdminController@showRaffle');
$router->get('/admin/raffles/{id}/edit', 'AdminController@editRaffle');
$router->post('/admin/raffles/{id}', 'AdminController@updateRaffle');
$router->post('/admin/raffles/{id}/delete', 'AdminController@deleteRaffle');
$router->post('/admin/raffles/{id}/publish', 'AdminController@publishRaffle');
$router->post('/admin/raffles/{id}/unpublish', 'AdminController@unpublishRaffle');

// Gestão de reservas e pagamentos
$router->get('/admin/reservations', 'AdminController@reservations');
$router->get('/admin/reservations/{id}', 'AdminController@showReservation');
$router->post('/admin/reservations/{id}/confirm', 'AdminController@confirmPayment');
$router->post('/admin/reservations/{id}/cancel', 'AdminController@cancelReservation');
$router->get('/admin/export/reservations', 'AdminController@exportReservations');

// Rotas públicas - Rifas
$router->get('/raffles', 'RaffleController@index');
$router->get('/raffles/{id}', 'RaffleController@show');
$router->post('/raffles/{id}/confirm', 'RaffleController@confirm');
$router->post('/raffles/{id}/reserve', 'RaffleController@reserve');
$router->post('/raffles/{id}/cancel', 'RaffleController@cancel');

// Rotas de usuário logado
$router->get('/profile', 'UserController@profile');
$router->post('/profile', 'UserController@updateProfile');
$router->get('/my-reservations', 'UserController@reservations');

// API Routes (JSON responses)
$router->get('/api/raffles/{id}/numbers', 'ApiController@getRaffleNumbers');
$router->post('/api/upload/payment-proof', 'ApiController@uploadPaymentProof');
