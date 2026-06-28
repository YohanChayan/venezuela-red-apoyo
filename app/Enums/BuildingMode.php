<?php

declare(strict_types=1);

namespace App\Enums;

enum BuildingMode: string
{
    case Rescate = 'rescate';
    case Abastecimiento = 'abastecimiento';

    public function label(): string
    {
        return match ($this) {
            self::Rescate => 'Rescate',
            self::Abastecimiento => 'Abastecimiento',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Rescate => '🆘',
            self::Abastecimiento => '📦',
        };
    }
}
