<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MasterUserSeeder extends Seeder
{
    /**
     * The master account (the owner). Credentials are intentionally hardcoded
     * for first boot — change the password after the first login.
     */
    public function run(): void
    {
        $user = User::firstOrNew(['username' => 'MasterVenezolano1']);

        $user->forceFill([
            'name' => 'Master',
            'password' => Hash::make('V3nezuela$Libre#2026!Master'),
            'role' => UserRole::Master->value,
        ])->save();
    }
}
