<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Users', [
            'users' => User::query()
                ->orderByRaw("CASE role WHEN 'master' THEN 0 WHEN 'admin' THEN 1 ELSE 2 END")
                ->orderBy('username')
                ->get()
                ->map(fn (User $user) => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'role' => $user->role->value,
                    'roleLabel' => $user->role->label(),
                    'isMaster' => $user->isMaster(),
                ]),
            'assignableRoles' => [
                ['value' => UserRole::Admin->value, 'label' => UserRole::Admin->label()],
                ['value' => UserRole::Unassigned->value, 'label' => UserRole::Unassigned->label()],
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:255', 'alpha_dash', Rule::unique('users', 'username')],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'role' => ['required', Rule::in([UserRole::Admin->value, UserRole::Unassigned->value])],
        ]);

        (new User)->forceFill([
            'username' => $data['username'],
            'name' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ])->save();

        return back()->with('success', "Cuenta «{$data['username']}» creada.");
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role' => ['required', Rule::in([UserRole::Admin->value, UserRole::Unassigned->value])],
        ]);

        abort_if($user->isMaster(), 403, 'No se puede cambiar el rol del master.');

        $user->forceFill(['role' => $data['role']])->save();

        return back()->with('success', 'Rol actualizado.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->isMaster(), 403, 'No se puede eliminar al master.');

        $user->delete();

        return back()->with('success', 'Cuenta eliminada.');
    }
}
