# FIXES.md — KoreSearch Project Changes

## Overview

All changes made to get the KoreSearch LMS project running and implement
Spatie RBAC + rate-limited authentication.

---

## 1. Laravel Skeleton — Missing Files Created

The project was shipped without the standard Laravel skeleton files.
These were created so `artisan` commands and HTTP serving could work.

| File | Purpose |
|------|---------|
| `artisan` | CLI entry point for all `php artisan` commands |
| `bootstrap/app.php` | Application bootstrapping (Kernel, Exception Handler) |
| `public/index.php` | HTTP front controller |
| `public/.htaccess` | Apache rewrite rules |
| `storage/` | Directory tree for logs, cache, sessions, compiled views |
| `config/app.php` | App configuration |
| `config/database.php` | Database connection config |
| `config/auth.php` | Authentication guards / providers |
| `config/session.php` | Session driver & lifetime |
| `config/cache.php` | Cache driver config |
| `config/logging.php` | Log channels |
| `config/view.php` | View paths & compiled path |
| `config/filesystems.php` | Filesystem disks |
| `config/hashing.php` | Hashing driver (bcrypt) |
| `config/mail.php` | Mail driver |
| `config/queue.php` | Queue driver |
| `config/services.php` | Third-party service config |
| `config/broadcasting.php` | Broadcast driver |
| `config/cors.php` | CORS settings |
| `config/sanctum.php` | Sanctum stateful domains |
| `routes/api.php` | Empty API routes stub |

**Middleware classes created** (referenced by `App\Http\Kernel`):

| Class | Extends |
|-------|---------|
| `app/Http/Middleware/TrustProxies.php` | `Illuminate\Http\Middleware\TrustProxies` |
| `app/Http/Middleware/PreventRequestsDuringMaintenance.php` | `Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance` |
| `app/Http/Middleware/TrimStrings.php` | `Illuminate\Foundation\Http\Middleware\TrimStrings` |
| `app/Http/Middleware/EncryptCookies.php` | `Illuminate\Cookie\Middleware\EncryptCookies` |
| `app/Http/Middleware/VerifyCsrfToken.php` | `Illuminate\Foundation\Http\Middleware\VerifyCsrfToken` |
| `app/Http/Middleware/Authenticate.php` | `Illuminate\Auth\Middleware\Authenticate` |
| `app/Http/Middleware/RedirectIfAuthenticated.php` | Custom — redirects authenticated users |
| `app/Http/Middleware/ValidateSignature.php` | `Illuminate\Routing\Middleware\ValidateSignature` |

**Service Providers created**:

| Provider | Purpose |
|----------|---------|
| `app/Providers/AppServiceProvider.php` | App bootstrapping |
| `app/Providers/AuthServiceProvider.php` | Policy registration |
| `app/Providers/EventServiceProvider.php` | Event/listener registration |
| `app/Providers/RouteServiceProvider.php` | Route loading & rate limiter |

**Other files created**:

| File | Purpose |
|------|---------|
| `app/Http/Controllers/Controller.php` | Base controller (AuthorizesRequests, ValidatesRequests) |

---

## 2. Database Fix

The SQL dump file (`database/koresearch_dump.sql`) creates a database called
`koresearch` and populates it. The `.env` file had `DB_DATABASE=kore_search`
which did not match. Fixed to `DB_DATABASE=koresearch`.

Then ran `php artisan migrate:fresh --seed` to rebuild all tables with the
updated schema.

---

## 3. Spatie Laravel Permission — RBAC Implementation

### 3.1 Package Installation

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### 3.2 Roles & Permissions (seeder: `RolePermissionSeeder`)

**Permissions created:**

| Permission | Description |
|------------|-------------|
| `view dashboard` | Access the admin dashboard |
| `manage courses` | Create / manage courses |
| `manage users` | View / manage users |
| `view orders` | View order records |
| `purchase courses` | Purchase courses (student) |

**Roles & assigned permissions:**

| Role | Permissions |
|------|-------------|
| `admin` | `view dashboard`, `manage courses`, `manage users`, `view orders` |
| `instructor` | `view dashboard`, `manage courses` |
| `student` | `purchase courses` |

### 3.3 User Model Update

Added `Spatie\Permission\Traits\HasRoles` trait to `app/Models/User.php`.
The existing `isAdmin()`, `isInstructor()`, `isStudent()` methods now delegate
to `$this->hasRole(...)` instead of checking the raw `role` column.

### 3.4 RoleMiddleware Update

`app/Http/Middleware/RoleMiddleware.php` now uses
`$request->user()->hasRole($role)` instead of `$request->user()->role !== $role`.

