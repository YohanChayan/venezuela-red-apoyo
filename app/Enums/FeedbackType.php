<?php

declare(strict_types=1);

namespace App\Enums;

enum FeedbackType: string
{
    case Sugerencia = 'sugerencia';
    case Problema = 'problema';
    case Comentario = 'comentario';

    public function label(): string
    {
        return match ($this) {
            self::Sugerencia => 'Sugerencia',
            self::Problema => 'Problema o error',
            self::Comentario => 'Comentario',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::Sugerencia => '💡',
            self::Problema => '🐞',
            self::Comentario => '💬',
        };
    }
}
