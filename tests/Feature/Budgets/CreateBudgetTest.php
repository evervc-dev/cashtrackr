<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('Validates required fields when creating a budget', function(){
    $user = User::factory()->create([
        'email_verified_at' => now()
    ]);

    $response = $this->actingAs($user)
        ->from(route('budgets.create'))
        ->post(route('budgets.store', [
            'name' => '',
            'amount' => '',
            'type' => ''
        ])
    );

    $response->assertRedirect(route('budgets.create'));
    $response->assertSessionHasErrors([
        'name',
        'amount',
        'type'
    ]);
});
