<?php

namespace App\Providers;

use App\Models\Course;
use App\Observers\CourseObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Course::observe(CourseObserver::class);

        View::composer('*', function ($view) {
            $view->with('cartCount', count(session()->get('cart', [])));
        });
    }
}
