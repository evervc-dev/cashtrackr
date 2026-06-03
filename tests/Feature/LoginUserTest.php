<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('Shows the login screen', function () {
    $response = $this->get('/login');

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
