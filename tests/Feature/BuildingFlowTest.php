<?php

declare(strict_types=1);

use App\Enums\BuildingMode;
use App\Enums\BuildingStatus;
use App\Enums\BuildingType;
use App\Enums\NeedStatus;
use App\Models\Building;
use App\Models\Contributor;
use App\Models\Supply;
use App\Models\SupplyCategory;
use App\Services\NeedService;
use Database\Seeders\SupplyCatalogSeeder;

function registerBuilding(array $overrides = []): Building
{
    test()->post('/edificios', array_merge([
        'name' => 'Edificio '.uniqid(),
        'type' => 'residencial',
    ], $overrides));

    return Building::latest('id')->firstOrFail();
}

it('renders the public building list', function () {
    $this->get('/')->assertOk();
});

it('registers a building from the public form without authentication', function () {
    $response = $this->post('/edificios', [
        'name' => 'Edificio Prueba',
        'type' => 'residencial',
        'structural_status' => 'colapsado',
        'community_name' => 'San Bernardino',
        'state' => 'Distrito Capital',
    ]);

    $building = Building::firstWhere('name', 'Edificio Prueba');

    // Rescue mode is inferred from a collapsed/damaged structure, not a manual field.
    expect($building)->not->toBeNull()
        ->and($building->mode)->toBe(BuildingMode::Rescate)
        ->and($building->community->name)->toBe('San Bernardino');

    $response->assertRedirect("/edificios/{$building->slug}");
});

it('validates the building registration', function () {
    $this->post('/edificios', ['name' => '', 'type' => 'inexistente'])
        ->assertSessionHasErrors(['name', 'type']);
});

it('adds a free-text need with a required category', function () {
    $this->seed(SupplyCatalogSeeder::class);
    $building = registerBuilding();
    $category = SupplyCategory::firstOrFail();

    $this->post("/edificios/{$building->slug}/necesidades", [
        'custom_supply_name' => 'Medicina específica XYZ',
        'supply_category_id' => $category->id,
        'priority' => 'critica',
    ])->assertRedirect();

    $need = $building->needs()->firstOrFail();

    expect($need->custom_supply_name)->toBe('Medicina específica XYZ')
        ->and($need->supply_category_id)->toBe($category->id)
        ->and($need->status)->toBe(NeedStatus::Solicitada);
});

it('rejects a free-text need without a category', function () {
    $building = registerBuilding();

    $this->post("/edificios/{$building->slug}/necesidades", [
        'custom_supply_name' => 'Algo',
        'priority' => 'media',
    ])->assertSessionHasErrors('supply_category_id');
});

it('inherits unit and category from a predefined supply', function () {
    $this->seed(SupplyCatalogSeeder::class);
    $building = registerBuilding();
    $supply = Supply::where('name', 'Palas')->firstOrFail();

    $this->post("/edificios/{$building->slug}/necesidades", [
        'supply_id' => $supply->id,
        'quantity' => 10,
        'priority' => 'alta',
    ])->assertRedirect();

    $need = $building->needs()->firstOrFail();

    expect($need->supply_id)->toBe($supply->id)
        ->and($need->unit)->toBe($supply->unit)
        ->and($need->supply_category_id)->toBe($supply->supply_category_id);
});

it('adds several needs at once and recomputes status', function () {
    $this->seed(SupplyCatalogSeeder::class);
    $building = registerBuilding();
    $water = Supply::where('name', 'Agua potable')->firstOrFail();

    $this->post("/edificios/{$building->slug}/necesidades/lote", [
        'needs' => [
            ['supply_id' => $water->id, 'quantity' => 100, 'priority' => 'critica'],
            ['custom_supply_name' => 'Generador grande', 'supply_category_id' => SupplyCategory::firstOrFail()->id, 'priority' => 'alta'],
        ],
    ])->assertRedirect();

    expect($building->needs()->count())->toBe(2)
        ->and($building->fresh()->status)->toBe(BuildingStatus::Critico);
});

