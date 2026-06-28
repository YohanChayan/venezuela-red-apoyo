<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreNeedRequest;
use App\Http\Requests\StoreNeedsRequest;
use App\Http\Requests\UpdateNeedStatusRequest;
use App\Models\Building;
use App\Models\Need;
use App\Services\NeedService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NeedController extends Controller
{
    public function __construct(private readonly NeedService $needs) {}

    public function store(StoreNeedRequest $request, Building $building): RedirectResponse
    {
        $this->needs->add($building, $request->validated(), $this->contributor($request));

        return back()->with('success', 'Necesidad agregada.');
    }

    public function storeBatch(StoreNeedsRequest $request, Building $building): RedirectResponse
    {
        $count = $this->needs->addMany($building, $request->validated()['needs'], $this->contributor($request));

        $message = $count === 1 ? 'Se agregó 1 necesidad.' : "Se agregaron {$count} necesidades.";

        return back()->with('success', $message);
    }

    public function updateStatus(UpdateNeedStatusRequest $request, Need $need): RedirectResponse
    {
        $this->needs->transition($need, $request->targetStatus(), $this->contributor($request));

        return back()->with('success', 'Estado de la necesidad actualizado.');
    }

    public function commit(Request $request, Need $need): RedirectResponse
    {
        $data = $request->validate(['name' => ['nullable', 'string', 'max:255']]);

        $this->needs->commit($need, $this->contributor($request), $data['name'] ?? null);

        return back()->with('success', 'Te anotaste para esta necesidad. ¡Gracias!');
    }
}
