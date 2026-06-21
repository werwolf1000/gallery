<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Property::query()->where('is_active', true)->orderBy('sort_order');

        if ($request->filled('category')) {
            $query->where('category', $request->string('category'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('min_area')) {
            $query->where('area', '>=', (int) $request->input('min_area'));
        }

        if ($request->filled('max_area')) {
            $query->where('area', '<=', (int) $request->input('max_area'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', (int) $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', (int) $request->input('max_price'));
        }

        return response()->json($query->get());
    }

    public function show(Property $property): JsonResponse
    {
        abort_unless($property->is_active, 404);

        return response()->json($property);
    }
}
