<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        return redirect()->intended($this->target($request));
    }

    private function target(Request $request): string
    {
        $user = $request->user();

        return $user && $user->canManage() ? '/admin' : '/cuenta';
    }
}
