<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class InstructorController extends Controller
{
    public function dashboard()
    {
        $instructor = Auth::user();
        $courses    = Course::with('instructor')->where('instructor_id', $instructor->id)->latest()->get();
        $totalStudents = Order::whereIn('course_id', $courses->pluck('id'))->where('status', 'completed')->distinct('user_id')->count('user_id');
        $totalRevenue  = Order::whereIn('course_id', $courses->pluck('id'))->where('status', 'completed')->sum('amount');

        return view('dashboard.instructor.index', compact('courses', 'totalStudents', 'totalRevenue'));
    }

    public function courses()
    {
        $instructor = Auth::user();
        $courses    = Course::with('instructor')->where('instructor_id', $instructor->id)->latest()->paginate(10);

        return view('dashboard.instructor.courses', compact('courses'));
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

        $slug = Str::slug($request->title);
        $original = $slug;
        $counter = 1;
        while (Course::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter++;
        }

        $thumbnailPath = null;

        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        Course::create([
            'instructor_id' => Auth::id(),
            'title'         => $request->title,
            'slug'          => $slug,
            'description'   => $request->description,
            'price'         => $request->price,
            'category'      => $request->category,
            'thumbnail'     => $thumbnailPath,
            'is_published'  => true,
        ]);

        return redirect()->route('instructor.courses')->with('success', 'Course uploaded successfully.');
    }

    public function destroyCourse(Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            return back()->with('error', 'You can only delete your own courses.');
        }

        $course->delete();

        return redirect()->route('instructor.courses')->with('success', 'Course deleted successfully.');
    }
}
