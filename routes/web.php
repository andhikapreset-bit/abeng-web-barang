<?php

use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;

// Route::get('/login', Login::class)->name('login');

// Halaman Dashboard Utama
Route::get('/dashboard', Dashboard::class)->name('dashboard');

// Redirect halaman utama ke login
Route::get('/', function () {
    return redirect()->route('login');
});