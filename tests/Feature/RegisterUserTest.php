<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(RefreshDatabase::class);

it('shows the registration screen', function() {
    $response = $this->get(route('register'));

    // Formas de validar si existe la página de registro
    $response->assertOk();
    $response->assertStatus(200);

    // Leer elementos de la página
    $response->assertSee('Crear Cuenta');

    // Leer en orden
    $response->assertSeeInOrder([
        'Crear Cuenta',
        'Registrarme'
    ]);
});


it('Register a new user unverified and dispatches the registered event', function() {
    // Evita que se envíen los emails (para evitar el consumo)
    Event::fake();

    $response = $this->post(route('register.store'), [
        'name' => 'Juan Pérez',
        'email' => 'juanperez23@gamil.com',
        'password' => 'Xq7#vL2!pR9@tF3%',
        'password_confirmation' => 'Xq7#vL2!pR9@tF3%'
    ]);

    $response->assertRedirect(route('verification.notice'));

    $user = User::whereEmail('juanperez23@gamil.com')->first();

    expect($user)->not()->toBeNull();
    expect($user->name)->toBe('Juan Pérez');
    expect($user->email)->toBe('juanperez23@gamil.com');
    expect($user->hasVerifiedEmail())->toBeFalse(); // Indica que la cuenta aún no esté verificada

    Event::assertDispatched(Registered::class);
});