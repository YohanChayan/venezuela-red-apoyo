<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreFeedbackRequest;
use App\Models\Feedback;
use Illuminate\Http\RedirectResponse;

class FeedbackController extends Controller
{
    /**
     * Store an anonymous piece of feedback (suggestion, problem or comment).
     */
    public function store(StoreFeedbackRequest $request): RedirectResponse
    {
        Feedback::create([
            ...$request->validated(),
            'contributor_id' => $this->contributor($request)->id,
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
        ]);

        return back()->with('success', '¡Gracias! Tu mensaje nos ayuda a mejorar.');
    }
}
