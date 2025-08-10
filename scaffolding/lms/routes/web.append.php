<?php

use Illuminate\Support\Facades\Route;

// Routes appended by Noble Nest LMS scaffolder. Safe to re-run; wrapped by installer markers.

// Home (Noble Nest landing) — separate from existing '/' to avoid conflicts
Route::get('/noble', [\App\Http\Controllers\HomeController::class, 'index'])->name('noble.home');

// AI Assistant endpoint (AJAX)
Route::post('/ai/assistant/message', [\App\Http\Controllers\ChatController::class, 'message'])->name('ai.assistant.message');

// Admin — Course management (basic CRUD)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);
});

// Authentication routes
Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register']);
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

// Parent routes for managing children (role-based access will be enforced via middleware)
Route::middleware(['auth', 'role:Parent'])->group(function () {
    Route::get('/children', [\App\Http\Controllers\Parent\ChildController::class, 'index'])->name('children.index');
    Route::get('/children/create', [\App\Http\Controllers\Parent\ChildController::class, 'create'])->name('children.create');
    Route::post('/children', [\App\Http\Controllers\Parent\ChildController::class, 'store'])->name('children.store');
    Route::get('/children/{child}/edit', [\App\Http\Controllers\Parent\ChildController::class, 'edit'])->name('children.edit');
    Route::put('/children/{child}', [\App\Http\Controllers\Parent\ChildController::class, 'update'])->name('children.update');
    Route::delete('/children/{child}', [\App\Http\Controllers\Parent\ChildController::class, 'destroy'])->name('children.destroy');
});

// Language and onboarding (POST for forms)
Route::post('/set-language', [\App\Http\Controllers\SettingsController::class, 'setLanguage'])->name('set-language');
Route::post('/dismiss-onboarding', [\App\Http\Controllers\SettingsController::class, 'dismissOnboarding'])->name('dismiss-onboarding');

// Language switcher route
Route::get('/lang/{lang}', function ($lang) {
    session(['lang' => $lang]);
    return redirect()->back();
})->name('lang.switch');

// Root route for default home and test coverage
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Protect premium activities and modules with subscription middleware
Route::middleware(['auth', 'subscription.active'])->group(function () {
    // Activity listing and filtering
    Route::get('/activities', [\App\Http\Controllers\ActivityController::class, 'index'])->name('activities.index');

    // Tracing activity routes
    Route::get('/activities/{activity}/tracing', [\App\Http\Controllers\ActivityController::class, 'showTracing'])->name('activities.tracing');
    Route::post('/activities/{activity}/trace', [\App\Http\Controllers\ActivityController::class, 'saveTrace']);

    // Drawing activity routes
    Route::get('/activities/{activity}/drawing', [\App\Http\Controllers\ActivityController::class, 'showDrawing'])->name('activities.drawing');
    Route::post('/activities/{activity}/drawing', [\App\Http\Controllers\ActivityController::class, 'saveDrawing']);

    // Puzzle activity routes
    Route::get('/activities/{activity}/puzzle', [\App\Http\Controllers\ActivityController::class, 'showPuzzle'])->name('activities.puzzle');
    Route::post('/activities/{activity}/puzzle/complete', [\App\Http\Controllers\ActivityController::class, 'savePuzzleComplete'])->name('activities.puzzle.complete');
});

// Payment routes
Route::middleware(['auth'])->group(function () {
    Route::post('/checkout/stripe', [\App\Http\Controllers\PaymentController::class, 'stripeCheckout'])->name('checkout.stripe');
    Route::post('/checkout/paypal', [\App\Http\Controllers\PaymentController::class, 'paypalCheckout'])->name('checkout.paypal');
    Route::get('/payment/success/{provider}', [\App\Http\Controllers\PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/cancel/{provider}', [\App\Http\Controllers\PaymentController::class, 'paymentCancel'])->name('payment.cancel');
});
// Stripe webhook (no auth)
Route::post('/webhook/stripe', [\App\Http\Controllers\PaymentController::class, 'stripeWebhook'])->name('webhook.stripe');

// Public static pages
Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');

// Authenticated pages
Route::middleware(['auth'])->group(function () {
    Route::view('/profile', 'profile')->name('profile');
    Route::view('/checkout', 'checkout')->name('checkout');
});

// === Theme Toggle ===
Route::post('/theme-toggle', function () {
    $current = session('theme', 'professional');
    $next = $current === 'playful' ? 'professional' : 'playful';
    session(['theme' => $next]);
    return response()->noContent();
})->name('theme.toggle');
