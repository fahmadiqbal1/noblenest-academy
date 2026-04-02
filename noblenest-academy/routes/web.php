<?php

use Illuminate\Support\Facades\Route;

// === Noble Nest LMS routes START ===
// Routes appended by Noble Nest LMS scaffolder. Safe to re-run; wrapped by installer markers.

// Home (Noble Nest landing) — separate from existing '/' to avoid conflicts
Route::get('/noble', [\App\Http\Controllers\HomeController::class, 'index'])->name('noble.home');

// AI Assistant endpoint (AJAX)
Route::post('/ai/assistant/message', [\App\Http\Controllers\ChatController::class, 'message'])
    ->middleware('throttle:30,1')
    ->name('ai.assistant.message');

// Admin — Course management (basic CRUD)
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);
});

// Admin Curriculum Explorer
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('curriculum', [\App\Http\Controllers\Admin\CurriculumController::class, 'index'])->name('curriculum');
});

// Add admin analytics routes
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::post('analytics/report', [\App\Http\Controllers\Admin\AnalyticsController::class, 'reportEmail'])->name('analytics.reportEmail');
    Route::get('analytics/most-liked', [\App\Http\Controllers\Admin\AnalyticsController::class, 'mostLiked'])->name('analytics.mostLiked');
    Route::get('analytics/monthly-completions', [\App\Http\Controllers\Admin\AnalyticsController::class, 'monthlyCompletions'])->name('analytics.monthlyCompletions');
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

// Language switcher route (with security allowlist)
Route::get('/lang/{lang}', function ($lang) {
    $allowed = ['en', 'fr', 'ru', 'zh', 'es', 'ko', 'ur', 'ar'];
    if (!in_array($lang, $allowed, true)) {
        abort(400, 'Invalid language code');
    }
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

// Quizzes (basic flow)
Route::get('/quizzes', [\App\Http\Controllers\QuizController::class, 'index'])->name('quizzes.index');
Route::get('/quizzes/{quiz}', [\App\Http\Controllers\QuizController::class, 'show'])->name('quizzes.show');
Route::post('/quizzes/{quiz}/submit', [\App\Http\Controllers\QuizController::class, 'submit'])->name('quizzes.submit');

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
// === Noble Nest LMS routes END ===

// Admin activity management
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('activities', \App\Http\Controllers\Admin\ActivityController::class)->except(['show', 'index']);
});

// Onboarding — 3-step MiroFish fast flow
Route::middleware(['auth'])->group(function () {
    // Legacy single-page onboarding (backward compat)
    Route::get('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'store'])->name('onboarding.store');
    // New 3-step flow
    Route::get('/onboarding/step/1', [\App\Http\Controllers\OnboardingController::class, 'show'])->name('onboarding.step1');
    Route::post('/onboarding/step/1', [\App\Http\Controllers\OnboardingController::class, 'storeStep1'])->name('onboarding.step1.store');
    Route::get('/onboarding/step/2', [\App\Http\Controllers\OnboardingController::class, 'showStep2'])->name('onboarding.step2');
    Route::post('/onboarding/step/2', [\App\Http\Controllers\OnboardingController::class, 'storeStep2'])->name('onboarding.step2.store');
    Route::get('/onboarding/step/3', [\App\Http\Controllers\OnboardingController::class, 'showStep3'])->name('onboarding.step3');
    Route::post('/onboarding/step/3', [\App\Http\Controllers\OnboardingController::class, 'storeStep3'])->name('onboarding.step3.store');
});

// AI Orchestrator
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('orchestrator', [\App\Http\Controllers\Admin\OrchestratorController::class, 'index'])->name('orchestrator.index');
    Route::post('orchestrator/dispatch', [\App\Http\Controllers\Admin\OrchestratorController::class, 'dispatchJob'])->name('orchestrator.dispatch');
    Route::post('orchestrator/providers', [\App\Http\Controllers\Admin\OrchestratorController::class, 'storeProvider'])->name('orchestrator.storeProvider');
    Route::post('orchestrator/providers/{provider}/verify', [\App\Http\Controllers\Admin\OrchestratorController::class, 'verifyProvider'])->name('orchestrator.verifyProvider');
    Route::delete('orchestrator/providers/{provider}', [\App\Http\Controllers\Admin\OrchestratorController::class, 'destroyProvider'])->name('orchestrator.destroyProvider');
    Route::post('orchestrator/providers/{provider}/toggle', [\App\Http\Controllers\Admin\OrchestratorController::class, 'toggleProvider'])->name('orchestrator.toggleProvider');
    Route::post('orchestrator/jobs/{job}/approve', [\App\Http\Controllers\Admin\OrchestratorController::class, 'approve'])->name('orchestrator.approve');
    Route::post('orchestrator/jobs/{job}/reject', [\App\Http\Controllers\Admin\OrchestratorController::class, 'reject'])->name('orchestrator.reject');
    Route::post('orchestrator/jobs/{job}/retry', [\App\Http\Controllers\Admin\OrchestratorController::class, 'retryJob'])->name('orchestrator.retry');
    Route::delete('orchestrator/jobs/{job}', [\App\Http\Controllers\Admin\OrchestratorController::class, 'destroyJob'])->name('orchestrator.destroyJob');
    Route::get('orchestrator/scan', [\App\Http\Controllers\Admin\OrchestratorController::class, 'scanCurriculum'])->name('orchestrator.scan');
});

