<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AIController;
use App\Http\Controllers\CourseRatingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Instructor\InstructorController;
use App\Http\Controllers\Student\StudentController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/login/throttled', [AuthController::class, 'showThrottled'])->name('login.throttled');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1');

Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
Route::get('/courses/{slug}', [CourseController::class, 'show'])->name('courses.show');
Route::middleware(['auth'])->post('/courses/{course}/rate', CourseRatingController::class)->name('courses.rate');

Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

Route::middleware(['auth'])->group(function () {
    Route::post('/cart/add/{course}', [CartController::class, 'add'])->name('cart.add');
    Route::delete('/cart/remove/{course}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart', [CartController::class, 'clearAll'])->name('cart.clear');

    Route::post('/ai/suggest-course', [AIController::class, 'suggestCourse'])->name('ai.suggest.course');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/confirmation/{order}', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');

    Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard');

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
        Route::patch('/users/{user}/password', [AdminUserController::class, 'updatePassword'])->name('users.password');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        Route::get('/courses', [AdminCourseController::class, 'index'])->name('courses');
        Route::post('/courses', [AdminCourseController::class, 'store'])->name('courses.store');
        Route::patch('/courses/{course}', [AdminCourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [AdminCourseController::class, 'destroy'])->name('courses.destroy');

        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/hero', [AdminSettingController::class, 'hero'])->name('hero');
            Route::patch('/hero', [AdminSettingController::class, 'updateHero'])->name('hero.update');
            Route::get('/featured', [AdminSettingController::class, 'featured'])->name('featured');
            Route::patch('/featured', [AdminSettingController::class, 'updateFeatured'])->name('featured.update');
        });
    });

    Route::middleware(['role:instructor'])->prefix('instructor')->name('instructor.')->group(function () {
        Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');
        Route::get('/courses', [InstructorController::class, 'courses'])->name('courses');
        Route::post('/courses', [InstructorController::class, 'storeCourse'])->name('courses.store');
        Route::delete('/courses/{course}', [InstructorController::class, 'destroyCourse'])->name('courses.destroy');
    });

    Route::middleware(['role:student'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/courses', [StudentController::class, 'courses'])->name('courses');
        Route::get('/orders', [StudentController::class, 'orders'])->name('orders');
    });
});
