<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\HeroSection;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredCourses = Course::with('instructor')
            ->where('is_published', true)
            ->latest()
            ->take(6)
            ->get();

        $categories = Course::where('is_published', true)
            ->select('category')
            ->distinct()
            ->pluck('category');

        $hero = HeroSection::where('is_active', true)->first();

        return view('home.index', compact('featuredCourses', 'categories', 'hero'));
    }
}
