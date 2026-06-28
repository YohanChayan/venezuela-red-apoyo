<?php

declare(strict_types=1);

namespace App\Resolvers;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Resolver;

/**
 * Keeps IP addresses and user agents out of the audit log entirely — we only
 * record what changed, who (anonymous contributor) and when.
 */
class PrivacyResolver implements Resolver
{
    public static function resolve(Auditable $auditable): string
    {
        return '';
    }
}
