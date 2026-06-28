<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Need;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use OwenIt\Auditing\Models\Audit;

class AdminController extends Controller
{
    public function dashboard(Request $request): Response
    {
        $buildingSlug = $request->query('building');

        $audits = Audit::query()
            ->with(['user', 'auditable' => function (MorphTo $morphTo) {
                $morphTo->morphWith([Need::class => ['building'], Building::class => []]);
            }])
            ->when($buildingSlug, fn ($query, $slug) => $this->scopeToBuilding($query, $slug))
            ->latest()
            ->paginate(40)
            ->withQueryString()
            ->through(fn (Audit $audit) => $this->presentAudit($audit));

        return Inertia::render('Admin/Dashboard', [
            'audits' => $audits,
            'filters' => ['building' => $buildingSlug],
            'editsLocked' => Setting::editsLocked(),
            'buildings' => Building::orderBy('name')->get(['slug', 'name']),
        ]);
    }

    public function toggleLock(): RedirectResponse
    {
        Setting::put('edits_locked', Setting::editsLocked() ? '0' : '1');

        return back()->with('success', Setting::editsLocked()
            ? 'Ediciones bloqueadas temporalmente.'
            : 'Ediciones reabiertas.');
    }

    private function scopeToBuilding(Builder $query, string $slug): void
    {
        $building = Building::where('slug', $slug)->first();

        if (! $building) {
            $query->whereRaw('1 = 0');

            return;
        }

        $needIds = $building->needs()->withTrashed()->pluck('id');

        $query->where(function ($where) use ($building, $needIds) {
            $where
                ->where(fn ($w) => $w->where('auditable_type', Building::class)->where('auditable_id', $building->id))
                ->orWhere(fn ($w) => $w->where('auditable_type', Need::class)->whereIn('auditable_id', $needIds));
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function presentAudit(Audit $audit): array
    {
        $auditable = $audit->auditable;

        $target = match (true) {
            $auditable instanceof Building => ['label' => $auditable->name, 'slug' => $auditable->slug, 'kind' => 'Edificio'],
            $auditable instanceof Need => ['label' => $auditable->displayName(), 'slug' => $auditable->building?->slug, 'kind' => 'Necesidad'],
            default => ['label' => 'Registro eliminado #'.$audit->auditable_id, 'slug' => null, 'kind' => 'Eliminado'],
        };

        return [
            'id' => $audit->id,
            'event' => $audit->event,
            'user' => $audit->user?->name ?: 'Anónimo',
            'at' => $audit->created_at?->toIso8601String(),
            'target' => $target,
            'changes' => collect($audit->getModified())
                ->map(fn ($value, $field) => [
                    'field' => $field,
                    'old' => $this->stringify($value['old'] ?? null),
                    'new' => $this->stringify($value['new'] ?? null),
                ])
                ->values()
                ->all(),
        ];
    }

    private function stringify(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \BackedEnum) {
            return (string) $value->value;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : (string) $value;
    }
}
