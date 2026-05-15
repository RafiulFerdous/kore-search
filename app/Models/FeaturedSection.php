<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeaturedSection extends Model
{
    protected $fillable = [
        'course_ids',
        'is_active',
    ];

    protected $casts = [
        'course_ids' => 'array',
        'is_active' => 'boolean',
    ];
}
