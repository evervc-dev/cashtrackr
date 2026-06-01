<?php

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
