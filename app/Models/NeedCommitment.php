<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\NeedStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NeedCommitment extends Model
{
    protected $fillable = [
        'need_id',
        'contributor_id',
        'name',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => NeedStatus::class,
        ];
    }

    /**
     * @return BelongsTo<Need, $this>
     */
    public function need(): BelongsTo
    {
        return $this->belongsTo(Need::class);
    }

    /**
     * @return BelongsTo<Contributor, $this>
     */
    public function contributor(): BelongsTo
    {
        return $this->belongsTo(Contributor::class);
    }
}
