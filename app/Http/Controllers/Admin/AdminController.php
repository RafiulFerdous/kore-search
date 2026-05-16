<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Order;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers  = User::count();
        $totalCourses = Course::count();
        $totalOrders  = Order::count();

        return view('dashboard.admin.index', compact('totalUsers', 'totalCourses', 'totalOrders'));
    }
}
