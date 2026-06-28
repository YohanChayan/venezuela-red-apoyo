<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Building;
use App\Models\Setting;
use App\Models\User;

function userWithRole(UserRole $role): User
{
    $user = User::factory()->create();
    $user->forceFill(['role' => $role->value])->save();

    return $user;
}

it('disables public registration', function () {
    $this->get('/register')->assertNotFound();
    $this->post('/register', ['username' => 'x', 'password' => 'password1234'])->assertNotFound();
});

it('logs in with a username and password', function () {
    $user = User::factory()->create(['username' => 'gestor1']);
    $user->forceFill(['role' => UserRole::Admin->value])->save();

    $this->post('/login', ['username' => 'gestor1', 'password' => 'password'])->assertRedirect();
    $this->assertAuthenticated();
});

it('routes users to the panel or pending page by role', function () {
    $this->get('/admin')->assertRedirect('/login');

    $this->actingAs(userWithRole(UserRole::Unassigned))->get('/admin')->assertRedirect('/cuenta');
    $this->actingAs(userWithRole(UserRole::Admin))->get('/admin')->assertOk();
});

it('lets only the master create and manage accounts', function () {
    $this->actingAs(userWithRole(UserRole::Admin))->get('/admin/usuarios')->assertForbidden();

    $master = userWithRole(UserRole::Master);
    $this->actingAs($master)->get('/admin/usuarios')->assertOk();

    $this->actingAs($master)
        ->post('/admin/usuarios', ['username' => 'nuevo_gestor', 'password' => 'claveSegura1', 'role' => 'admin'])
        ->assertRedirect();

    expect(User::firstWhere('username', 'nuevo_gestor')?->role)->toBe(UserRole::Admin);
});

it('protects the master account from deletion', function () {
    $master = userWithRole(UserRole::Master);

    $this->actingAs($master)->delete("/admin/usuarios/{$master->id}")->assertForbidden();
});

it('blocks public writes when edits are globally locked', function () {
    Setting::put('edits_locked', '1');

    $this->post('/edificios', ['name' => 'Bloqueado'])->assertStatus(423);
});

it('lets a manager write even when edits are locked', function () {
    Setting::put('edits_locked', '1');

    $this->actingAs(userWithRole(UserRole::Admin))
        ->post('/edificios', ['name' => 'Edificio del gestor'])
        ->assertRedirect();
});

it('does not store IP addresses in the audit log', function () {
    config(['audit.console' => true]);

    $this->post('/edificios', ['name' => 'Sin IP', 'type' => 'residencial']);
    $building = Building::firstWhere('name', 'Sin IP');

    expect($building->audits()->latest()->first()->ip_address)->toBe('');
});
