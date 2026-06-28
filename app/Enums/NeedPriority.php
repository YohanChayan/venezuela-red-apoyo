<?php

declare(strict_types=1);

namespace App\Enums;

enum NeedPriority: string
{
    case Baja = 'baja';
    case Media = 'media';
    case Alta = 'alta';
    case Critica = 'critica';

    public function label(): string
    {
        return match ($this) {
            self::Baja => 'Baja',
            self::Media => 'Media',
            self::Alta => 'Alta',
            self::Critica => 'Crítica',
        };
    }

    public function weight(): int
    {
        return match ($this) {
            self::Baja => 1,
            self::Media => 2,
            self::Alta => 3,
            self::Critica => 4,
        };
    }
}
