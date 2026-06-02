<?php

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

uses(RefreshDatabase::class);

it('Shows the registration screen', function() {
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

// Happy Path
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

it('Should validate required fields when the request body is empty', function() {
    $response = $this->post(route('register.store'), []);

    $response->assertSessionHasErrors([
        'name' => 'El nombre es obligatorio',
        'email' => 'El email es obligatorio',
        'password'  => 'La contraseña es obligatoria'
    ]);
});

it('Prevents duplicate email addresses', function() {
    // Crea un usuario con un email específico y los demás datos generados aleatoriamente
    User::factory()->create([
        'email' => 'juanperez23@gamil.com'
    ]);

    // Intentamos crear otro usuario con el mismo email del factory para generar el error
    $response = $this->post(route('register.store'), [
        'name' => 'Juan Pérez',
        'email' => 'juanperez23@gamil.com',
        'password' => 'Xq7#vL2!pR9@tF3%',
        'password_confirmation' => 'Xq7#vL2!pR9@tF3%'
    ]);

    $response->assertRedirect();

    $response->assertSessionHasErrors([
        'email' => 'El email ya está registrado'
    ]);
});

it('Sends the verification email notification after registration', function() {
    Notification::fake();

    $response = $this->post(route('register.store'), [
        'name' => 'Juan Pérez',
        'email' => 'juanperez23@gamil.com',
        'password' => 'Xq7#vL2!pR9@tF3%',
        'password_confirmation' => 'Xq7#vL2!pR9@tF3%'
    ]);

    $user = User::whereEmail('juanperez23@gamil.com')->first();

    Notification::assertSentTo($user, VerifyEmail::class);
});

it('Verifies the user email from a signed verification link', function() {
    // Crea un usuario falso pero sin verificar
    $user = User::factory()->unverified()->create();

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->getKey(),
            'hash' => sha1($user->email),
        ]
    );

    // Simula que el usuario visita la ruta generada
    $response = $this->actingAs($user)->get($verificationUrl);

    $response->assertRedirect(route('dashboard'));
    expect($user->hasVerifiedEmail())->toBeTrue();
});

it('Does not allow an unverified user to access the dashboard', function() {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertRedirect(route('verification.notice'));
});

it('Allows a verified user to access the dashboard', function() {
    $user = User::factory()->create([
        'email_verified_at' => now()
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));

    $response->assertOk();
});