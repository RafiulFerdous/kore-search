<?php

namespace App\Observers;

use App\Models\Course;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CourseObserver
{
    public function saved(Course $course): void
    {
        Cache::increment('courses.version');
    }

    public function deleted(Course $course): void
    {
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }

        Cache::increment('courses.version');
        Cache::forget('course.' . $course->slug);
    }
}
