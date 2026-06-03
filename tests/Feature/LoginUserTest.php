<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('Shows the login screen', function () {
    $response = $this->get('/login');

    $response->assertOk();
});
