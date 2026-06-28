<?php

declare(strict_types=1);

namespace App\Resolvers;

use App\Models\Contributor;
use Illuminate\Contracts\Auth\Authenticatable;
use OwenIt\Auditing\Contracts\UserResolver;

/**
 * Attributes each audited change to the current anonymous contributor so the
 * per-building history answers "who did it" without requiring a login.
 */
class ContributorAuditResolver implements UserResolver
{
    public static function resolve(): ?Authenticatable
    {
        $contributor = request()->attributes->get('contributor');

        return $contributor instanceof Contributor ? $contributor : null;
    }
}
