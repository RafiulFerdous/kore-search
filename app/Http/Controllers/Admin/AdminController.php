<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\FeaturedSection;
use App\Models\HeroSection;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers  = User::count();
        $totalCourses = Course::count();
        $totalOrders  = Order::count();

        return view('dashboard.admin.index', compact('totalUsers', 'totalCourses', 'totalOrders'));
    }

    public function users()
    {
        $users = Cache::remember('admin.users.list.' . Cache::get('admin.users.version', 0) . '.page.' . (request('page', 1)), 300, function () {
            return User::with('roles')->latest()->paginate(10);
        });

        return view('dashboard.admin.users', compact('users'));
    }

    public function courses()
    {
        $courses = Cache::remember('admin.courses.list.' . Cache::get('admin.courses.version', 0) . '.page.' . (request('page', 1)), 300, function () {
            return Course::with('instructor')->latest()->paginate(10);
        });

        $instructors = User::role('instructor')->orderBy('name')->get(['id', 'name']);

        return view('dashboard.admin.courses', compact('courses', 'instructors'));
    }

    public function orders()
    {
        $orders = Order::with(['user', 'course'])->orderBy('created_at', 'desc')->paginate(10);

        return view('dashboard.admin.orders', compact('orders'));
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'in:admin,instructor,student'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role' => ['required', 'in:admin,instructor,student'],
        ]);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => $request->role]);
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users')->with('success', "{$user->name}'s role updated to {$request->role}.");
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($request->password)]);

        return redirect()->route('admin.users')->with('success', "{$user->name}'s password updated.");
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['required', 'string', 'max:1000'],
            'price'        => ['required', 'numeric', 'min:0'],
            'category'     => ['required', 'string'],
            'instructor_id'=> ['required', 'exists:users,id'],
            'level'        => ['nullable', 'string', 'max:50'],
            'duration'     => ['nullable', 'string', 'max:50'],
            'thumbnail'    => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'topics'       => ['nullable', 'string'],
            'is_published' => ['boolean'],
        ]);

        $topics = $request->filled('topics')
            ? array_filter(array_map('trim', explode("\n", $request->topics)))
            : [];

        $thumbnailPath = $request->hasFile('thumbnail')
            ? $request->file('thumbnail')->store('thumbnails', 'public')
            : null;

        Course::create([
            'instructor_id' => $request->instructor_id,
            'title'         => $request->title,
            'slug'          => Str::slug($request->title),
            'description'   => $request->description,
            'price'         => $request->price,
            'category'      => $request->category,
            'level'         => $request->level,
            'duration'      => $request->duration,
            'thumbnail'     => $thumbnailPath,
            'topics'        => $topics,
            'is_published'  => $request->boolean('is_published'),
        ]);

        Cache::increment('admin.courses.version');

        return redirect()->route('admin.courses')->with('success', 'Course created successfully.');
    }

    public function updateCourse(Request $request, Course $course)
    {
        $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['required', 'string', 'max:1000'],
            'price'        => ['required', 'numeric', 'min:0'],
            'category'     => ['required', 'string'],
            'instructor_id'=> ['required', 'exists:users,id'],
            'level'        => ['nullable', 'string', 'max:50'],
            'duration'     => ['nullable', 'string', 'max:50'],
            'thumbnail'    => ['nullable', 'image', 'mimes:png,jpg,jpeg', 'max:2048'],
            'topics'       => ['nullable', 'string'],
            'is_published' => ['boolean'],
        ]);

        $topics = $request->filled('topics')
            ? array_filter(array_map('trim', explode("\n", $request->topics)))
            : [];

        $data = [
            'instructor_id' => $request->instructor_id,
            'title'         => $request->title,
            'slug'          => Str::slug($request->title),
            'description'   => $request->description,
            'price'         => $request->price,
            'category'      => $request->category,
            'level'         => $request->level,
            'duration'      => $request->duration,
            'topics'        => $topics,
            'is_published'  => $request->boolean('is_published'),
        ];

        if ($request->hasFile('thumbnail')) {
            if ($course->thumbnail) {
                Storage::disk('public')->delete($course->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        $course->update($data);

        Cache::increment('admin.courses.version');
        Cache::forget('course.' . $course->slug);

        return redirect()->route('admin.courses')->with('success', 'Course updated successfully.');
    }

    public function destroyCourse(Course $course)
    {
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        Cache::increment('admin.courses.version');

        return redirect()->route('admin.courses')->with('success', 'Course deleted successfully.');
    }

    public function heroSettings()
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

    public function updateHeroSettings(Request $request)
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

    public function featuredSettings()
    {
        $featured = FeaturedSection::where('is_active', true)->first() ?? new FeaturedSection();
        $courses  = Course::where('is_published', true)->latest()->get();

        return view('dashboard.admin.settings.featured', compact('featured', 'courses'));
    }

    public function updateFeaturedSettings(Request $request)
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
