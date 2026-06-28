<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\BuildingMode;
use App\Enums\BuildingStatus;
use App\Enums\BuildingType;
use App\Enums\NeedPriority;
use App\Enums\NeedStatus;
use App\Http\Requests\StoreBuildingRequest;
use App\Http\Requests\UpdateBuildingRequest;
use App\Http\Resources\BuildingResource;
use App\Http\Resources\BuildingSummaryResource;
use App\Http\Resources\SupplyCategoryResource;
use App\Models\Building;
use App\Models\Community;
use App\Models\Need;
use App\Models\SupplyCategory;
use App\Services\BuildingService;
use App\Services\DuplicateFinder;
use App\Support\EnumOptions;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;
use OwenIt\Auditing\Models\Audit;

class BuildingController extends Controller
{
    /**
     * How long the homepage counters are cached. Short enough to feel live,
     * long enough to spare the database a dozen queries per visit.
     */
    private const STATS_TTL_SECONDS = 20;

    public function __construct(private readonly BuildingService $buildings) {}

    public function index(Request $request): Response
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'type' => $request->query('type'),
            'status' => $request->query('status'),
            'needStatus' => $request->query('needStatus'),
            'category' => $request->query('category'),
        ];

        $openNeed = [NeedStatus::Confirmada->value, NeedStatus::Cancelada->value];

        $buildings = Building::query()
            ->with(['community', 'needs'])
            ->when($filters['q'] !== '', function ($query) use ($filters) {
                $term = '%'.$filters['q'].'%';
                $query->where(function ($where) use ($term) {
                    $where->where('name', 'like', $term)
                        ->orWhere('address', 'like', $term)
                        ->orWhereHas('community', fn ($community) => $community->where('name', 'like', $term));
                });
            })
            ->when($filters['type'], fn ($query, $type) => $query->where('type', $type))
            ->when($filters['status'], fn ($query, $status) => $query->where('status', $status))
            ->when($filters['needStatus'], fn ($query, $needStatus) => $query->whereHas(
                'needs',
                fn ($need) => $need->where('status', $needStatus),
            ))
            ->when($filters['category'], fn ($query, $category) => $query->whereHas(
                'needs',
                fn ($need) => $need->where('supply_category_id', $category)->whereNotIn('status', $openNeed),
            ))
            // Sectors with more buildings first (keeps grouping consistent across pages).
            ->orderByRaw('(select count(*) from buildings as b2 where b2.community_id = buildings.community_id and b2.deleted_at is null) desc')
            ->orderByRaw("CASE status WHEN 'critico' THEN 0 WHEN 'necesita_apoyo' THEN 1 WHEN 'normal' THEN 2 ELSE 3 END")
            ->orderByDesc('last_reported_at')
            ->paginate(24)
            ->withQueryString()
            ->through(fn (Building $building) => (new BuildingSummaryResource($building))->resolve());

        return Inertia::render('Buildings/Index', [
            'buildings' => $buildings,
            'stats' => $this->buildStats(),
            'needsSummary' => $this->buildNeedsSummary(),
            'filters' => $filters,
            'options' => [
                'types' => EnumOptions::from(BuildingType::cases()),
                'statuses' => EnumOptions::from(BuildingStatus::cases()),
            ],
        ]);
    }

    public function store(StoreBuildingRequest $request): RedirectResponse
    {
        $building = $this->buildings->register($request->validated(), $this->contributor($request));

        return redirect()
            ->route('buildings.show', $building)
            ->with('success', '¡Gracias! El edificio quedó registrado.');
    }

    public function show(Building $building): Response
    {
        $building->load(['community', 'needs.category', 'needs.supply', 'needs.claimedBy', 'needs.commitments']);

        return Inertia::render('Buildings/Show', [
            'building' => new BuildingResource($building),
            'supplyCategories' => SupplyCategoryResource::collection(
                SupplyCategory::with('supplies')->orderBy('sort_order')->get()
            ),
            'priorities' => EnumOptions::from(NeedPriority::cases()),
            'history' => $this->buildHistory($building),
        ]);
    }

    public function update(UpdateBuildingRequest $request, Building $building): RedirectResponse
    {
        $this->buildings->update($building, $request->validated(), $this->contributor($request));

        return redirect()
            ->route('buildings.show', $building)
            ->with('success', 'Edificio actualizado.');
    }

    /**
     * Returns possible duplicates for a typed name (warning, never blocks).
     */
    public function similar(Request $request, DuplicateFinder $finder): JsonResponse
    {
        $matches = $finder->findSimilar(
            (string) $request->query('name', ''),
            $request->query('community'),
        );

        return response()->json(
            $matches->map(fn (Building $building) => [
                'name' => $building->name,
                'slug' => $building->slug,
                'sector' => $building->community?->name,
                'status' => $building->status->label(),
            ])->all(),
        );
    }

    /**
     * Homepage counters. Cached briefly so the list view doesn't fire a dozen
     * COUNT queries against the (remote) database on every request. Each block
     * is collapsed into a single aggregate query via conditional sums.
     *
     * @return array<string, int>
     */
    private function buildStats(): array
    {
        return Cache::remember('home:stats', now()->addSeconds(self::STATS_TTL_SECONDS), function (): array {
            $closed = [NeedStatus::Confirmada->value, NeedStatus::Cancelada->value];

            $buildings = Building::query()
                ->selectRaw('count(*) as total')
                ->selectRaw('sum(case when status = ? then 1 else 0 end) as criticos', [BuildingStatus::Critico->value])
                ->selectRaw('sum(case when mode = ? then 1 else 0 end) as rescate', [BuildingMode::Rescate->value])
                ->selectRaw('sum(case when type = ? then 1 else 0 end) as hospitales', [BuildingType::Hospital->value])
                ->first();

            $needs = Need::query()
                ->selectRaw('sum(case when status not in (?, ?) then 1 else 0 end) as abiertas', $closed)
                ->selectRaw('sum(case when priority = ? and status not in (?, ?) then 1 else 0 end) as criticas', [NeedPriority::Critica->value, ...$closed])
                ->first();

            return [
                'total' => (int) $buildings->total,
                'criticos' => (int) $buildings->criticos,
                'rescate' => (int) $buildings->rescate,
                'sectores' => Community::count(),
                'necesidadesAbiertas' => (int) $needs->abiertas,
                'necesidadesCriticas' => (int) $needs->criticas,
                'hospitales' => (int) $buildings->hospitales,
            ];
        });
    }

    /**
     * @return array<string, mixed>
     */
    private function buildNeedsSummary(): array
    {
        return Cache::remember('home:needs-summary', now()->addSeconds(self::STATS_TTL_SECONDS), function (): array {
            $closed = [NeedStatus::Confirmada->value, NeedStatus::Cancelada->value];

            $counts = Need::query()
                ->selectRaw('status, count(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $byStatus = collect([
                NeedStatus::Solicitada,
                NeedStatus::Comprometida,
                NeedStatus::EnCamino,
                NeedStatus::Entregada,
                NeedStatus::Confirmada,
            ])->map(fn (NeedStatus $status) => [
                'value' => $status->value,
                'label' => $status->label(),
                'count' => (int) ($counts[$status->value] ?? 0),
            ])->all();

            // Derived from the grouped counts above — no extra query.
            $open = (int) $counts
                ->reject(fn (int $total, string $status) => in_array($status, $closed, true))
                ->sum();

            $categoryCounts = Need::query()
                ->whereNotIn('status', $closed)
                ->whereNotNull('supply_category_id')
                ->selectRaw('supply_category_id, count(*) as total')
                ->groupBy('supply_category_id')
                ->orderByDesc('total')
                ->limit(6)
                ->pluck('total', 'supply_category_id');

            $topCategories = SupplyCategory::query()
                ->whereIn('id', $categoryCounts->keys())
                ->get(['id', 'name', 'icon'])
                ->map(fn (SupplyCategory $category) => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'icon' => $category->icon,
                    'count' => (int) $categoryCounts[$category->id],
                ])
                ->sortByDesc('count')
                ->values()
                ->all();

            return [
                'lastHour' => Need::where('created_at', '>=', now()->subHour())->count(),
                'open' => $open,
                'byStatus' => $byStatus,
                'topCategories' => $topCategories,
            ];
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildHistory(Building $building): array
    {
        $needIds = $building->needs()->withTrashed()->pluck('id');

        return Audit::query()
            ->with(['user', 'auditable' => fn (MorphTo $morphTo) => $morphTo->morphWith([Need::class => ['supply']])])
            ->where(function ($query) use ($building, $needIds) {
                $query
                    ->where(fn ($w) => $w->where('auditable_type', Building::class)->where('auditable_id', $building->id))
                    ->orWhere(fn ($w) => $w->where('auditable_type', Need::class)->whereIn('auditable_id', $needIds));
            })
            ->latest()
            ->limit(30)
            ->get()
            ->map(fn (Audit $audit) => $this->presentActivity($audit))
            ->all();
    }

    /**
     * Turn an audit into a human, public-friendly activity line.
     *
     * @return array<string, mixed>
     */
    private function presentActivity(Audit $audit): array
    {
        $user = $audit->user?->name ?: 'Anónimo';

        if ($audit->auditable_type === Need::class) {
            $name = $audit->auditable?->displayName() ?? 'una necesidad';
            $newStatus = $audit->getModified()['status']['new'] ?? null;
            if ($newStatus instanceof \BackedEnum) {
                $newStatus = $newStatus->value;
            }

            $summary = match (true) {
                $audit->event === 'created' => "agregó la necesidad «{$name}»",
                $audit->event === 'deleted' => "quitó la necesidad «{$name}»",
                $newStatus === 'comprometida' => "se encargó de «{$name}»",
                $newStatus === 'en_camino' => "va en camino con «{$name}»",
                $newStatus === 'entregada' => "entregó «{$name}»",
                $newStatus === 'confirmada' => "resolvió «{$name}»",
                $newStatus === 'cancelada' => "canceló la necesidad «{$name}»",
                $newStatus === 'solicitada' => "reabrió la necesidad «{$name}»",
                default => "actualizó la necesidad «{$name}»",
            };
        } else {
            $summary = match ($audit->event) {
                'created' => 'registró este lugar',
                'deleted' => 'eliminó este lugar',
                default => 'actualizó los datos del lugar',
            };
        }

        return [
            'id' => $audit->id,
            'user' => $user,
            'summary' => $summary,
            'at' => $audit->created_at?->toIso8601String(),
        ];
    }
}
