<?php

use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminImageController;
use App\Http\Controllers\AdminPageContentController;
use App\Http\Controllers\AdminProjectController;
use App\Http\Controllers\AdminPropertyController;
use App\Http\Controllers\AdminPropertyAvailabilityController;
use App\Http\Controllers\AdminPropertyStatusController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\AdminStatController;
use App\Http\Controllers\AdminTimelineEntryController;
use App\Http\Controllers\AdminUpcomingProjectController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminWhyInvestController;
use App\Http\Controllers\AdminWhyInvestIconController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\NakheelController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication (PROVIDED — you don't need to change this)
|--------------------------------------------------------------------------
| A simple login system is wired up for you:
|     GET  /login      shows the login form
|     POST /login      signs the user in
|     GET  /register   shows the sign-up form
|     POST /register   creates an account
|     POST /logout     signs them out
|
| The whole app is protected: every controller requires a logged-in user
| (see app/Http/Controllers/Controller.php). Sign in with the seeded account
| created by the database seeder:   admin@example.com  /  password
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Forgot / reset password (regular users only)
|--------------------------------------------------------------------------
| Self-service password reset is deliberately restricted to non-admin
| accounts — see AuthController::sendResetLink(). Admin/Sub Admin passwords
| are reset by the Main Admin from the admin panel instead.
*/
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request')->middleware('throttle:public');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:password-reset');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset')->middleware('throttle:public');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update')->middleware('throttle:password-reset');

/*
|--------------------------------------------------------------------------
| Language switcher (PROVIDED — English/Arabic toggle in the header)
|--------------------------------------------------------------------------
*/
Route::get('/language/{locale}', [LanguageController::class, 'switch'])->name('language.switch')->middleware('throttle:public');

/*
|--------------------------------------------------------------------------
| Nakheel Properties (PROVIDED — the real-estate site)
|--------------------------------------------------------------------------
| Unrelated to the Categories/Items teaching app below. Every page requires
| sign-in (NakheelController inherits the base controller's `auth`), so
| guests are sent to /login first. See app/Http/Controllers/NakheelController.php.
*/
Route::middleware('throttle:public')->group(function () {
    Route::get('/', [NakheelController::class, 'properties'])->middleware('auth')->name('home');
    Route::get('/properties', [NakheelController::class, 'properties'])->name('properties.index');
    Route::get('/projects', [NakheelController::class, 'projectShow'])->name('projects.index');
    Route::get('/projects/{slug}', [NakheelController::class, 'projectShow'])->name('projects.show');
    Route::get('/about', [NakheelController::class, 'about'])->name('about');
    Route::get('/future-projects', [NakheelController::class, 'futureProjects'])->name('future-projects');
});

/*
|--------------------------------------------------------------------------
| Nakheel Admin (PROVIDED — content management for the site above)
|--------------------------------------------------------------------------
| Lets an admin user manage the projects and properties shown on the public
| site. Gated by `auth` (must be signed in) AND `admin` (must have
| is_admin = true on the users table — see App\Http\Middleware\EnsureUserIsAdmin).
| user@example.com gets a 403 if they try to reach any /admin route.
|
| Two admin roles: Main Admin (role='main', full access to everything —
| admin@example.com by default) and Sub Admin (role='sub', restricted to
| whatever sections the Main Admin granted via /admin/users — see
| App\Support\AdminPermissions and App\Http\Middleware\EnsureUserHasPermission,
| applied per resource group below as `permission:<section>`).
|
| Properties have no admin "index" — the public /properties page IS the
| listing screen, with Edit/Remove controls on each card for admins (see
| the property-card component). Projects still get a separate admin index.
*/
Route::redirect('/admin', '/properties');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'throttle:admin'])->group(function () {
    Route::resource('projects', AdminProjectController::class)->except('show')->middleware('permission:projects');
    Route::resource('properties', AdminPropertyController::class)->except(['show', 'index'])->middleware('permission:properties');
    Route::patch('settings/sold-visibility', [AdminSettingController::class, 'updateSoldVisibility'])->name('settings.sold-visibility')->middleware('permission:properties');
    Route::post('properties/{property}/availability', [AdminPropertyAvailabilityController::class, 'store'])->name('properties.availability.store')->middleware('permission:property_availability');
    Route::put('properties/{property}/availability/{range}', [AdminPropertyAvailabilityController::class, 'update'])->name('properties.availability.update')->middleware('permission:property_availability');
    Route::delete('properties/{property}/availability/{range}', [AdminPropertyAvailabilityController::class, 'destroy'])->name('properties.availability.destroy')->middleware('permission:property_availability');
    Route::resource('statuses', AdminPropertyStatusController::class)->except('show')->middleware('permission:statuses');
    Route::resource('stats', AdminStatController::class)->except(['show', 'index'])->middleware('permission:about_us');
    Route::resource('timeline', AdminTimelineEntryController::class)->except(['show', 'index'])->middleware('permission:about_us');
    Route::resource('categories', AdminCategoryController::class)->except(['show', 'index'])->middleware('permission:about_us');
    Route::resource('upcoming', AdminUpcomingProjectController::class)->except(['show', 'index'])->middleware('permission:future_projects');
    Route::resource('why-invest', AdminWhyInvestController::class)->except(['show', 'index'])->middleware('permission:why_invest');
    Route::get('why-invest-icons', [AdminWhyInvestIconController::class, 'index'])->name('why-invest-icons.index')->middleware('permission:why_invest_icons');
    Route::post('why-invest-icons', [AdminWhyInvestIconController::class, 'store'])->name('why-invest-icons.store')->middleware('permission:why_invest_icons');
    Route::delete('why-invest-icons/{key}', [AdminWhyInvestIconController::class, 'destroy'])->name('why-invest-icons.destroy')->middleware('permission:why_invest_icons');
    Route::get('page-content/{key}/edit', [AdminPageContentController::class, 'edit'])->name('page-content.edit')->middleware('permission');
    Route::put('page-content/{key}', [AdminPageContentController::class, 'update'])->name('page-content.update')->middleware('permission');

    Route::post('images/{resource}/{id}', [AdminImageController::class, 'store'])->name('images.store')->middleware('permission');
    Route::delete('images/{resource}/{id}', [AdminImageController::class, 'destroy'])->name('images.destroy')->middleware('permission');

    // Admin Management — creating/editing Sub Admins and their permissions.
    // Restricted to the Main Admin only; a Sub Admin can never reach this
    // no matter what permissions they hold (see EnsureUserIsMainAdmin).
    Route::prefix('users')->name('users.')->middleware('main-admin')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::get('/create', [AdminUserController::class, 'create'])->name('create');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
        Route::get('/{user}/password', [AdminUserController::class, 'showPasswordForm'])->name('password.edit');
        Route::put('/{user}/password', [AdminUserController::class, 'resetPassword'])->name('password.update');
        Route::patch('/{user}/toggle', [AdminUserController::class, 'toggle'])->name('toggle');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
    });
});
