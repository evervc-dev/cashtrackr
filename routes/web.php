<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/register', [RegisterController::class, 'index'])->name('register');
Route::post('/auth/register', [RegisterController::class, 'store'])->name('register.store');

Route::get('/auth/login', [LoginController::class, 'index'])->name('login');
Route::post('/auth/login', [LoginController::class, 'store'])->name('login.store');

Route::get('/email/verify/{id}/{hash}', function(EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect()->route('dashboard')->with('success', 'Tu correo fue verificado correctamente. Ya puedes crear presupuestos y gastos.');

    // 'auth' valida la sesión y 'signed' valida la integridad del hash
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/email/verify', function() {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::post('/email/verification-notification', function(Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('success', 'Se ha reenviado el correo de verificación');

    //Rate limit con throttle donde el primer 1 es la cantidad de peticiones que se pueden enviar
    // y el otro 1 es en la cantidad de tiempo que se pueden enviar, es decir, lo siguiente permite 1 peticón en cada (1) minuto
})->middleware(['auth', 'throttle:1,1'])->name('verification.send');

Route::get('dashboard', function() {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');