it('records commitments from multiple people and moves the need to asignada', function () {
    $building = registerBuilding();
    $need = $building->needs()->create([
        'custom_supply_name' => 'Agua',
        'priority' => 'media',
        'status' => NeedStatus::Solicitada,
    ]);

    $a = Contributor::create(['token' => 'dev-a', 'trust_level' => 'normal']);
    $b = Contributor::create(['token' => 'dev-b', 'trust_level' => 'normal']);
    $service = app(NeedService::class);

    $service->commit($need, $a, 'Carlos');
    $service->commit($need, $a, 'Carlos'); // same name → no duplicate
    $service->commit($need, $b, 'Ana');

    expect($need->commitments()->count())->toBe(2)
        ->and($need->fresh()->status)->toBe(NeedStatus::Comprometida);
});

it('lets one device register several named helpers for the same need', function () {
    $building = registerBuilding();
    $need = $building->needs()->create([
        'custom_supply_name' => 'Agua',
        'priority' => 'media',
        'status' => NeedStatus::Solicitada,
    ]);

    // A single contributor (one browser) anotando varias personas.
    $this->post("/necesidades/{$need->id}/comprometerse", ['name' => 'Carlos'])->assertRedirect();
    $this->post("/necesidades/{$need->id}/comprometerse", ['name' => 'Ana'])->assertRedirect();
    $this->post("/necesidades/{$need->id}/comprometerse", ['name' => 'carlos'])->assertRedirect(); // dup name (case-insensitive)

    expect($need->commitments()->count())->toBe(2)
        ->and($need->commitments()->pluck('name')->map(fn ($n) => mb_strtolower($n))->sort()->values()->all())
        ->toBe(['ana', 'carlos']);
});

it('derives the need status from the dominant commitment status', function () {
    $building = registerBuilding();
    $need = $building->needs()->create([
        'custom_supply_name' => 'Agua',
        'priority' => 'media',
        'status' => NeedStatus::Solicitada,
    ]);

    $a = Contributor::create(['token' => 'dev-a', 'trust_level' => 'normal']);
    $b = Contributor::create(['token' => 'dev-b', 'trust_level' => 'normal']);
    $service = app(NeedService::class);

    $service->commit($need, $a, 'Carlos');
    $service->commit($need, $b, 'Ana');

    $carlos = $need->commitments()->where('contributor_id', $a->id)->firstOrFail();

    // 1 comprometida + 1 en_camino → tie broken toward the most advanced state.
    $service->transitionCommitment($carlos, NeedStatus::EnCamino, $a);
    expect($need->fresh()->status)->toBe(NeedStatus::EnCamino);

    // When the second person also moves on, en_camino clearly dominates.
    $ana = $need->commitments()->where('contributor_id', $b->id)->firstOrFail();
    $service->transitionCommitment($ana, NeedStatus::EnCamino, $b);
    expect($need->fresh()->status)->toBe(NeedStatus::EnCamino);
});

it('reopens a need when every commitment is cancelled', function () {
    $building = registerBuilding();
    $need = $building->needs()->create([
        'custom_supply_name' => 'Agua',
        'priority' => 'media',
        'status' => NeedStatus::Solicitada,
    ]);

    $a = Contributor::create(['token' => 'dev-a', 'trust_level' => 'normal']);
    $service = app(NeedService::class);

    $service->commit($need, $a, 'Carlos');
    expect($need->fresh()->status)->toBe(NeedStatus::Comprometida);

    $carlos = $need->commitments()->firstOrFail();
    $service->transitionCommitment($carlos, NeedStatus::Cancelada, $a);

    expect($need->fresh()->status)->toBe(NeedStatus::Solicitada);
});

