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
        $ordersCount = Order::where('user_id', $student->id)->count();

        return view('dashboard.student.index', compact('courses', 'totalSpent', 'ordersCount'));
    }

    public function courses()
    {
        $student = Auth::user();
        $purchasedCourseIds = Order::where('user_id', $student->id)->where('status', 'completed')->pluck('course_id');
        $courses = Course::whereIn('id', $purchasedCourseIds)->get();

        return view('dashboard.student.courses', compact('courses'));
    }

    public function orders()
    {
        $student = Auth::user();
        $orders = Order::with('course')->where('user_id', $student->id)->latest()->paginate(10);

        return view('dashboard.student.orders', compact('orders'));
    }
}
