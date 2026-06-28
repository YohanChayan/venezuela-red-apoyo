<?php

declare(strict_types=1);

namespace App\Enums;

enum BuildingStatus: string
{
    case SinAsignar = 'sin_asignar';
    case Normal = 'normal';
    case NecesitaApoyo = 'necesita_apoyo';
    case Critico = 'critico';

    public function label(): string
    {
        return match ($this) {
            self::SinAsignar => 'Sin asignar',
            self::Normal => 'Normal',
            self::NecesitaApoyo => 'Necesita apoyo',
            self::Critico => 'Crítico',
        };
    }

    /**
     * Tailwind-friendly semantic color name for the status semaphore.
     */
    public function color(): string
    {
        return match ($this) {
            self::SinAsignar => 'slate',
            self::Normal => 'green',
            self::NecesitaApoyo => 'amber',
            self::Critico => 'red',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::SinAsignar => '⚪',
            self::Normal => '🟢',
            self::NecesitaApoyo => '🟡',
            self::Critico => '🔴',
        };
    }
}
