<?php

namespace App\Http\Controllers;

use App\Filters\CourseFilter;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CourseController extends Controller
{
    public function index(Request $request, CourseFilter $filter)
    {
        $filters = array_merge(
            $request->only(['category', 'level', 'search', 'price_min', 'price_max', 'rating']),
            ['sort' => $request->input('sort', 'newest'), 'page' => $request->input('page', 1)]
        );

        $version = Cache::get('courses.version', 1);
        $cacheKey = 'courses.list.' . $version . '.' . md5(serialize($filters));

        $data = Cache::remember($cacheKey, 3600, function () use ($request, $filter) {
            $query = $filter->apply(Course::with('instructor')->where('is_published', true), $request);
            $query = $filter->applySorting($query, $request);

            $courses = $query->paginate(9);

            $categories = Course::where('is_published', true)
                ->select('category')
                ->distinct()
                ->pluck('category');

            $activeFilters = $filter->activeFilters($request);

            return compact('courses', 'categories', 'activeFilters');
        });

        return view('courses.index', $data);
    }

    public function show($slug)
    {
        $cacheKey = 'course.' . $slug;

        $course = Cache::remember($cacheKey, 3600, function () use ($slug) {
            return Course::with('instructor')->withCount('ratings')->where('slug', $slug)->firstOrFail();
        });

        $userRating = null;
        $canRate = false;
        $reviews = collect();

        if (auth()->check()) {
            $user = auth()->user();
            $canRate = $user->isStudent() && $course->isPurchasedBy($user);
            if ($canRate) {
                $userRating = $course->userRating($user);
            }
        }

        $reviews = $course->ratings()->with('user')->whereNotNull('review')->latest()->get();

        return view('courses.show', compact('course', 'canRate', 'userRating', 'reviews'));
    }

    public static function invalidateCache(): void
    {
        Cache::increment('courses.version');
    }
}
