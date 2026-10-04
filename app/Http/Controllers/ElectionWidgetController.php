<?php

namespace App\Http\Controllers;

use App\Services\ElectionWidgetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ElectionWidgetController extends Controller
{
    public function __invoke(Request $request, ElectionWidgetService $widget): JsonResponse
    {
        $validated = $request->validate([
            'election_id' => ['nullable', 'integer', 'exists:election,id'],
        ]);

        return response()
            ->json($widget->payload(isset($validated['election_id']) ? (int) $validated['election_id'] : null))
            ->header('Cache-Control', 'no-store, private');
    }
}
