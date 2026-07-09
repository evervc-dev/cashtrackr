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
    $response->assertSessionHas('error', 'Credenciales incorrectas');

    $this->assertGuest();
});

it('Prevents unverified user from accessing dashboard', function() {
    $user = User::factory()->unverified()->create([
        'email' => 'juanperez23@gamil.com',
        'password' => bcrypt('Xq7#vL2!pR9@tF3%')
    ]);

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'Xq7#vL2!pR9@tF3%',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticated();

    $dashboardResponse = $this->get(route('dashboard'));
    $dashboardResponse->assertRedirect(route('verification.notice'));
});

it('Does not allows access to dashboard if email is not verified', function() {
    $user = User::factory()->create([
        'email_verified_at' => null
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertRedirect(route('verification.notice'));
});

it('Allow access to dashboard if email is verified', function() {
    $user = User::factory()->create([
        'email_verified_at' => now()
    ]);

    $response = $this->actingAs($user)->get(route('dashboard'));
    $response->assertOk();
});


it('Fails login if user does not exist', function() {
    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => 'noexiste@gamil.com',
        'password' => 'password123'
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors([
        'email' => 'No se ha encontrado una cuenta con ese correo.'
    ]);

    $this->assertGuest();
});