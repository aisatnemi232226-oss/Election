<?php

namespace App\Http\Controllers;

use App\Services\ElectionWidgetService;
use Illuminate\Http\JsonResponse;

class ElectionWidgetController extends Controller
{
    public function __invoke(ElectionWidgetService $widget): JsonResponse
    {
        return response()
            ->json($widget->payload())
            ->header('Cache-Control', 'no-store, private');
    }
}
