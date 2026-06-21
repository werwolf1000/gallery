<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    public const CATEGORIES = [
        'purchase' => 'Покупка',
        'rent' => 'Аренда',
        'izhs' => 'Строительство ИЖС',
        'renovation' => 'Ремонт',
        'apartment' => 'Квартиры',
        'home_staging' => 'Хоумстейджинг',
    ];

    protected $fillable = [
        'title',
        'address',
        'category',
        'type',
        'area',
        'rooms',
        'floor',
        'price',
        'bonus',
        'image_url',
        'badge',
        'status',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'price' => 'integer',
            'area' => 'integer',
            'rooms' => 'integer',
            'floor' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
