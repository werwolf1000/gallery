<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Property;
use App\Models\SliderSlide;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    public function orders(): JsonResponse
    {
        $orders = Order::with(['user', 'property'])->latest()->get();
        $stats = [
            'pending' => Order::where('status', 'pending')->count(),
            'active' => Order::where('status', 'active')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return response()->json(['orders' => $orders, 'stats' => $stats]);
    }

    public function updateOrder(Request $request, Order $order): JsonResponse
    {
        $data = $request->validate([
            'status' => ['sometimes', Rule::in(['pending', 'active', 'completed', 'cancelled'])],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        $order->update($data);

        return response()->json($order->load(['user', 'property']));
    }

    public function destroyOrder(Order $order): JsonResponse
    {
        $order->delete();

        return response()->json(['message' => 'Заказ удалён']);
    }

    public function users(): JsonResponse
    {
        $users = User::withCount('orders')->orderByDesc('created_at')->get();

        return response()->json($users);
    }

    public function updateUser(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_blocked' => ['sometimes', 'boolean'],
            'role' => ['sometimes', Rule::in(['user', 'admin'])],
            'password' => ['nullable', Password::defaults()],
        ]);

        if (isset($data['password']) && $data['password'] === '') {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json($user->loadCount('orders'));
    }

    public function storeUser(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', Password::defaults()],
            'role' => ['nullable', Rule::in(['user', 'admin'])],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'role' => $data['role'] ?? 'user',
        ]);

        return response()->json($user, 201);
    }

    public function properties(): JsonResponse
    {
        return response()->json(Property::orderBy('sort_order')->get());
    }

    public function storeProperty(Request $request): JsonResponse
    {
        $data = $this->validateProperty($request);
        $property = Property::create($data);

        return response()->json($property, 201);
    }

    public function updateProperty(Request $request, Property $property): JsonResponse
    {
        $property->update($this->validateProperty($request));

        return response()->json($property);
    }

    public function destroyProperty(Property $property): JsonResponse
    {
        $property->delete();

        return response()->json(['message' => 'Объект удалён']);
    }

    public function slides(): JsonResponse
    {
        return response()->json(SliderSlide::orderBy('sort_order')->get());
    }

    public function storeSlide(Request $request): JsonResponse
    {
        $slide = SliderSlide::create($this->validateSlide($request));

        return response()->json($slide, 201);
    }

    public function updateSlide(Request $request, SliderSlide $slide): JsonResponse
    {
        $slide->update($this->validateSlide($request));

        return response()->json($slide);
    }

    public function destroySlide(SliderSlide $slide): JsonResponse
    {
        $slide->delete();

        return response()->json(['message' => 'Слайд удалён']);
    }

    private function validateProperty(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'category' => ['nullable', 'string', Rule::in(array_keys(Property::CATEGORIES))],
            'type' => ['nullable', 'string', 'max:50'],
            'area' => ['nullable', 'integer', 'min:0'],
            'rooms' => ['nullable', 'integer', 'min:0'],
            'floor' => ['nullable', 'integer', 'min:0'],
            'price' => ['required', 'integer', 'min:0'],
            'bonus' => ['nullable', 'string', 'max:500'],
            'image_url' => ['nullable', 'string', 'max:1000'],
            'badge' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function validateSlide(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:500'],
            'image_url' => ['required', 'string', 'max:1000'],
            'link' => ['nullable', 'string', 'max:500'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