### 3.5 Kernel Middleware Aliases

Added Spatie's middleware aliases to `App\Http\Kernel`:

```php
'role'              => \App\Http\Middleware\RoleMiddleware::class,
'permission'        => \Spatie\Permission\Middleware\PermissionMiddleware::class,
'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
```

### 3.6 Registration Role Assignment

In `AuthController::register()`, after creating a user, the `student` role is
assigned via Spatie:

```php
$user->assignRole('student');
```

### 3.7 DatabaseSeeder Update

`DatabaseSeeder` now calls `RolePermissionSeeder` first, then assigns each
seeded user their Spatie role via `$user->assignRole(...)`.

---

## 4. Rate Limiting on Auth Routes

`routes/web.php` updated to wrap all auth routes (login & register GET/POST)
in a `throttle:5,1` middleware group — 5 attempts per minute.

Additionally, `AuthController::login()` has an inline rate limiter using
Laravel's `Illuminate\Cache\RateLimiter` that:
- Tracks attempts by `email|ip` key
- Allows 5 attempts per 60 seconds
- Returns `auth.throttle` error message on excess
- Clears the limiter on successful login

```php
Route::middleware(['throttle:5,1'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});
```

---

## 5. Test Users

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@koresearch.com | password |
| Instructor | instructor@koresearch.com | password |
| Student | student@koresearch.com | password |

---

## 6. Running the Project

```bash
# Start dev server
php artisan serve

# Reset database (drops all tables, re-runs migrations, seeds)
php artisan migrate:fresh --seed

# Seed roles/permissions only
php artisan db:seed --class=RolePermissionSeeder
```

---

## 7. Route Access Matrix

| Route | Guest | Student | Instructor | Admin |
|-------|-------|---------|------------|-------|
| `/` | ✓ | ✓ | ✓ | ✓ |
| `/courses` | ✓ | ✓ | ✓ | ✓ |
| `/cart` | ✓ | ✓ | ✓ | ✓ |
| `/login` | ✓ | — | — | — |
| `/register` | ✓ | — | — | — |
| `/checkout` | →login | ✓ | ✓ | ✓ |
| `/dashboard` | →login | 403 | 403 | ✓ |
| `/dashboard/courses` | →login | 403 | 403 | ✓ |

✓ = accessible, →login = redirect to login, 403 = forbidden

---

## 8. Dynamic Hero Section

### 8.1 Migration

Created `database/migrations/2026_05_15_041313_create_hero_sections_table.php`
with columns: `title`, `subtitle`, `hero_image`, `stats` (JSON), `is_active`.

### 8.2 Model

Created `app/Models/HeroSection.php` — `$casts` for `stats` as `array` and
`is_active` as `boolean`.

### 8.3 Seeder

Created `database/seeders/HeroSectionSeeder.php` with initial hero data:

| Field | Value |
|-------|-------|
| title | `Unlock Your Potential with <span class="highlight">KoreSearch</span>` |
| subtitle | `Explore expert-led courses...` |
| hero_image | `https://placehold.co/560x400/...` |
| stats | `[{count: "540+", label: "Students"}, ...]` |

### 8.4 Controller

`app/Http/Controllers/HomeController.php` — fetches the active hero section:
```php
$hero = HeroSection::where('is_active', true)->first();
```

### 8.5 View

`resources/views/home/index.blade.php` — hero section now renders from `$hero`
data with null-safe fallbacks to hardcoded defaults. Stats loop over the JSON
`stats` array.

```blade
<h1 class="hero-title">{!! $hero?->title ?? '...' !!}</h1>
<p class="hero-subtitle">{{ $hero?->subtitle ?? '...' }}</p>
@foreach($hero->stats as $stat)
    <strong>{{ $stat['count'] }}</strong>
    <span>{{ $stat['label'] }}</span>
@endforeach
```

---

## 9. Header & Footer Separation

Extracted the navigation and footer into their own partial files:

| Partial | Source location | Extracted from |
|---------|----------------|----------------|
| `resources/views/partials/header.blade.php` | Entire `<nav class="navbar">` block | `layouts/app.blade.php` lines 15–65 |
| `resources/views/partials/footer.blade.php` | Entire `<footer>` block + `<script>` tag | `layouts/app.blade.php` lines 94–109 |

`layouts/app.blade.php` now uses `@include('partials.header')` and
`@include('partials.footer')` to pull them in. Flash messages (success/error/info)
remain in the layout between header and content, since they're page-level
concerns.

