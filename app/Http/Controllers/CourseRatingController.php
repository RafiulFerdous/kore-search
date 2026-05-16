<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        try {
            DB::beginTransaction();

            CourseRating::updateOrCreate(
                ['user_id' => $user->id, 'course_id' => $course->id],
                ['rating' => $request->rating, 'review' => $request->review],
            );

            $course->recalculateRating();

            DB::commit();

            return back()->with('success', 'Your rating has been submitted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit rating. Please try again.');
        }
    }
}
