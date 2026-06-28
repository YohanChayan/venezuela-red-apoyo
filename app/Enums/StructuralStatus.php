<?php

declare(strict_types=1);

namespace App\Enums;

enum StructuralStatus: string
{
    case Seguro = 'seguro';
    case Danado = 'danado';
    case Colapsado = 'colapsado';
    case SinEvaluar = 'sin_evaluar';

    public function label(): string
    {
        return match ($this) {
            self::Seguro => 'Seguro',
            self::Danado => 'Dañado',
            self::Colapsado => 'Colapsado',
            self::SinEvaluar => 'Sin evaluar',
        };
    }
}