// ===========================================================================
// TEACHER & STUDENT MARKETPLACE EXTENSION
// ===========================================================================

// --- Public: marketplace (no auth needed) ---
Route::get('/marketplace', [\App\Http\Controllers\Student\MarketplaceController::class, 'index'])->name('marketplace.index');
Route::get('/marketplace/{course:slug}', [\App\Http\Controllers\Student\MarketplaceController::class, 'show'])->name('marketplace.show');

// --- Invite link (auth redirects to register if not logged in) ---
Route::get('/invite/{token}', [\App\Http\Controllers\Student\EnrollmentController::class, 'joinViaInvite'])->name('invite.join');

// --- Student routes ---
Route::middleware(['auth', 'role:Student'])->prefix('student')->name('student.')->group(function () {
    Route::get('my-courses', [\App\Http\Controllers\Student\EnrollmentController::class, 'myCourses'])->name('my-courses');
    Route::get('courses/{course:slug}', [\App\Http\Controllers\Student\MarketplaceController::class, 'show'])->name('course.show');
    Route::get('courses/{course:slug}/checkout', [\App\Http\Controllers\Student\EnrollmentController::class, 'checkout'])->name('enroll.checkout');
    Route::post('courses/{course:slug}/enroll', [\App\Http\Controllers\Student\EnrollmentController::class, 'enroll'])->name('enroll');
});

// --- Teacher routes ---
Route::middleware(['auth', 'role:Teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Teacher\DashboardController::class, 'index'])->name('dashboard');

    // Course CRUD
    Route::get('courses', [\App\Http\Controllers\Teacher\CourseController::class, 'index'])->name('courses.index');
    Route::get('courses/create', [\App\Http\Controllers\Teacher\CourseController::class, 'create'])->name('courses.create');
    Route::post('courses', [\App\Http\Controllers\Teacher\CourseController::class, 'store'])->name('courses.store');
    Route::get('courses/{course}', [\App\Http\Controllers\Teacher\CourseController::class, 'show'])->name('courses.show');
    Route::get('courses/{course}/edit', [\App\Http\Controllers\Teacher\CourseController::class, 'edit'])->name('courses.edit');
    Route::put('courses/{course}', [\App\Http\Controllers\Teacher\CourseController::class, 'update'])->name('courses.update');
    Route::delete('courses/{course}', [\App\Http\Controllers\Teacher\CourseController::class, 'destroy'])->name('courses.destroy');
    Route::post('courses/{course}/publish', [\App\Http\Controllers\Teacher\CourseController::class, 'togglePublish'])->name('courses.publish');

    // Sessions
    Route::post('courses/{course}/sessions', [\App\Http\Controllers\Teacher\SessionController::class, 'store'])->name('sessions.store');
    Route::post('sessions/{session}/start', [\App\Http\Controllers\Teacher\SessionController::class, 'start'])->name('sessions.start');
    Route::post('sessions/{session}/end', [\App\Http\Controllers\Teacher\SessionController::class, 'end'])->name('sessions.end');
    Route::post('sessions/{session}/cancel', [\App\Http\Controllers\Teacher\SessionController::class, 'cancel'])->name('sessions.cancel');

    // Invite links
    Route::post('courses/{course}/invite-links', [\App\Http\Controllers\Teacher\InviteLinkController::class, 'store'])->name('invite-links.store');
    Route::delete('invite-links/{link}', [\App\Http\Controllers\Teacher\InviteLinkController::class, 'destroy'])->name('invite-links.destroy');
});

// --- Classroom (Teacher + enrolled Student) ---
Route::middleware(['auth'])->prefix('classroom')->name('classroom.')->group(function () {
    Route::get('{roomId}', [\App\Http\Controllers\ClassroomController::class, 'room'])->name('room');
    Route::get('{roomId}/participants', [\App\Http\Controllers\ClassroomController::class, 'participants'])->name('participants');
});

// ===========================================================================
// PHASE 1+ NEW ROUTES
// ===========================================================================

