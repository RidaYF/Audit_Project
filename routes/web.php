<?php

use App\Http\Controllers\EntrepriseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ControleController; 
use App\Http\Controllers\RiskController; 

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
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ============================================
    // GESTION DES AUDITS
    // ============================================
    Route::prefix('audits')->group(function () {
        // CRUD de base
        Route::get('/', [AuditController::class, 'index'])->name('audits.index');
        Route::get('/create', [AuditController::class, 'create'])->name('audits.create');
        Route::post('/', [AuditController::class, 'store'])->name('audits.store');
        Route::get('/{id}', [AuditController::class, 'show'])->name('audits.show');
        Route::get('/{id}/edit', [AuditController::class, 'edit'])->name('audits.edit');
        Route::put('/{id}', [AuditController::class, 'update'])->name('audits.update');
        Route::delete('/{id}', [AuditController::class, 'destroy'])->name('audits.destroy');
        Route::post('/{id}/start', [AuditController::class, 'start'])->name('audits.start');
        
        // Gestion des clauses
        Route::get('/{id}/clauses', [AuditController::class, 'clauses'])->name('audits.clauses');
        Route::get('/{auditId}/clause/{clauseEvalId}/edit', [AuditController::class, 'editClause'])->name('audits.clause.edit');
        Route::put('/{auditId}/clause/{clauseEvalId}', [AuditController::class, 'updateClause'])->name('audits.clause.update');
        
        // Gestion des contrôles
        Route::get('/{id}/controls', [AuditController::class, 'controlsList'])->name('audits.controls');
        Route::get('/{auditId}/control/{evaluationId}/edit', [AuditController::class, 'editControl'])->name('audits.control.edit');
        Route::put('/{auditId}/control/{evaluationId}', [AuditController::class, 'updateControl'])->name('audits.control.update');

        Route::get('/{auditId}/clause-risk/{clauseEvalId}/edit', [AuditController::class, 'editClauseRisk'])
        ->name('audits.clause.edit-risk');
        Route::put('/{auditId}/clause-risk/{clauseEvalId}', [AuditController::class, 'updateClauseRisk'])
            ->name('audits.clause.update-risk');

        Route::get('/{id}/recalculate', [AuditController::class, 'recalculateIndicators'])->name('audits.recalculate');
    });


    // ============================================
    // AUDIT EN COURS (pages de travail)
    // ============================================
    Route::prefix('audit')->group(function () {
        Route::get('/current/{audit_id?}', [AuditController::class, 'current'])->name('audit.current');
        Route::get('/controls/{section?}', [AuditController::class, 'controls'])->name('controls.iso');
        Route::get('/controls/{id}/show', [AuditController::class, 'controlsShow'])->name('controls.iso.show');
        Route::get('/evidence', [AuditController::class, 'evidence'])->name('evidence.collect');
        Route::get('/evaluation', [AuditController::class, 'evaluation'])->name('evaluation.index');
        Route::get('/risk', [AuditController::class, 'risk'])->name('risk.management'); // Gardez celle-ci
        
        // Mises à jour des évaluations (API)
        Route::put('/clause-evaluation/{id}', [AuditController::class, 'updateClauseEvaluation'])->name('audit.clause-evaluation.update');
        Route::put('/evaluation/{id}', [AuditController::class, 'updateEvaluation'])->name('audit.evaluation.update');

        Route::get('/evaluation/{controleId}', [AuditController::class, 'evaluationSingle'])->name('evaluation.single');
                
    });
    
    // ============================================
    // GESTION DES ENTREPRISES
    // ============================================
    Route::prefix('entreprises')->name('entreprises.')->group(function () {
        Route::get('/', [EntrepriseController::class, 'index'])->name('index');
        Route::get('/create', [EntrepriseController::class, 'create'])->name('create');
        Route::post('/', [EntrepriseController::class, 'store'])->name('store');
        Route::get('/{id}', [EntrepriseController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [EntrepriseController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EntrepriseController::class, 'update'])->name('update');
        Route::delete('/{id}', [EntrepriseController::class, 'destroy'])->name('destroy');

            // Routes supplémentaires pour la gestion avancée
    Route::patch('/{id}/toggle-active', [EntrepriseController::class, 'toggleActive'])->name('toggle-active');
    Route::patch('/{id}/restore', [EntrepriseController::class, 'restore'])->name('restore');
    Route::delete('/{id}/force', [EntrepriseController::class, 'forceDestroy'])->name('force-destroy');
    });



    // Routes pour la gestion des contrôles (admin/formateur uniquement)
    Route::prefix('controles')->name('controles.')->middleware(['auth'])->group(function () {
        Route::get('/', [ControleController::class, 'index'])->name('index');
        Route::get('/{id}/edit', [ControleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ControleController::class, 'update'])->name('update');
        Route::get('/{id}/preuves', [ControleController::class, 'preuves'])->name('preuves');
        Route::post('/{id}/preuves', [ControleController::class, 'addPreuve'])->name('preuves.add');
        Route::delete('/preuves/{preuveId}', [ControleController::class, 'deletePreuve'])->name('preuves.delete');
        Route::delete('/preuves/{preuveId}/file', [ControleController::class, 'deleteFile'])->name('preuves.delete-file');
    });

    // ============================================
    // Risk (pages de travail)
    // ============================================
    
    // ============================================
    // API ROUTES (pour chargement dynamique)
    // ============================================
    Route::prefix('api')->group(function () {
        Route::get('/enterprises/{id}', [AuditController::class, 'getEnterpriseData']);
        Route::get('/references/{id}/structure', [AuditController::class, 'getReferenceStructure']);
    });
    
    // ============================================
    // RAPPORTS ET STATISTIQUES
    // ============================================
    Route::prefix('reports')->group(function () {
        // Affichage
        Route::get('/audit/{id?}', [ReportController::class, 'audit'])->name('report.index');
        
        // Sauvegarde (Post)
        Route::post('/audit/{id}/store', [ReportController::class, 'storeReport'])->name('report.store');
        
        Route::get('/history', [ReportController::class, 'history'])->name('audit.history');
        Route::get('/statistics', [ReportController::class, 'statistics'])->name('statistics.index');
        
});
});
// Fallback route pour les pages non trouvées
Route::fallback(function () {
    return view('errors.404');
});

Route::get('/php-info', function () {
    return phpinfo();
});