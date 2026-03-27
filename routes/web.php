<?php

use Illuminate\Support\Facades\Route;


# Les routes 

// Pages publiques
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Pages d'authentification
Route::get('/signin', function () {
    return view('au.auth.signin', ['title' => 'Sign In']);
})->name('signin');

Route::get('/signup', function () {
    return view('pages.auth.signup', ['title' => 'Sign Up']);
})->name('signup');


// Pages d'information
Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/documentation', function () {
    return view('documentation');
})->name('documentation');

Route::get('/faq', function () {
    return view('faq');
})->name('faq');


// Dashboard pages
Route::get('/dashboard', function () {
    return view('pages.dashboard.dashboard', ['title' => 'Audit Dashboard']);
})->name('dashboard');


// Pages d'audit
Route::get('/audit/current', function () {
    return view('audit.current', ['title' => 'Audit en cours']);
})->name('audit.current');

Route::get('/audit/controls/{section}', function ($section) {
    return view('audit.controls', ['title' => "Contrôles ISO 27002 - $section", 'section' => $section]);
})->name('controls.iso');

Route::get('/audit/evidence', function () {
    return view('audit.evidence', ['title' => 'Collecte de preuves']);
})->name('evidence.collect');

Route::get('/audit/evaluation', function () {
    return view('audit.evaluation', ['title' => 'Évaluation de conformité']);
})->name('evaluation.index');

Route::get('/audit/risk', function () {
    return view('audit.risk', ['title' => 'Gestion des risques ISO 27005']);
})->name('risk.management');


// Pages de rapports
Route::get('/reports/audit', function () {
    return view('reports.audit', ['title' => 'Rapport d\'audit']);
})->name('report.index');

Route::get('/reports/history', function () {
    return view('reports.history', ['title' => 'Historique des audits']);
})->name('audit.history');

Route::get('/reports/statistics', function () {
    return view('reports.statistics', ['title' => 'Statistiques de conformité']);
})->name('statistics.index');


// Pages de support
Route::get('/support', function () {
    return view('support', ['title' => 'Support']);
})->name('support');


// Fallback route pour les pages non trouvées
Route::fallback(function () {
    return view('errors.404');
});