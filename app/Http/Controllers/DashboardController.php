<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Order;
use App\Models\User;
use App\Http\Controllers\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $users      = User::latest()->get();
        $courses    = Course::with('instructor')->latest()->get();
        $orders     = Order::with(['user', 'course'])->orderBy('created_at', 'desc')->get();

        return view('dashboard.index', compact('totalUsers', 'users', 'courses', 'orders'));
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

        CourseController::invalidateCache();

        return redirect()->route('dashboard')->with('success', 'Course uploaded successfully.');
    }

    public function destroyCourse(Course $course)
    {
        $course->delete();

        CourseController::invalidateCache();

        return redirect()->route('dashboard')->with('success', 'Course deleted successfully.');
    }
}