it('cancels the whole need and all its commitments, without auto-reopening', function () {
    $building = registerBuilding();
    $need = $building->needs()->create([
        'custom_supply_name' => 'Agua',
        'priority' => 'media',
        'status' => NeedStatus::Solicitada,
    ]);

    $a = Contributor::create(['token' => 'dev-a', 'trust_level' => 'normal']);
    app(NeedService::class)->commit($need, $a, 'Carlos');
    expect($need->fresh()->status)->toBe(NeedStatus::Comprometida);

    $this->post("/necesidades/{$need->id}/cancelar")->assertRedirect();

    $need->refresh();
    expect($need->status)->toBe(NeedStatus::Cancelada)
        ->and($need->dominantStatus())->toBe(NeedStatus::Cancelada) // what the resource renders
        ->and($need->cancelled_at)->not->toBeNull()
        ->and($need->commitments()->where('status', '!=', 'cancelada')->count())->toBe(0);
});

it('reopens a cancelled need to solicitada', function () {
    $building = registerBuilding();
    $need = $building->needs()->create([
        'custom_supply_name' => 'Agua',
        'priority' => 'media',
        'status' => NeedStatus::Solicitada,
    ]);

    app(NeedService::class)->cancelNeed($need);
    expect($need->fresh()->status)->toBe(NeedStatus::Cancelada);

    $this->post("/necesidades/{$need->id}/reabrir")->assertRedirect();
    expect($need->fresh()->status)->toBe(NeedStatus::Solicitada);
});

it('commits to a need via the public endpoint', function () {
    $building = registerBuilding();
    $need = $building->needs()->create([
        'custom_supply_name' => 'Palas',
        'priority' => 'alta',
        'status' => NeedStatus::Solicitada,
    ]);

    $this->post("/necesidades/{$need->id}/comprometerse", ['name' => 'Brigada El Valle'])->assertRedirect();

    expect($need->commitments()->count())->toBe(1)
        ->and($need->fresh()->status)->toBe(NeedStatus::Comprometida);
});

it('filters places by an open need category', function () {
    $this->seed(SupplyCatalogSeeder::class);
    $category = SupplyCategory::firstOrFail();

    $withNeed = registerBuilding(['name' => 'ConPedido']);
    $withNeed->needs()->create([
        'custom_supply_name' => 'Algo',
        'supply_category_id' => $category->id,
        'priority' => 'media',
        'status' => NeedStatus::Solicitada,
    ]);
    registerBuilding(['name' => 'SinPedido']);

    $this->get("/?category={$category->id}")
        ->assertOk()
        ->assertSee('ConPedido')
        ->assertDontSee('SinPedido');
});

it('enforces the per-commitment lifecycle state machine', function () {
    $building = registerBuilding();
    $need = $building->needs()->create([
        'custom_supply_name' => 'Agua',
        'priority' => 'media',
        'status' => NeedStatus::Solicitada,
    ]);

    // A person commits (starts at "comprometida").
    $this->post("/necesidades/{$need->id}/comprometerse", ['name' => 'Carlos'])->assertRedirect();
    $commitment = $need->commitments()->firstOrFail();

    // Valid: comprometida -> en_camino
    $this->patch("/commitments/{$commitment->id}/estado", ['status' => 'en_camino'])
        ->assertRedirect();

    expect($commitment->fresh()->status)->toBe(NeedStatus::EnCamino)
        ->and($need->fresh()->status)->toBe(NeedStatus::EnCamino);

    // Invalid: en_camino -> confirmada (must pass through entregada)
    $this->patch("/commitments/{$commitment->id}/estado", ['status' => 'confirmada'])
        ->assertSessionHasErrors('status');

    expect($commitment->fresh()->status)->toBe(NeedStatus::EnCamino);
});

