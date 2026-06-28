<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gatekeeper for public write routes: managers always pass; everyone else is
 * stopped by the temporary global edit lock. Abuse is otherwise handled by
 * rate limiting — we keep no IP or device records.
 */
class EnsureWritable
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user && $user->canManage()) {
            return $next($request);
        }

        if (Setting::editsLocked()) {
            abort(423, 'Las ediciones están bloqueadas temporalmente por mantenimiento.');
        }

        return $next($request);
    }
}
