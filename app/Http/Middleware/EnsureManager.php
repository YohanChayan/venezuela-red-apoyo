<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate the management panel to authenticated master/admin users. Guests go to
 * login; logged-in but unassigned users go to their pending-account page.
 */
class EnsureManager
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect('/login');
        }

        if (! $user->canManage()) {
            return redirect('/cuenta');
        }

        return $next($request);
    }
}
