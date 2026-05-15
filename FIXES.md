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
