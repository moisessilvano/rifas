<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Session::isAuthenticated()) {
            $redirectPath = Session::isAdmin() ? '/admin' : '/';
            $this->redirect($redirectPath);
        }

        $this->view('auth/login', [
            'title' => 'Login',
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    public function login(): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('error', 'Token de segurança inválido.');
            $this->redirect('/login');
        }

        $email = $this->sanitize($this->getInput('email'));
        $password = $this->getInput('password');

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email e senha são obrigatórios.');
            $this->redirect('/login');
        }

        if (!$this->validateEmail($email)) {
            Session::flash('error', 'Email inválido.');
            $this->redirect('/login');
        }

        $user = User::findByEmail($email);
        
        if (!$user || !User::verifyPassword($password, $user['password'])) {
            Session::flash('error', 'Email ou senha incorretos.');
            $this->redirect('/login');
        }

        // Remove senha dos dados da sessão
        unset($user['password']);
        
        Session::login($user);
        Session::flash('success', 'Login realizado com sucesso!');

        $redirectPath = $user['role'] === 'admin' ? '/admin' : '/';
        $this->redirect($redirectPath);
    }

    public function showRegister(): void
    {
        if (Session::isAuthenticated()) {
            $this->redirect('/');
        }

        $this->view('auth/register', [
            'title' => 'Cadastro',
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    public function register(): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('error', 'Token de segurança inválido.');
            $this->redirect('/register');
        }

        $name = $this->sanitize($this->getInput('name'));
        $email = $this->sanitize($this->getInput('email'));
        $password = $this->getInput('password');
        $confirmPassword = $this->getInput('confirm_password');

        // Validações
        if (empty($name) || empty($email) || empty($password)) {
            Session::flash('error', 'Todos os campos são obrigatórios.');
            $this->redirect('/register');
        }

        if (!$this->validateEmail($email)) {
            Session::flash('error', 'Email inválido.');
            $this->redirect('/register');
        }

        if (strlen($password) < 6) {
            Session::flash('error', 'A senha deve ter pelo menos 6 caracteres.');
            $this->redirect('/register');
        }

        if ($password !== $confirmPassword) {
            Session::flash('error', 'As senhas não coincidem.');
            $this->redirect('/register');
        }

        if (User::emailExists($email)) {
            Session::flash('error', 'Este email já está em uso.');
            $this->redirect('/register');
        }

        try {
            $userId = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'role' => 'user'
            ]);

            $user = User::findById($userId);
            Session::login($user);
            
            Session::flash('success', 'Cadastro realizado com sucesso!');
            $this->redirect('/');
            
        } catch (\Exception $e) {
            Session::flash('error', 'Erro ao criar conta. Tente novamente.');
            $this->redirect('/register');
        }
    }

    public function logout(): void
    {
        Session::logout();
        Session::flash('success', 'Logout realizado com sucesso!');
        $this->redirect('/');
    }

    public function showAdminLogin(): void
    {
        if (Session::isAuthenticated() && Session::isAdmin()) {
            $this->redirect('/admin');
        }

        $this->view('auth/admin-login', [
            'title' => 'Login Administrador',
            'csrf_token' => $this->generateCsrf()
        ]);
    }

    public function adminLogin(): void
    {
        if (!$this->validateCsrf()) {
            Session::flash('error', 'Token de segurança inválido.');
            $this->redirect('/admin/login');
        }

        $email = $this->sanitize($this->getInput('email'));
        $password = $this->getInput('password');

        if (empty($email) || empty($password)) {
            Session::flash('error', 'Email e senha são obrigatórios.');
            $this->redirect('/admin/login');
        }

        $user = User::findByEmail($email);
        
        if (!$user || $user['role'] !== 'admin' || !User::verifyPassword($password, $user['password'])) {
            Session::flash('error', 'Credenciais de administrador inválidas.');
            $this->redirect('/admin/login');
        }

        unset($user['password']);
        Session::login($user);
        
        Session::flash('success', 'Login de administrador realizado com sucesso!');
        $this->redirect('/admin');
    }
}
