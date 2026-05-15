<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'category' => $request->input('category'),
            'level' => $request->input('level'),
            'search' => $request->input('search'),
            'page' => $request->input('page', 1),
        ];

        $version = Cache::get('courses.version', 1);
        $cacheKey = 'courses.list.' . $version . '.' . md5(serialize($filters));

        $data = Cache::remember($cacheKey, 3600, function () use ($request) {
            $query = Course::with('instructor')->where('is_published', true);

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('level')) {
                $query->where('level', $request->level);
            }

            if ($request->filled('search')) {
                $query->where('title', 'like', '%' . $request->search . '%');
            }

            $courses = $query->latest()->paginate(9);

            $categories = Course::where('is_published', true)
                ->select('category')
                ->distinct()
                ->pluck('category');

            return compact('courses', 'categories');
        });

        return view('courses.index', $data);
    }

    public function show($slug)
    {
        $cacheKey = 'course.' . $slug;

        $course = Cache::remember($cacheKey, 3600, function () use ($slug) {
            return Course::with('instructor')->where('slug', $slug)->firstOrFail();
        });

        return view('courses.show', compact('course'));
    }

    public static function invalidateCache(): void
    {
        Cache::increment('courses.version');
    }
}
