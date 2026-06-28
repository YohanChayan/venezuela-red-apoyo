<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restrict an action to the master (the owner) — e.g. assigning roles.
 */
class EnsureMaster
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect('/login');
        }

        abort_unless($user->isMaster(), 403);

        return $next($request);
    }
}
