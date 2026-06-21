<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SliderSlide extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image_url',
        'link',
        'button_text',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
