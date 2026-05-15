<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'category',
        'level',
        'price',
        'is_published',
        'enrolled_count',
        'rating',
        'duration',
        'topics',
    ];

    protected $casts = [
        'topics'       => 'array',
        'is_published' => 'boolean',
        'price'        => 'decimal:2',
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isFree(): bool
    {
        return $this->price == 0;
    }
}
