<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SliderSlide;
use Illuminate\Http\JsonResponse;

class SliderController extends Controller
{
    public function index(): JsonResponse
    {
        $slides = SliderSlide::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return response()->json($slides);
    }
}
