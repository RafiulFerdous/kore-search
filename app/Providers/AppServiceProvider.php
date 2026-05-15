<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\User;
use App\Observers\CourseObserver;
use App\Observers\UserObserver;
use App\Services\AI\Contracts\AIService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(AIService::class, function ($app) {
            $driver = config('ai.default', 'ollama');
            $class  = config("ai.providers.{$driver}.driver");

            if (!$class || !class_exists($class)) {
                throw new \RuntimeException("AI provider '{$driver}' is not configured.");
            }

            return $app->make($class);
        });
    }

    public function boot(): void
    {
        Course::observe(CourseObserver::class);
        User::observe(UserObserver::class);

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        View::composer('*', function ($view) {
            $cart = session()->get('cart', []);
            $view->with('cartCount', count($cart));
            $view->with('cartIds', $cart);
        });
    }
}
