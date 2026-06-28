<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Contributor;
use Illuminate\Http\Request;
use RuntimeException;

abstract class Controller
{
    /**
     * The anonymous contributor resolved by the IdentifyContributor middleware.
     */
    protected function contributor(Request $request): Contributor
    {
        $contributor = $request->attributes->get('contributor');

        if (! $contributor instanceof Contributor) {
            throw new RuntimeException('Contributor not resolved. Is IdentifyContributor middleware applied?');
        }

        return $contributor;
    }
}