it('attributes need changes to a contributor in the audit history', function () {
    config(['audit.console' => true]);

    $building = registerBuilding();
    $need = $building->needs()->create([
        'custom_supply_name' => 'Agua',
        'priority' => 'media',
        'status' => NeedStatus::Solicitada,
    ]);

    // Committing flips the need solicitada -> comprometida, which is audited.
    $this->post("/necesidades/{$need->id}/comprometerse", ['name' => 'Carlos']);

    $audit = $need->audits()->latest()->first();

    expect($audit)->not->toBeNull()
        ->and($audit->user_type)->toBe(Contributor::class)
        ->and($audit->user_id)->not->toBeNull();
});

it('updates a building when the version matches', function () {
    $building = registerBuilding(['name' => 'Editable', 'type' => 'residencial']);

    $this->put("/edificios/{$building->slug}", [
        'version' => $building->version,
        'name' => 'Editable Renombrado',
        'type' => 'hospital',
        'address' => 'Nueva dirección 123',
    ])->assertRedirect();

    $fresh = $building->fresh();

    expect($fresh->name)->toBe('Editable Renombrado')
        ->and($fresh->address)->toBe('Nueva dirección 123')
        ->and($fresh->version)->toBe($building->version + 1);
});

it('rejects an edit with a stale version (optimistic lock)', function () {
    $building = registerBuilding(['name' => 'Bloqueo', 'type' => 'residencial']);

    $this->put("/edificios/{$building->slug}", [
        'version' => $building->version + 5,
        'name' => 'No debe pasar',
        'type' => 'residencial',
    ])->assertSessionHasErrors('version');

    expect($building->fresh()->name)->toBe('Bloqueo');
});

it('defaults a new building to "necesita apoyo"', function () {
    $building = registerBuilding(['name' => 'Nuevo', 'type' => 'residencial']);

    expect($building->fresh()->status)->toBe(BuildingStatus::NecesitaApoyo)
        ->and($building->fresh()->status_is_manual)->toBeFalse();
});

it('auto-derives the building status from a critical need', function () {
    $this->seed(SupplyCatalogSeeder::class);
    $building = registerBuilding();

    $this->post("/edificios/{$building->slug}/necesidades", [
        'custom_supply_name' => 'Rescatistas',
        'supply_category_id' => SupplyCategory::firstOrFail()->id,
        'priority' => 'critica',
    ])->assertRedirect();

    expect($building->fresh()->status)->toBe(BuildingStatus::Critico);
});

it('registers a building with only a name (type defaults to otro)', function () {
    $this->post('/edificios', ['name' => 'Solo Nombre'])->assertRedirect();

    $building = Building::firstWhere('name', 'Solo Nombre');

    expect($building)->not->toBeNull()
        ->and($building->type)->toBe(BuildingType::Otro);
});

it('flags near-duplicate building names regardless of case/spacing/accents', function () {
    $this->post('/edificios', ['name' => 'Edificio Petunia', 'community_name' => 'Los Palos Grandes']);

    $response = $this->getJson('/api/edificios-similares?'.http_build_query(['name' => 'edificio  petunia']));

    $response->assertOk();
    expect($response->json())->not->toBeEmpty()
        ->and($response->json()[0]['name'])->toBe('Edificio Petunia');
});

it('respects a manual status override over auto-derivation', function () {
    $this->seed(SupplyCatalogSeeder::class);
    $building = registerBuilding();

    $this->put("/edificios/{$building->slug}", [
        'version' => $building->version,
        'name' => $building->name,
        'type' => 'residencial',
        'status' => 'normal',
    ])->assertRedirect();

    expect($building->fresh()->status)->toBe(BuildingStatus::Normal)
        ->and($building->fresh()->status_is_manual)->toBeTrue();

    // A critical need must NOT override the manual status.
    $this->post("/edificios/{$building->slug}/necesidades", [
        'custom_supply_name' => 'X',
        'supply_category_id' => SupplyCategory::firstOrFail()->id,
        'priority' => 'critica',
    ])->assertRedirect();

    expect($building->fresh()->status)->toBe(BuildingStatus::Normal);
});
