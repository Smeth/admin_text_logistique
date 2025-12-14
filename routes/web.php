<?php

use App\Http\Controllers\AgenceController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ColiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviseController;
use App\Http\Controllers\EntrepriseTransporteurController;
use App\Http\Controllers\FactureController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TarifController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
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
        Route::post('/colis/{coli}/add-step', [ColiController::class, 'addStep'])->name('colis.add-step');
    });
    
    // Factures et reçus
    Route::get('/colis/{coli}/facture', [FactureController::class, 'facture'])->name('colis.facture');
    Route::get('/colis/{coli}/recu', [FactureController::class, 'recu'])->name('colis.recu');
    Route::get('/colis/{coli}/facture/download', [FactureController::class, 'downloadFacture'])->name('colis.facture.download');
    Route::get('/colis/{coli}/recu/download', [FactureController::class, 'downloadRecu'])->name('colis.recu.download');

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

    // Gestion de caisse (Admin uniquement)
    Route::middleware('role:admin')->group(function () {
        Route::resource('caisses', CaisseController::class)->parameters(['caisses' => 'caisse']);
        Route::post('/caisses/{caisse}/ouvrir', [CaisseController::class, 'ouvrir'])->name('caisses.ouvrir');
        Route::post('/caisses/{caisse}/fermer', [CaisseController::class, 'fermer'])->name('caisses.fermer');
        Route::resource('transactions', TransactionController::class)->except(['edit', 'update']);
    });

    // Gestion des utilisateurs et rôles (Admin uniquement)
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
    });

    // Sauvegarde, Export et Import (Admin uniquement)
    Route::middleware('role:admin')->group(function () {
        // Sauvegardes
        Route::prefix('backups')->name('backups.')->group(function () {
            Route::get('/', [\App\Http\Controllers\BackupController::class, 'index'])->name('index');
            Route::post('/', [\App\Http\Controllers\BackupController::class, 'create'])->name('create');
            Route::get('/{filename}/download', [\App\Http\Controllers\BackupController::class, 'download'])->name('download');
            Route::post('/restore', [\App\Http\Controllers\BackupController::class, 'restore'])->name('restore');
            Route::delete('/{filename}', [\App\Http\Controllers\BackupController::class, 'destroy'])->name('destroy');
        });

        // Exports
        Route::prefix('exports')->name('exports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ExportController::class, 'index'])->name('index');
            Route::get('/clients', [\App\Http\Controllers\ExportController::class, 'exportClients'])->name('clients');
            Route::get('/colis', [\App\Http\Controllers\ExportController::class, 'exportColis'])->name('colis');
            Route::get('/transactions', [\App\Http\Controllers\ExportController::class, 'exportTransactions'])->name('transactions');
            Route::get('/configurations', [\App\Http\Controllers\ExportController::class, 'exportConfigurations'])->name('configurations');
        });

        // Imports
        Route::prefix('imports')->name('imports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\ImportController::class, 'index'])->name('index');
            Route::post('/clients', [\App\Http\Controllers\ImportController::class, 'importClients'])->name('clients');
            Route::post('/colis', [\App\Http\Controllers\ImportController::class, 'importColis'])->name('colis');
            Route::post('/configurations', [\App\Http\Controllers\ImportController::class, 'importConfigurations'])->name('configurations');
        });
    });
});
