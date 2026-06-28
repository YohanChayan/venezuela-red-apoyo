<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case Master = 'master';
    case Admin = 'admin';
    case Unassigned = 'unassigned';

    public function label(): string
    {
        return match ($this) {
            self::Master => 'Master',
            self::Admin => 'Administrador',
            self::Unassigned => 'Sin asignar',
        };
    }
}
