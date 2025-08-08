<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Raffle;

class HomeController extends Controller
{
    public function index(): void
    {
        $raffles = Raffle::getPublished(12);

        $this->view('home/index', [
            'title' => 'Rifas Disponíveis',
            'raffles' => $raffles
        ]);
    }
}
