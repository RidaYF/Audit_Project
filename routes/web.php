<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/audit/start', function () {
    return view('audit.start');
})->name('audit.start');