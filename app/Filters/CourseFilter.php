<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CourseFilter
{
    protected array $filters = [
        'search'    => 'applySearch',
        'category'  => 'applyCategory',
        'level'     => 'applyLevel',
        'price_min' => 'applyPriceMin',
        'price_max' => 'applyPriceMax',
        'rating'    => 'applyRating',
    ];

    public function apply(Builder $query, Request $request): Builder
    {
        foreach ($this->filters as $key => $method) {
            if ($request->filled($key)) {
                $this->$method($query, $request->input($key));
            }
        }

        return $query;
    }

    public function applySorting(Builder $query, Request $request): Builder
    {
        return match ($request->input('sort', 'newest')) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating'     => $query->orderBy('rating', 'desc'),
            'popular'    => $query->orderBy('enrolled_count', 'desc'),
            default      => $query->latest(),
        };
    }

    public function activeFilters(Request $request): Collection
    {
        $labels = [];

        if ($request->filled('search')) {
            $labels[] = ['key' => 'search', 'label' => 'Search: "' . e($request->search) . '"'];
        }

        if ($request->filled('category')) {
            $labels[] = ['key' => 'category', 'label' => $request->category];
        }

        if ($request->filled('level')) {
            $labels[] = ['key' => 'level', 'label' => ucfirst($request->level)];
        }

        if ($request->filled('price_min') && $request->filled('price_max')) {
            $labels[] = ['key' => 'price', 'label' => '৳' . $request->price_min . '–৳' . $request->price_max];
        } elseif ($request->filled('price_min')) {
            $labels[] = ['key' => 'price_min', 'label' => 'Min ৳' . $request->price_min];
        } elseif ($request->filled('price_max')) {
            $labels[] = ['key' => 'price_max', 'label' => 'Max ৳' . $request->price_max];
        }

        if ($request->filled('rating')) {
            $labels[] = ['key' => 'rating', 'label' => $request->rating . '+ Stars'];
        }

        return collect($labels);
    }

    protected function applySearch(Builder $query, string $value): void
    {
        $query->where(function (Builder $q) use ($value) {
            $q->where('title', 'like', "%{$value}%")
              ->orWhere('description', 'like', "%{$value}%");
        });
    }

    protected function applyCategory(Builder $query, string $value): void
    {
        $query->where('category', $value);
    }

    protected function applyLevel(Builder $query, string $value): void
    {
        $query->where('level', $value);
    }

    protected function applyPriceMin(Builder $query, string $value): void
    {
        $query->where('price', '>=', (int) $value);
    }

    protected function applyPriceMax(Builder $query, string $value): void
    {
        $query->where('price', '<=', (int) $value);
    }

    protected function applyRating(Builder $query, string $value): void
    {
        $query->where('rating', '>=', (float) $value);
    }
}
