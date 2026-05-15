<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\FeaturedSection;
use App\Models\HeroSection;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCourses = Cache::get('home.featured_courses');

        if (!$featuredCourses) {
            $featured = FeaturedSection::where('is_active', true)->first();
            $ids = $featured?->course_ids ?? [];

            if (!empty($ids)) {
                $featuredCourses = Course::with('instructor')
                    ->whereIn('id', $ids)
                    ->where('is_published', true)
                    ->latest()
                    ->get();
            } else {
                $featuredCourses = Course::with('instructor')
                    ->where('is_published', true)
                    ->inRandomOrder()
                    ->take(6)
                    ->get();
            }

            Cache::put('home.featured_courses', $featuredCourses, 3600);
        }

        $categories = Course::where('is_published', true)
            ->select('category')
            ->distinct()
            ->pluck('category');

        $hero = HeroSection::where('is_active', true)->first();

        $totalCourses    = Course::where('is_published', true)->count();
        $totalInstructors = User::role('instructor')->count();
        $totalStudents   = User::role('student')->count();

        return view('home.index', compact('featuredCourses', 'categories', 'hero', 'totalCourses', 'totalInstructors', 'totalStudents'));
    }
}
