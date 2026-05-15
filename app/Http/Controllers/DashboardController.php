<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function redirect()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('instructor')) {
            return redirect()->route('instructor.dashboard');
        }

        return redirect()->route('student.dashboard');
    }

    public function adminDashboard()
    {
        $totalUsers = User::count();
        $users      = User::latest()->get();
        $courses    = Course::with('instructor')->latest()->get();
        $orders     = Order::with(['user', 'course'])->orderBy('created_at', 'desc')->get();

        return view('dashboard.admin.index', compact('totalUsers', 'users', 'courses', 'orders'));
    }

    public function instructorDashboard()
    {
        $instructor = Auth::user();
        $courses    = Course::with('instructor')->where('instructor_id', $instructor->id)->latest()->get();
        $totalStudents = Order::whereIn('course_id', $courses->pluck('id'))->where('status', 'completed')->distinct('user_id')->count('user_id');
        $totalRevenue  = Order::whereIn('course_id', $courses->pluck('id'))->where('status', 'completed')->sum('amount');
        $totalEnrollments = $courses->sum('enrolled_count');

        return view('dashboard.instructor.index', compact('courses', 'totalStudents', 'totalRevenue', 'totalEnrollments'));
    }

    public function studentDashboard()
    {
        $student = Auth::user();
        $purchasedCourseIds = Order::where('user_id', $student->id)->where('status', 'completed')->pluck('course_id');
        $courses = Course::whereIn('id', $purchasedCourseIds)->get();
        $totalSpent = Order::where('user_id', $student->id)->where('status', 'completed')->sum('amount');
        $recentOrders = Order::with('course')->where('user_id', $student->id)->latest()->take(5)->get();

        return view('dashboard.student.index', compact('courses', 'totalSpent', 'recentOrders'));
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:500'],
            'price'       => ['required', 'numeric', 'min:0'],
            'category'    => ['required', 'string'],
            'thumbnail'   => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        Course::create([
            'instructor_id' => Auth::id(),
            'title'         => $request->title,
            'slug'          => Str::slug($request->title),
            'description'   => $request->description,
            'price'         => $request->price,
            'category'      => $request->category,
            'thumbnail'     => $thumbnailPath,
            'is_published'  => true,
        ]);

        return redirect()->route('instructor.dashboard')->with('success', 'Course uploaded successfully.');
    }

    public function destroyCourse(Course $course)
    {
        $course->delete();

        return redirect()->route('instructor.dashboard')->with('success', 'Course deleted successfully.');
    }
}
