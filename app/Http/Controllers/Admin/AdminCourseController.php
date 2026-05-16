<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminCourseController extends Controller
{
    public function index()
    {
        $courses = Cache::remember('admin.courses.list.' . Cache::get('admin.courses.version', 0) . '.page.' . (request('page', 1)), 300, function () {
            return Course::with('instructor')->latest()->paginate(10);
        });

        $instructors = User::role('instructor')->orderBy('name')->get(['id', 'name']);

        return view('dashboard.admin.courses', compact('courses', 'instructors'));
    }

    public function store(Request $request)
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

    public function update(Request $request, Course $course)
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

    public function destroy(Course $course)
    {
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        $course->delete();

        Cache::increment('admin.courses.version');

        return redirect()->route('admin.courses')->with('success', 'Course deleted successfully.');
    }
}
