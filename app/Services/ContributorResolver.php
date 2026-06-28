<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contributor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Resolves the anonymous contributor behind a request from a device-token
 * cookie, creating one on first contact. No registration, but every public
 * action stays attributable and revocable.
 */
class ContributorResolver
{
    public const COOKIE = 'contributor_token';

    /**
     * @return array{0: Contributor, 1: string} the contributor and its token
     */
    public function resolve(Request $request): array
    {
        $token = $request->cookie(self::COOKIE);

        if (! is_string($token) || $token === '') {
            $token = Str::random(48);
        }

        $contributor = Contributor::firstOrCreate(
            ['token' => $token],
            ['trust_level' => 'normal', 'first_seen_at' => now()],
        );

        // We deliberately do not store IP or user-agent — only a last-seen stamp.
        $contributor->forceFill(['last_seen_at' => now()])->save();

        return [$contributor, $token];
    }
}
