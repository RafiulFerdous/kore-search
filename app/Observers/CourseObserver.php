<?php

namespace App\Observers;

use App\Models\Course;
use Illuminate\Support\Facades\Cache;

class CourseObserver
{
    public function saved(Course $course): void
    {
        Cache::increment('courses.version');
    }

    public function deleted(Course $course): void
    {
        Cache::increment('courses.version');
        Cache::forget('course.' . $course->slug);
    }
}