// --- Public pages ---
Route::view('/for-schools', 'pages.for-schools')->name('for-schools');
Route::post('/school-inquiry', [\App\Http\Controllers\SchoolInquiryController::class, 'store'])->name('school-inquiry.store');
Route::get('/ref/{code}', [\App\Http\Controllers\ReferralController::class, 'track'])->name('referral.track');

// --- Parent dashboard + child experience ---
Route::middleware(['auth', 'role:Parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Parent\DashboardController::class, 'index'])->name('dashboard');
    Route::get('child/{child}', [\App\Http\Controllers\Parent\DashboardController::class, 'child'])->name('child');
    Route::get('milestones', [\App\Http\Controllers\Parent\MilestoneController::class, 'index'])->name('milestones');
    Route::post('child/{child}/milestone/{milestone}/toggle', [\App\Http\Controllers\Parent\MilestoneController::class, 'toggle'])->name('milestone.toggle');
    Route::get('share-card/{child}', [\App\Http\Controllers\ShareCardController::class, 'show'])->name('share-card');
});

// --- Child activity feed (parent views on behalf of child) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/child/{child}/activities', [\App\Http\Controllers\ChildActivityController::class, 'index'])->name('child.activities');
    Route::post('/child/{child}/activities/{activity}/complete', [\App\Http\Controllers\ChildActivityController::class, 'complete'])->name('child.activity.complete');
    Route::post('/activities/{activity}/like', [\App\Http\Controllers\ActivityLikeController::class, 'toggle'])->name('activity.like');
});

// --- Notifications ---
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::post('{id}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('read');
    Route::post('read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('read-all');
});

// --- Admin: teacher vetting & payouts ---
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('teachers', [\App\Http\Controllers\Admin\TeacherVettingController::class, 'index'])->name('teacher-vetting');
    Route::get('teachers/{teacherProfile}', [\App\Http\Controllers\Admin\TeacherVettingController::class, 'show'])->name('teacher-vetting.show');
    Route::post('teachers/{teacherProfile}/approve', [\App\Http\Controllers\Admin\TeacherVettingController::class, 'approve'])->name('teacher-vetting.approve');
    Route::post('teachers/{teacherProfile}/reject', [\App\Http\Controllers\Admin\TeacherVettingController::class, 'reject'])->name('teacher-vetting.reject');
    Route::get('payouts', [\App\Http\Controllers\Admin\PayoutController::class, 'index'])->name('payouts');
    Route::post('payouts/{payoutRequest}/approve', [\App\Http\Controllers\Admin\PayoutController::class, 'approve'])->name('payouts.approve');
    Route::post('payouts/{payoutRequest}/reject', [\App\Http\Controllers\Admin\PayoutController::class, 'reject'])->name('payouts.reject');
    Route::get('schools', [\App\Http\Controllers\Admin\SchoolInquiryController::class, 'index'])->name('school-inquiries');
    Route::get('schools/{schoolInquiry}', [\App\Http\Controllers\Admin\SchoolInquiryController::class, 'show'])->name('school-inquiries.show');
    Route::post('schools/{schoolInquiry}/assign', [\App\Http\Controllers\Admin\SchoolInquiryController::class, 'assign'])->name('school-inquiries.assign');
    Route::post('schools/{schoolInquiry}/close', [\App\Http\Controllers\Admin\SchoolInquiryController::class, 'close'])->name('school-inquiries.close');
    Route::get('scholarships', [\App\Http\Controllers\Admin\ScholarshipController::class, 'index'])->name('scholarships.index');
    Route::post('scholarships', [\App\Http\Controllers\Admin\ScholarshipController::class, 'store'])->name('scholarships.store');
    // Horizon dashboard (admin only)
    Route::get('horizon', function () {
        return redirect('/horizon');
    })->name('horizon');
});

// --- Teacher: profile + payout requests ---
Route::middleware(['auth', 'role:Teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('profile', [\App\Http\Controllers\Teacher\ProfileController::class, 'show'])->name('profile');
    Route::put('profile', [\App\Http\Controllers\Teacher\ProfileController::class, 'update'])->name('profile.update');
    Route::get('payouts', [\App\Http\Controllers\Teacher\PayoutController::class, 'index'])->name('payouts');
    Route::post('payouts/request', [\App\Http\Controllers\Teacher\PayoutController::class, 'request'])->name('payouts.request');
});

// --- Referral program ---
Route::middleware(['auth'])->prefix('referrals')->name('referrals.')->group(function () {
    Route::get('/', [\App\Http\Controllers\ReferralController::class, 'index'])->name('index');
});

// --- Pricing (geo-detected, public) ---
Route::get('/pricing', [\App\Http\Controllers\PricingController::class, 'index'])->name('pricing');

