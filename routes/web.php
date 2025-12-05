<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;

// Ruta de login
Route::get('/', Login::class)->name('login');
Route::get('/login', Login::class)->name('login.page');

// Logout
Route::get('/logout', function () {
    session()->flush();
    return redirect()->route('login');
})->name('logout');

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth.session'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect()->route('auditoria.nueva');
    })->name('dashboard');

    Route::get('/auditoria/nueva', \App\Livewire\FormularioAuditoria::class)->name('auditoria.nueva');
    Route::get('/auditoria/crosselling', \App\Livewire\FormularioCrosselling::class)->name('auditoria.crosselling');
    Route::get('/auditoria/digital', \App\Livewire\FormularioDigital::class)->name('auditoria.digital');
});
