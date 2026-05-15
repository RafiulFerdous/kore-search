<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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

    public function ratings()
    {
        return $this->hasMany(CourseRating::class);
    }

    public function isFree(): bool
    {
        return $this->price == 0;
    }

    public function recalculateRating(): void
    {
        $avg = $this->ratings()->avg('rating');
        $this->update(['rating' => round($avg, 2)]);
        Cache::forget('course.' . $this->slug);
        Cache::increment('courses.version');
    }

    public function userRating(User $user): ?CourseRating
    {
        return $this->ratings()->where('user_id', $user->id)->first();
    }

    public function isPurchasedBy(User $user): bool
    {
        return $this->orders()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->exists();
    }

    public function isInCart(): bool
    {
        return in_array($this->id, session()->get('cart', []));
    }
}
