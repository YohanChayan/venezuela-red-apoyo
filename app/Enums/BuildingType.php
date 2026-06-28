<?php

declare(strict_types=1);

namespace App\Enums;

enum BuildingType: string
{
    case Residencial = 'residencial';
    case Hospital = 'hospital';
    case Refugio = 'refugio';
    case CentroAcopio = 'centro_acopio';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Residencial => 'Edificio residencial',
            self::Hospital => 'Hospital / Centro de salud',
            self::Refugio => 'Refugio',
            self::CentroAcopio => 'Centro de acopio',
            self::Otro => 'Otro',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Residencial => '🏢',
            self::Hospital => '🏥',
            self::Refugio => '⛺',
            self::CentroAcopio => '📦',
            self::Otro => '📍',
        };
    }
}
