<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\ContributorResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyContributor
{
    public function __construct(private readonly ContributorResolver $resolver) {}

    public function handle(Request $request, Closure $next): Response
    {
        [$contributor, $token] = $this->resolver->resolve($request);

        $request->attributes->set('contributor', $contributor);

        $response = $next($request);

        return $response->withCookie(
            cookie()->forever(ContributorResolver::COOKIE, $token, httpOnly: true)
        );
    }
}
