<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRating;
use Illuminate\Http\Request;

class CourseRatingController extends Controller
{
    public function __invoke(Request $request, Course $course)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $user = $request->user();

        abort_unless($user->isStudent() && $course->isPurchasedBy($user), 403, 'Only enrolled students can rate this course.');

        CourseRating::updateOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['rating' => $request->rating, 'review' => $request->review],
        );

        $course->recalculateRating();

        return back()->with('success', 'Your rating has been submitted.');
    }
}
