<?php

it('renders a branded 404 page', function () {
    config(['app.debug' => false]);

    $response = $this->get('/no-such-page-xyz');

    $response->assertNotFound();
    $response->assertSee('Página no encontrada');
    $response->assertSee(config('app.name'));
    $response->assertSee('Volver al inicio');
});

it('compiles every branded error view', function (int $code, string $needle) {
    expect(view("errors.{$code}")->render())->toContain($needle);
})->with([
    [403, 'Sin acceso'],
    [419, 'La página expiró'],
    [429, 'Demasiadas solicitudes'],
    [500, 'Algo salió mal'],
    [503, 'En mantenimiento'],
]);
