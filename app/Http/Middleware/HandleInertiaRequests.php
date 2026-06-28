<?php

namespace App\Http\Middleware;

use App\Enums\BuildingStatus;
use App\Enums\BuildingType;
use App\Enums\StructuralStatus;
use App\Models\Community;
use App\Support\EnumOptions;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $contributor = $request->attributes->get('contributor');
        $user = $request->user();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'role' => $user->role->value,
                    'roleLabel' => $user->role->label(),
                    'canManage' => $user->canManage(),
                    'isMaster' => $user->isMaster(),
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'contributor' => $contributor ? [
                'name' => $contributor->name,
            ] : null,
            'buildingOptions' => [
                'types' => EnumOptions::from(BuildingType::cases()),
                'statuses' => EnumOptions::from(BuildingStatus::cases()),
                'structuralStatuses' => EnumOptions::from(StructuralStatus::cases()),
                'sectors' => fn () => Community::orderBy('name')->pluck('name'),
            ],
        ];
    }
}