The auth system in the layout was reviewed:
- `@auth` / `@endauth` — shows Dashboard link when logged in
- `@guest` / `@else` / `@endguest` — toggles between Login/Register buttons
  and the user avatar dropdown with logout
- `Auth::user()->name` / `Auth::user()->email` — used in the dropdown header
- CSRF token in logout form
All auth logic is properly scoped and working.

---

## 10. Course Model Observer — Automatic Cache Invalidation

### 10.1 Observer

Created `app/Observers/CourseObserver.php` which automatically invalidates the
course list cache whenever a Course model is saved (created/updated) or deleted:

| Event | Action |
|-------|--------|
| `saved()` | `Cache::increment('courses.version')` — busts all list caches |
| `deleted()` | `Cache::increment('courses.version')` + `Cache::forget('course.' . $course->slug)` — busts list + individual cache |

Using `saved` instead of separate `created`/`updated` ensures both inserts and
updates bust the cache. This covers all scenarios: dashboard creates, admin
edits, instructor updates, etc.

### 10.2 Registration

Registered in `app/Providers/AppServiceProvider.php`:

```php
use App\Models\Course;
use App\Observers\CourseObserver;

public function boot(): void
{
    Course::observe(CourseObserver::class);
}
```

### 10.3 Manual Calls Removed

Previously, `DashboardController::storeCourse()` and
`DashboardController::destroyCourse()` manually called
`CourseController::invalidateCache()`. These calls have been removed since the
observer handles it automatically.

The `CourseController::invalidateCache()` static method is kept as a public API
for any other code that needs to manually bust the cache.

### 10.4 Redis Database Note

## 11. Cart Price Synchronization

### Problem

The cart stored only course IDs in the session. Every price was fetched live from
the `courses` table at cart/checkout time. If an admin or instructor changed a
course's price after a student added it to their cart:

| Scenario | Effect |
|----------|--------|
| Price increases | User sees & pays higher price at checkout — no warning |
| Price decreases | User pays less — platform loses revenue |

No price snapshot was taken at add-to-cart time, and the order `amount` always
reflected whatever `course.price` happened to be at the moment of checkout.

### Fix — Price Snapshot & Change Detection

**1. Snapshot on add** (`CartController@add`):

When a course is added to cart, its current price is stored alongside the cart:

```php
session()->put('cart_prices.' . $course->id, $course->price);
```

**2. Cleanup on remove** (`CartController@remove`):

```php
session()->forget('cart_prices.' . $course->id);
```

**3. Change detection** (`CartController@index`, `CheckoutController@index`):

On every cart/checkout page load, the snapshot price is compared against the
live `course.price`. If they differ, the course ID is added to a `$priceChanges`
array passed to the view:

```php
$priceChanges[$course->id] = ['old' => $snapshot, 'new' => $course->price];
```

**4. Visual warning** (cart & checkout views):

When a price change is detected:
- The original price is shown with a strikethrough
- The new price appears in red
- A red "Price Changed" badge is displayed on the cart page

**5. Snapshot honored at checkout** (`CheckoutController@process`):

When creating the order, the snapshot price (`$cartPrices[$course->id]`) is used
instead of `$course->price`. This ensures the user pays the price they agreed to
when adding to cart, regardless of subsequent changes.

### 11.1 AJAX Cart Operations

Both add-to-cart and remove-from-cart now use AJAX with no page reload:

- **Add** (`.btn-add-cart`): was already AJAX via the `expectsJson()` branch in
  `CartController@add`. The JS handler disables the button, shows "Adding…",
  sends a `POST` via `fetch()`, restores the button, and shows a toast.
- **Remove** (`.btn-remove-cart`): was a full `<form>` submit with `@method('DELETE')`.
  Converted to a `<button data-course-id="...">` with a JS click handler that
  sends a `DELETE` via `fetch()`. On success, the cart item fades out (300ms),
  is removed from the DOM, the heading count and summary total are recalculated
  client-side, and the badge updates. If the last item is removed, the cart
  content is replaced with the empty-cart UI inline — no redirect.
- **Shared helper** `cartRequest(url, method, btn, done)` in `application.js`
  handles CSRF token, headers, error toast, badge update, and button state for
  both operations.

### Files changed

| File | Change |
|------|--------|
| `app/Http/Controllers/CartController.php` | Snapshot on add, cleanup on remove, change detection in index |
| `app/Http/Controllers/CheckoutController.php` | Change detection in index, snapshot price in order creation |
| `resources/views/cart/index.blade.php` | Show strikethrough old price + red new price + badge |
| `resources/views/checkout/index.blade.php` | Show strikethrough old price + red new price |
| `public/css/app.css` | `.old-price`, `.new-price`, `.price-warning-badge`, checkout variants |
| `public/js/application.js` | `cartRequest()` shared helper, AJAX remove handler with fade-out, empty-cart fallback |
| `resources/views/cart/index.blade.php` | Form replaced with `<button data-course-id>` for AJAX removal |
| `app/Http/Controllers/CheckoutController.php` | Also clears `cart_prices` on checkout |

