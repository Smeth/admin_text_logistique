<?php

use App\Http\Controllers\AgenceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ColiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviseController;
use App\Http\Controllers\EntrepriseTransporteurController;
use App\Http\Controllers\TarifController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Routes protégées
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Clients (Agent peut créer et voir, Admin peut tout faire)
    Route::resource('clients', ClientController::class)->except(['edit', 'update', 'destroy']);
    Route::middleware('role:admin')->group(function () {
        Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    });

    // Colis (Agent peut créer et voir, Admin peut tout faire)
    Route::resource('colis', ColiController::class)->except(['edit', 'update', 'destroy']);
    Route::middleware('role:admin')->group(function () {
        Route::get('/colis/{coli}/edit', [ColiController::class, 'edit'])->name('colis.edit');
        Route::put('/colis/{coli}', [ColiController::class, 'update'])->name('colis.update');
        Route::delete('/colis/{coli}', [ColiController::class, 'destroy'])->name('colis.destroy');
    });

    // Agences (Admin uniquement)
    Route::middleware('role:admin')->group(function () {
        Route::resource('agences', AgenceController::class);
    });

    // Entreprises Transporteurs (Admin uniquement)
    Route::middleware('role:admin')->group(function () {
        Route::resource('transporteurs', EntrepriseTransporteurController::class);
    });

    // Paramétrage (Admin uniquement)
    Route::middleware('role:admin')->group(function () {
        Route::resource('devises', DeviseController::class);
        Route::resource('tarifs', TarifController::class);
    });
});
