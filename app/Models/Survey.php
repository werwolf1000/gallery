<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    public const STATUSES = [
        'active' => 'Активен',
        'draft' => 'Черновик',
        'inactive' => 'Неактивен',
    ];

    public const CATEGORIES = [
        'service' => 'Качество обслуживания',
        'consultation' => 'Консультация',
        'site' => 'Оценка сайта',
        'recommendation' => 'Рекомендации',
        'other' => 'Другое',
    ];

    protected $fillable = [
        'title',
        'description',
        'status',
        'category',
        'questions',
        'responses_count',
    ];

    protected function casts(): array
    {
        return [
            'questions' => 'array',
            'responses_count' => 'integer',
        ];
    }
}
