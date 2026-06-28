<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ContributorFactory;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contributor extends Model implements Authenticatable
{
    /** @use HasFactory<ContributorFactory> */
    use AuthenticatableTrait, HasFactory;

    protected $fillable = [
        'token',
        'name',
        'phone',
        'trust_level',
        'ip',
        'user_agent',
        'first_seen_at',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function isBanned(): bool
    {
        return $this->trust_level === 'banned';
    }

    public function canModerate(): bool
    {
        return in_array($this->trust_level, ['moderator', 'admin'], true);
    }
}
