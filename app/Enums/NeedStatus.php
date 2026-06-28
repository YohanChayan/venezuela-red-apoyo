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

    /**
     * The lifecycle for a single person's commitment. A person who commits
     * starts at "comprometida" (committing IS the request being claimed), so
     * "solicitada" never applies to an individual commitment.
     *
     * @return array<int, self>
     */
    public function commitmentTransitions(): array
    {
        return match ($this) {
            self::Solicitada => [],
            self::Comprometida => [self::EnCamino, self::Cancelada],
            self::EnCamino => [self::Entregada, self::Comprometida, self::Cancelada],
            self::Entregada => [self::Confirmada, self::EnCamino],
            self::Confirmada => [],
            self::Cancelada => [self::Comprometida],
        };
    }

    public function canCommitmentTransitionTo(self $target): bool
    {
        return in_array($target, $this->commitmentTransitions(), true);
    }

    /**
     * Progress rank along the lifecycle, used to derive a need's overall status
     * from its commitments (the most advanced status wins ties).
     */
    public function order(): int
    {
        return match ($this) {
            self::Cancelada => -1,
            self::Solicitada => 0,
            self::Comprometida => 1,
            self::EnCamino => 2,
            self::Entregada => 3,
            self::Confirmada => 4,
        };
    }

    public function isOpen(): bool
    {
        return ! in_array($this, [self::Confirmada, self::Cancelada], true);
    }
}
