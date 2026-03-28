<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;

# Les routes 

// Pages publiques (accessibles sans authentification)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Routes d'authentification
Route::get('/signin', [AuthController::class, 'showLogin'])->name('signin');
Route::post('/signin', [AuthController::class, 'login']);
Route::get('/signup', [AuthController::class, 'showRegister'])->name('signup');
Route::post('/signup', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/reset-password', function () {
    return view('auth.reset-password');
})->name('password.request');

// Pages d'information (publiques)
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/documentation', function () {
    return view('documentation');
})->name('documentation');

Route::get('/faq', function () {
    return view('faq');
})->name('faq');

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Dashboard pages
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pages d'audit
    Route::get('/audit/current', [AuditController::class, 'current'])->name('audit.current');
    Route::get('/audit/controls/{section?}', [AuditController::class, 'controls'])->name('controls.iso');
    Route::get('/audit/evidence', [AuditController::class, 'evidence'])->name('evidence.collect');
    Route::get('/audit/evaluation', [AuditController::class, 'evaluation'])->name('evaluation.index');
    Route::get('/audit/risk', [AuditController::class, 'risk'])->name('risk.management');
    Route::get('/audits', [AuditController::class, 'index'])->name('audits.index');
    Route::get('/audits/create', [AuditController::class, 'create'])->name('audits.create');
    Route::post('/audits', [AuditController::class, 'store'])->name('audits.store');
    Route::get('/audits/{id}', [AuditController::class, 'show'])->name('audits.show');
    Route::get('/audits/{id}/edit', [AuditController::class, 'edit'])->name('audits.edit');
    Route::put('/audits/{id}', [AuditController::class, 'update'])->name('audits.update');
    Route::delete('/audits/{id}', [AuditController::class, 'destroy'])->name('audits.destroy');
    Route::post('/audits/{id}/start', [AuditController::class, 'start'])->name('audits.start');

    // Routes API pour charger les structures ISO
    Route::get('/api/enterprises/{id}', [AuditController::class, 'getEnterpriseData']);
    Route::get('/api/references/{id}/structure', [AuditController::class, 'getReferenceStructure']);


    // Routes pour les contrôles
    Route::get('/audit/controls', [AuditController::class, 'controls'])->name('controls.iso');
    Route::get('/audit/controls/{section}', [AuditController::class, 'controls'])->name('controls.iso');
    Route::get('/audit/controls/{id}/show', [AuditController::class, 'controlsShow'])->name('controls.iso.show');
    Route::put('/audit/clause-evaluation/{id}', [AuditController::class, 'updateClauseEvaluation'])->name('audit.clause-evaluation.update');
    Route::put('/audit/evaluation/{id}', [AuditController::class, 'updateEvaluation'])->name('audit.evaluation.update');


    // Pages de rapports
    Route::get('/reports/audit', [ReportController::class, 'audit'])->name('report.index');
    Route::get('/reports/history', [ReportController::class, 'history'])->name('audit.history');
    Route::get('/reports/statistics', [ReportController::class, 'statistics'])->name('statistics.index');
});

// Fallback route pour les pages non trouvées
Route::fallback(function () {
    return view('errors.404');
});

Route::get('/php-info', function () {
    return phpinfo();
});