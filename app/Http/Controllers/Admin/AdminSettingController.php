<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\FeaturedSection;
use App\Models\HeroSection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminSettingController extends Controller
{
    public function hero()
    {
        $hero = HeroSection::where('is_active', true)->first() ?? new HeroSection();

        $totalCourses    = Course::where('is_published', true)->count();
        $totalInstructors = User::role('instructor')->count();
        $totalStudents   = User::role('student')->count();

        if (!old() && (!$hero->stats || empty($hero->stats))) {
            $hero->stats = [
                ['count' => $totalStudents,   'label' => 'Students'],
                ['count' => $totalCourses,    'label' => 'Courses'],
                ['count' => $totalInstructors, 'label' => 'Instructors'],
            ];
        }

        return view('dashboard.admin.settings.hero', compact('hero', 'totalStudents', 'totalCourses', 'totalInstructors'));
    }

    public function updateHero(Request $request)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'subtitle'    => ['required', 'string', 'max:500'],
            'hero_image'  => ['nullable', 'url', 'max:500'],
            'stats.*.count' => ['nullable', 'string', 'max:20'],
            'stats.*.label' => ['nullable', 'string', 'max:50'],
        ]);

        $stats = [];
        if ($request->filled('stats')) {
            foreach ($request->stats as $stat) {
                if (!empty($stat['count']) && !empty($stat['label'])) {
                    $stats[] = $stat;
                }
            }
        }

        HeroSection::updateOrCreate(
            ['is_active' => true],
            [
                'title'      => $request->title,
                'subtitle'   => $request->subtitle,
                'hero_image' => $request->hero_image,
                'stats'      => $stats,
                'is_active'  => true,
            ]
        );

        return redirect()->route('admin.settings.hero')->with('success', 'Hero section updated successfully.');
    }

    public function featured()
    {
        $featured = FeaturedSection::where('is_active', true)->first() ?? new FeaturedSection();
        $courses  = Course::where('is_published', true)->latest()->paginate(12);

        return view('dashboard.admin.settings.featured', compact('featured', 'courses'));
    }

    public function updateFeatured(Request $request)
    {
        $request->validate([
            'course_ids'   => ['nullable', 'array', 'max:6'],
            'course_ids.*' => ['integer', 'exists:courses,id'],
        ]);

        FeaturedSection::updateOrCreate(
            ['is_active' => true],
            [
                'course_ids' => $request->course_ids ?? [],
                'is_active'  => true,
            ]
        );

        $ids = $request->course_ids ?? [];
        if (!empty($ids)) {
            $courses = Course::with('instructor')->whereIn('id', $ids)->where('is_published', true)->latest()->get();
        } else {
            $courses = Course::with('instructor')->where('is_published', true)->inRandomOrder()->take(6)->get();
        }
        Cache::put('home.featured_courses', $courses, 3600);

        return redirect()->route('admin.settings.featured')->with('success', 'Featured courses updated successfully.');
    }
}
