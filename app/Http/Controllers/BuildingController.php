<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\BuildingMode;
use App\Enums\BuildingStatus;
use App\Enums\BuildingType;
use App\Enums\NeedPriority;
use App\Enums\NeedStatus;
use App\Enums\StructuralStatus;
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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BuildingController extends Controller
{
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

    public function create(): Response
    {
        return Inertia::render('Buildings/Create', [
            'options' => [
                'types' => EnumOptions::from(BuildingType::cases()),
                'statuses' => EnumOptions::from(BuildingStatus::cases()),
                'modes' => EnumOptions::from(BuildingMode::cases()),
                'structuralStatuses' => EnumOptions::from(StructuralStatus::cases()),
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
     * @return array<string, int>
     */
    private function buildStats(): array
    {
        $closed = [NeedStatus::Confirmada->value, NeedStatus::Cancelada->value];

        return [
            'total' => Building::count(),
            'criticos' => Building::where('status', BuildingStatus::Critico->value)->count(),
            'rescate' => Building::where('mode', BuildingMode::Rescate->value)->count(),
            'sectores' => Community::count(),
            'necesidadesAbiertas' => Need::whereNotIn('status', $closed)->count(),
            'necesidadesCriticas' => Need::where('priority', NeedPriority::Critica->value)
                ->whereNotIn('status', $closed)
                ->count(),
            'hospitales' => Building::where('type', BuildingType::Hospital->value)->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildNeedsSummary(): array
    {
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
            'open' => Need::whereNotIn('status', $closed)->count(),
            'byStatus' => $byStatus,
            'topCategories' => $topCategories,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildHistory(Building $building): array
    {
        return $building->audits()->with('user')->latest()->limit(25)->get()->map(fn ($audit) => [
            'id' => $audit->id,
            'event' => $audit->event,
            'user' => $audit->user?->name ?: 'Anónimo',
            'at' => $audit->created_at?->toIso8601String(),
            'changes' => collect($audit->getModified())->map(fn ($value, $field) => [
                'field' => $field,
                'old' => $value['old'] ?? null,
                'new' => $value['new'] ?? null,
            ])->values()->all(),
        ])->all();
    }
}
