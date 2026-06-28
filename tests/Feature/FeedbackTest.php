<?php

declare(strict_types=1);

use App\Models\Feedback;

it('stores anonymous feedback from the public form', function () {
    $response = $this->post('/feedback', [
        'type' => 'sugerencia',
        'message' => 'Sería útil poder filtrar por municipio.',
        'contact' => 'ana@example.com',
        'url' => '/',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $feedback = Feedback::firstOrFail();
    expect($feedback->type->value)->toBe('sugerencia')
        ->and($feedback->message)->toBe('Sería útil poder filtrar por municipio.')
        ->and($feedback->contact)->toBe('ana@example.com')
        ->and($feedback->contributor_id)->not->toBeNull();
});

it('requires a type and a message', function () {
    $this->post('/feedback', ['type' => '', 'message' => ''])
        ->assertSessionHasErrors(['type', 'message']);

    expect(Feedback::count())->toBe(0);
});

it('rejects an invalid feedback type', function () {
    $this->post('/feedback', ['type' => 'spam', 'message' => 'hola'])
        ->assertSessionHasErrors('type');
});

it('accepts feedback without an optional contact', function () {
    $this->post('/feedback', [
        'type' => 'problema',
        'message' => 'El botón de registrar no abre en mi teléfono.',
    ])->assertSessionHasNoErrors();

    expect(Feedback::firstOrFail()->contact)->toBeNull();
});
