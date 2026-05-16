# KoreSearch LMS — Job Assessment Answers

## 1. How did you find the bugs?

Started by running `php artisan serve` and clicking through every page. The app crashed immediately — missing `artisan`, `bootstrap/app.php`, `public/index.php`, and half the config directory. That was the first red flag: the project was shipped without the Laravel skeleton.

Once I got a white screen to render, I enabled debug mode and used `php artisan route:list` to check routes, `php artisan migrate --pretend` to see if migrations would run, and checked Laravel logs. The debugbar was a huge help for finding N+1 queries and missing eager loads on the course listing and dashboard pages.

For the cart bugs, I traced session data dumps in the views with `@dump(session()->all())`. That's how I spotted the price mismatch problem — the cart stored only IDs, so prices were read live from the database on every page load. If an admin changed a price mid-session, the user would see a different number at checkout than when they added it.

For the 422 response bug, I opened the browser's network tab and saw the add-to-cart AJAX call returning a proper JSON error, but the JS was treating any non-2xx as a generic failure instead of parsing the server's message.

## 2. How did you fix each bug?

**Missing Laravel skeleton files**
The original repo was missing `artisan`, `bootstrap/app.php`, `public/index.php`, `public/.htaccess`, `storage/` directory, and most config files. Created all of them manually. `bootstrap/app.php` was rewritten to use the Laravel 11+ fluent API (`->withRouting()`, `->withMiddleware()`, `->withExceptions()`). All middleware classes (`TrustProxies`, `TrimStrings`, `EncryptCookies`, etc.) were created from scratch.


**RBAC using raw column instead of Spatie** — `User::isAdmin()` and friends were checking `$this->role !== 'admin'` instead of using Spatie's `$this->hasRole('admin')`. Fixed in `app/Models/User.php:47-60`. Same issue in `app/Http/Middleware/RoleMiddleware.php:13` — was doing `$request->user()->role !== $role`, changed to `$request->user()->hasRole($role)`.

**Cart price synchronization** — `app/Http/Controllers/CartController.php:58` now stores `cart_prices.{course_id}` snapshot when adding to cart. `CheckoutController.php:57` uses the snapshot price (`$cartPrices[$course->id] ?? $course->price`) when creating the order instead of the live price. Cart and checkout views show a strikethrough old price + red new price + warning badge when a change is detected.

**No cart state on course cards** — `app/Models/Course.php:76-79` added `isInCart()` method that reads the session directly. Used in course-card.blade.php and courses/show.blade.php to render the button as disabled/green "Added to Cart" vs active "Add to Cart". Previously this was attempted with a View Composer but Blade component inheritance made it unreliable.

**422 AJAX response handling** — `public/js/application.js:83-98` — before the fix, `.catch()` swallowed all non-2xx. Changed to `r.json().then(data => { data._ok = r.ok; return data })` so the JSON body is always parsed regardless of HTTP status. The then-handler checks `data._ok` and uses the server's own error message instead of a generic fallback. Also added a `fail` callback so the button text restores on error (was staying stuck on "Adding…").

**Cache invalidation** — Created `app/Observers/CourseObserver.php` that auto-increments `courses.version` on `saved()` and `deleted()`. Removed the manual `CourseController::invalidateCache()` calls from `DashboardController`. Registered the observer in `AppServiceProvider::boot()`.

**Laravel 10 → 13 upgrade** — `composer.json` bumped `laravel/framework` to `^13.0`, `php` to `^8.2`, `spatie/laravel-permission` to `^7.0`. Removed `app/Http/Kernel.php` (middleware now in `bootstrap/app.php` via `->withMiddleware()`). Removed `App\Http\Kernel`, `RouteServiceProvider`, `AuthServiceProvider`, `Exceptions\Handler`, `Console\Kernel` — all replaced by Laravel 11+ patterns. `public/index.php` simplified to `$app->handleRequest()`. `artisan` updated to use `$app->handleCommand()`.

**Missing helpers** — `app/helpers.php` created with `removeQuery()` function, registered in `composer.json` autoload `files`.

**Toast notification system** — Replaced the three duplicate `@if(session(...))` alert blocks in `layouts/app.blade.php` with a single `<div id="toastContainer">` that reads flash data from a `data-flash` JSON attribute. `application.js` handles the rendering with slide-in animation, auto-dismiss after 4 seconds, close button, and SVG icons per type.

**CourseObserver registration** — `app/Providers/AppServiceProvider.php:34` registers `Course::observe(CourseObserver::class)`, and the observer is at `app/Observers/CourseObserver.php`.

## 3. What challenges did you face?

The biggest headache was the Laravel upgrade from 10 to 13 — skipped versions 11 and 12 entirely. Laravel 11 removed `Http\Kernel`, `Exceptions\Handler`, `Console\Kernel`, and `RouteServiceProvider` as files. Had to re-read the upgrade guides for two major versions and figure out the new `bootstrap/app.php` pattern. The first few attempts at `php artisan serve` just threw class-not-found errors until I got all the middleware aliases and service providers registered correctly through the new fluent API.

Setting up Spatie Permission was tricky because the old code was halfway migrated — some places used the raw `role` column, others tried to use Spatie. Had to find every reference to `$user->role` in controllers, middleware, and views and decide whether it should stay or switch to `hasRole()`.

The AJAX cart button state bug was subtle. The initial approach used a View Composer to pass `$cartIds` to all views, but Blade components (`@component('components.course-card')`) don't inherit parent view data in Laravel. Ended up fixing it by putting the session check directly in the Course model via `isInCart()`.

The price snapshot fix sounded simple but took longer than expected. Storing a snapshot was easy, but detecting when it changed and showing it in the UI required threading that data through three controllers (cart, checkout, order confirmation) and two views.

## 4. What are your suggestions or comments?

A few things I'd improve if given more time:

- **Tests** — There are zero tests. Every fix and new feature was verified manually. I'd add feature tests for the checkout flow, cart operations, and role-based access at minimum.

- **Image handling** — All course thumbnails use placeholder URLs from placehold.co. I added file upload support in `AdminCourseController` but the thumbnails in the seeder are hardcoded URLs. A proper media library (like Spatie Media Library) would be better.

- **CSRF on AJAX** — The current JS reads CSRF token from a `<meta>` tag, which works but the Laravel convention is the `XSRF-TOKEN` cookie. Not a bug, just worth aligning with framework conventions.

- **Query performance** — The admin course list caches with `admin.courses.version` but doesn't bust that version when courses change outside the admin controller (e.g., instructor creates a course). A cache tag approach (`Cache::tags(['courses'])`) would be cleaner, but tags don't work with file/database cache drivers — only Redis/Memcached.

- **Pagination on filters** — The course filter page stores filters in the query string but if someone bookmarks `/courses?category=Backend&page=2`, it works fine. However, removing a filter resets to page 1, which is correct but could be more explicit.

- **Role separation in seeders** — The `DatabaseSeeder` still sets the raw `role` column AND assigns a Spatie role. The `role` column is redundant now that Spatie manages roles. A future cleanup could drop that column and rely entirely on Spatie's `model_has_roles` table.

- **Ollama dependency** — The AI feature defaults to a demo provider (no API needed), which is smart. The Ollama integration works but requires the user to have Ollama running locally. Not everyone will set that up, so keeping the demo fallback was a good call.

- **Eager loading** — Added `Course::with('instructor')` to the main query in `CourseController::index()` at `app/Http/Controllers/CourseController.php:23`. Also added `with('course', 'user')` and `with('course')` in `CheckoutController::confirmation()` at `app/Http/Controllers/CheckoutController.php:87-91`. These were plain N+1 bugs before.
