<?php

declare(strict_types=1);

namespace App\Enums;

enum NeedStatus: string
{
    case Solicitada = 'solicitada';
    case Comprometida = 'comprometida';
    case EnCamino = 'en_camino';
    case Entregada = 'entregada';
    case Confirmada = 'confirmada';
    case Cancelada = 'cancelada';

    public function label(): string
    {
        return match ($this) {
            self::Solicitada => 'Solicitada',
            self::Comprometida => 'Asignada',
            self::EnCamino => 'En camino',
            self::Entregada => 'Entregada',
            self::Confirmada => 'Resuelta',
            self::Cancelada => 'Cancelada',
        };
    }

    /**
     * The anti-chaos lifecycle state machine. Only these transitions are valid.
     *
     * @return array<int, self>
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::Solicitada => [self::Comprometida, self::Cancelada],
            self::Comprometida => [self::EnCamino, self::Solicitada, self::Cancelada],
            self::EnCamino => [self::Entregada, self::Comprometida, self::Cancelada],
            self::Entregada => [self::Confirmada, self::EnCamino],
            self::Confirmada => [],
            self::Cancelada => [self::Solicitada],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }

    public function isOpen(): bool
    {
        return ! in_array($this, [self::Confirmada, self::Cancelada], true);
    }
}
