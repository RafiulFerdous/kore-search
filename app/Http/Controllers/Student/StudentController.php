<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        $student = Auth::user();
        $purchasedCourseIds = Order::where('user_id', $student->id)->where('status', 'completed')->pluck('course_id');
        $courses = Course::whereIn('id', $purchasedCourseIds)->get();
        $totalSpent = Order::where('user_id', $student->id)->where('status', 'completed')->sum('amount');
        $recentOrders = Order::with('course')->where('user_id', $student->id)->latest()->take(5)->get();

        return view('dashboard.student.index', compact('courses', 'totalSpent', 'recentOrders'));
    }
}