---

The Redis cache store uses database index `1` (configured in
`config/database.php` under `redis.cache.database`), not `0`. When inspecting
cache keys with `redis-cli`, use `redis-cli -n 1 KEYS '*'` to see cached data.

---

## 12. Toast Notification System

Replaced the old inline `.alert` flash messages with a centralized toast
notification system.

### 12.1 Layout (`resources/views/layouts/app.blade.php`)

- Removed three `@if(session(...))` blocks with duplicate `id="flashAlert"` divs
- Added a `<div id="toastContainer" class="toast-container">` with a `data-flash`
  JSON attribute that encodes `success`, `error`, `info`, and `warning` session
  flashes

### 12.2 JavaScript (`public/js/application.js`)

- Added `showToast(message, type)` function that:
  - Creates animated toast DOM elements with SVG icons per type
  - Appends to the `#toastContainer`
  - Auto-dismisses after 4 seconds
  - Has a close button
- Reads flash data from `data-flash` attribute on page load and shows each
- The existing AJAX cart handler now calls `showToast()` instead of the old
  `showFlash()`

### 12.3 CSS (`public/css/app.css`)

- Replaced old `.alert` / `.alert-success` / `.alert-error` / `.alert-info` /
  `.alert-close` / `.alert.fade-out` classes (lines 329–378) with new toast
  classes:
  - `.toast-container` — fixed top-right, z-index 9999, non-interactive container
  - `.toast` — base card with shadow, rounded corners, slide-in animation
  - `.toast-visible` / `.toast-leaving` — entrance/exit transform states
  - `.toast-success` / `.toast-error` / `.toast-warning` / `.toast-info` —
    colored left border variants
  - `.toast-icon` / `.toast-message` / `.toast-close` — inner layout

---

## 13. Course Card Redesign & Add-to-Cart Button

### 13.1 Add-to-Cart Button

Added an "Add to Cart" button to every course card. It uses the existing
AJAX handler (`.btn-add-cart` → `cartRequest()` in `application.js`):

```blade
<button class="btn-add-cart" data-course-id="{{ $course->id }}">
    <svg>...</svg> Add to Cart
</button>
```

Hitting the button triggers `POST /cart/add/{id}` via `fetch()` with no page
reload. The cart badge updates, a toast notification appears, and the button
is disabled during the request with "Adding…" text.

### 13.2 Visual Redesign

| Element | Before | After |
|---------|--------|-------|
| Thumbnail | 180px, no overlay, simple zoom | 190px, gradient overlay on hover (transparent→black), 1.08× zoom |
| Level badge | Plain badge on thumbnail | Same position, upgraded with subtle box-shadow |
| Price | In footer next to "View Course" | Moved to a frosted-glass ribbon on the thumbnail (`course-price-ribbon`) |
| Category | Standalone line above title | Moved to a top bar with duration (`course-card-top`) |
| Title | Single line | 2-line clamp with ellipsis |
| Stars & enrolled count | Inline with no separator | Separated by a `•` dot |
| Footer | Price + "View Course" button | "Add to Cart" button (primary) + icon-only "View" link |
| Hover | `translateY(-3px)` + shadow | `translateY(-6px)` + deeper shadow |
| Card shadow | `shadow-sm` | Custom subtle shadow, deeper on hover |

### 13.3 New CSS Classes

| Class | Purpose |
|-------|---------|
| `.course-card-overlay` | Gradient overlay on thumbnail, fades in on hover |
| `.course-price-ribbon` | Frosted-glass price badge at bottom-right of thumbnail |
| `.course-card-top` | Flex row for category + duration |
| `.course-duration` | Duration text |
| `.meta-dot` | Bullet separator between stars and student count |
| `.btn-view-link` | Icon-only external link button in footer |
| `.course-card-footer .btn-add-cart` | Scoped add-to-cart button (does not affect course show page) |

### Files changed

| File | Change |
|------|--------|
| `resources/views/components/course-card.blade.php` | Added add-to-cart button, redesigned layout with overlay, ribbon, top bar, view link |
| `public/css/app.css` | Complete rewrite of `.course-card*` block, added new classes, removed redundant old `.btn-add-cart` rule |
