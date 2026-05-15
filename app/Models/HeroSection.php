<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSection extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'hero_image',
        'stats',
        'is_active',
    ];

    protected $casts = [
        'stats' => 'array',
        'is_active' => 'boolean',
    ];
}
