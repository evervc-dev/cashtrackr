<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('Shows the login screen', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
});

it('Logs in a verified user successfully', function () {
    $user = User::factory()->create([
        'email' => 'juanperez23@gamil.com',
        'password' => bcrypt('Xq7#vL2!pR9@tF3%'),
        'email_verified_at' => now(),
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'Xq7#vL2!pR9@tF3%',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();
});

it('Does not log in with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'juanperez23@gamil.com',
        'password' => bcrypt('Xq7#vL2!pR9@tF3%')
    ]);

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'inccorrect-password',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors([
        'email' => 'Credenciales incorrectas'
    ]);

    $this->assertGuest();
});

