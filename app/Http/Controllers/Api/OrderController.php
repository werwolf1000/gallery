<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Property;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with(['property'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'property_id' => ['nullable', 'exists:properties,id'],
            'bonus' => ['nullable', 'string', 'max:500'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        if (empty($data['property_id']) && empty($data['bonus'])) {
            throw ValidationException::withMessages([
                'bonus' => ['Укажите объект или активируйте бонус.'],
            ]);
        }

        $property = null;
        $amount = 0;
        $bonus = $data['bonus'] ?? null;

        if (! empty($data['property_id'])) {
            $property = Property::query()
                ->where('is_active', true)
                ->findOrFail($data['property_id']);
            $amount = $property->price;
            $bonus = $bonus ?? $property->bonus;
        }

        $order = Order::create([
            'number' => $this->generateOrderNumber(),
            'user_id' => $request->user()->id,
            'property_id' => $property?->id,
            'status' => 'pending',
            'message' => $data['message'] ?? null,
            'bonus' => $bonus,
            'amount' => $amount,
        ]);

        return response()->json($order->load('property'), 201);
    }

    private function generateOrderNumber(): string
    {
        $lastId = Order::max('id') ?? 0;

        return 'GSP-'.str_pad((string) ($lastId + 1), 3, '0', STR_PAD_LEFT);
    }
}
