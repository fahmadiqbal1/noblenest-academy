<?php

use Illuminate\Support\Facades\Route;

// ============================================================================
// HEALTH CHECK ENDPOINTS — NO AUTH REQUIRED
// Used by load balancers, Kubernetes probes, deployment validation
// ============================================================================
Route::get('/health', [\App\Http\Controllers\HealthCheckController::class, 'check'])
    ->withoutMiddleware(['web', 'auth'])
    ->name('health.check');

Route::get('/health/detailed', [\App\Http\Controllers\HealthCheckController::class, 'detailed'])
    ->withoutMiddleware(['web', 'auth'])
    ->name('health.detailed');

// === Noble Nest LMS routes START ===
// Routes appended by Noble Nest LMS scaffolder. Safe to re-run; wrapped by installer markers.

// Home (Noble Nest landing) — separate from existing '/' to avoid conflicts
Route::get('/noble', [\App\Http\Controllers\HomeController::class, 'index'])->name('noble.home');

// AI Assistant endpoint (AJAX)
Route::post('/ai/assistant/message', [\App\Http\Controllers\ChatController::class, 'message'])
    ->middleware('throttle:30,1')
    ->name('ai.assistant.message');

// Phase 1 — admin-only x-ui.* style guide (visual regression baseline).
Route::get('/_styleguide', fn () => view('_styleguide'))
    ->middleware(['auth', 'role:Admin'])
    ->name('admin.styleguide');

// Admin — content + analytics CRUD (Phase 5: merged from 3 adjacent groups into 1).
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    // Courses / modules / quizzes / questions
    Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);
    Route::resource('modules', \App\Http\Controllers\Admin\ModuleController::class);
    Route::resource('quizzes', \App\Http\Controllers\Admin\QuizController::class)->except(['show']);
    Route::resource('quizzes.questions', \App\Http\Controllers\Admin\QuestionController::class)->except(['index', 'show']);

    // Curriculum explorer
    Route::get('curriculum', [\App\Http\Controllers\Admin\CurriculumController::class, 'index'])->name('curriculum');

    // Analytics
    Route::get('analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::post('analytics/report', [\App\Http\Controllers\Admin\AnalyticsController::class, 'reportEmail'])->name('analytics.reportEmail');
    Route::get('analytics/most-liked', [\App\Http\Controllers\Admin\AnalyticsController::class, 'mostLiked'])->name('analytics.mostLiked');
    Route::get('analytics/monthly-completions', [\App\Http\Controllers\Admin\AnalyticsController::class, 'monthlyCompletions'])->name('analytics.monthlyCompletions');
});

// Authentication routes
Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->middleware(['guest', 'throttle:3,1']);
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->middleware('throttle:5,1');
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
    Route::get('/activities/{activity}', [\App\Http\Controllers\ActivityController::class, 'show'])->name('activities.show');

    // Tracing activity routes
    Route::get('/activities/{activity}/tracing', [\App\Http\Controllers\ActivityController::class, 'showTracing'])->name('activities.tracing');
    Route::post('/activities/{activity}/trace', [\App\Http\Controllers\ActivityController::class, 'saveTrace']);

    // Drawing activity routes
    Route::get('/activities/{activity}/drawing', [\App\Http\Controllers\ActivityController::class, 'showDrawing'])->name('activities.drawing');
    Route::post('/activities/{activity}/drawing', [\App\Http\Controllers\ActivityController::class, 'saveDrawing']);

    // Puzzle activity routes
    Route::get('/activities/{activity}/puzzle', [\App\Http\Controllers\ActivityController::class, 'showPuzzle'])->name('activities.puzzle');
    Route::post('/activities/{activity}/puzzle/complete', [\App\Http\Controllers\ActivityController::class, 'savePuzzleComplete'])->name('activities.puzzle.complete');

    // Video player route
    Route::get('/activities/{activity}/video', [\App\Http\Controllers\ActivityController::class, 'showVideo'])->name('activities.video');

    // Slides / simulation viewer route
    Route::get('/activities/{activity}/slides', [\App\Http\Controllers\ActivityController::class, 'showSlides'])->name('activities.slides');
});

// Payment routes (Phase 4: subscriptions only — PayPal removed)
Route::middleware(['auth'])->group(function () {
    Route::post('/checkout/stripe', [\App\Http\Controllers\PaymentController::class, 'stripeCheckout'])->name('checkout.stripe');
    Route::post('/billing/portal', [\App\Http\Controllers\PaymentController::class, 'stripeBillingPortal'])->name('billing.portal');
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
    Route::resource('activities', \App\Http\Controllers\Admin\ActivityController::class)->except(['show']);
    Route::post('activities/bulk-upload', [\App\Http\Controllers\Admin\ActivityController::class, 'bulkUpload'])->name('activities.bulkUpload');
    // Curriculum assignment routes
    Route::post('curriculum/add', [\App\Http\Controllers\Admin\CurriculumController::class, 'add'])->name('curriculum.add');
    Route::post('curriculum/remove', [\App\Http\Controllers\Admin\CurriculumController::class, 'remove'])->name('curriculum.remove');
    Route::post('curriculum/drag-assign', [\App\Http\Controllers\Admin\CurriculumController::class, 'dragAssign'])->name('curriculum.dragAssign');
    Route::post('curriculum/drag-remove', [\App\Http\Controllers\Admin\CurriculumController::class, 'dragRemove'])->name('curriculum.dragRemove');
    // Admin user & child management
    Route::get('users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/role', [\App\Http\Controllers\Admin\AdminUserController::class, 'updateRole'])->name('users.updateRole');
    Route::get('children', [\App\Http\Controllers\Admin\AdminChildController::class, 'index'])->name('children.index');
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
    Route::post('orchestrator/media', [\App\Http\Controllers\Admin\OrchestratorController::class, 'generateMedia'])->name('orchestrator.generateMedia');
    Route::get('orchestrator/media/{job}/status', [\App\Http\Controllers\Admin\OrchestratorController::class, 'mediaJobStatus'])->name('orchestrator.mediaJobStatus');
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

// ===========================================================================
// PHASES 2-9: NEW ROUTES
// ===========================================================================

// --- Scholarship (public) ---
Route::get('/scholarship/apply', fn() => view('scholarship.apply'))->name('scholarship.apply');
Route::post('/scholarship/apply', [\App\Http\Controllers\Admin\ScholarshipController::class, 'publicApply'])->name('scholarship.apply.post');

// --- Milestone Wall (public) ---
Route::get('/milestones', [\App\Http\Controllers\MilestoneWallController::class, 'index'])->name('milestones.wall');

// --- Child dashboard + assessment (parent auth) ---
Route::middleware(['auth', 'role:Parent'])->group(function () {
    Route::get('/child/{child}/dashboard', [\App\Http\Controllers\Child\DashboardController::class, 'show'])->name('child.dashboard');
    Route::get('/child/{child}/assessment', [\App\Http\Controllers\AssessmentController::class, 'index'])->name('child.assessment');
});

// --- Course reviews (student) ---
Route::middleware(['auth', 'role:Student'])->group(function () {
    Route::post('courses/{course}/reviews', [\App\Http\Controllers\Student\CourseReviewController::class, 'store'])->name('course.reviews.store');
    Route::delete('courses/{course}/reviews', [\App\Http\Controllers\Student\CourseReviewController::class, 'destroy'])->name('course.reviews.destroy');
});

// --- Teacher analytics ---
Route::middleware(['auth', 'role:Teacher'])->prefix('teacher')->name('teacher.')->group(function () {
    Route::get('analytics', [\App\Http\Controllers\Teacher\AnalyticsController::class, 'index'])->name('analytics');
});

// --- Admin: content batch + review pipeline ---
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('content-batch/create', [\App\Http\Controllers\Admin\ContentBatchController::class, 'create'])->name('content-batch.create');
    Route::post('content-batch', [\App\Http\Controllers\Admin\ContentBatchController::class, 'store'])->name('content-batch.store');
    Route::get('content-batch/{job}/preview', [\App\Http\Controllers\Admin\ContentBatchController::class, 'preview'])->name('content-batch.preview');
    Route::post('content-batch/{job}/publish', [\App\Http\Controllers\Admin\ContentBatchController::class, 'publish'])->name('content-batch.publish');
    Route::get('content-review', [\App\Http\Controllers\Admin\ContentReviewController::class, 'index'])->name('content-review.index');
    Route::post('content-review/{activity}/approve', [\App\Http\Controllers\Admin\ContentReviewController::class, 'approve'])->name('content-review.approve');
    Route::delete('content-review/{activity}', [\App\Http\Controllers\Admin\ContentReviewController::class, 'reject'])->name('content-review.reject');
    Route::post('content-review/approve-all', [\App\Http\Controllers\Admin\ContentReviewController::class, 'approveAll'])->name('content-review.approve-all');
});

// --- Password Reset ---
Route::get('/forgot-password', fn() => view('auth.forgot-password'))->middleware('guest')->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLink'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', fn($token) => view('auth.reset-password', ['token' => $token]))->middleware('guest')->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->middleware('guest')->name('password.update');

// --- Privacy + GDPR / COPPA (authenticated) ---
Route::middleware(['auth'])->prefix('privacy')->name('privacy.')->group(function () {
    Route::get('/', [\App\Http\Controllers\PrivacyController::class, 'index'])->name('dashboard');
    Route::get('export', [\App\Http\Controllers\PrivacyController::class, 'exportData'])->name('export');
    Route::delete('delete', [\App\Http\Controllers\PrivacyController::class, 'deleteData'])->name('delete');
    // Phase 5: under-13 Parental Consent gate (COPPA / GDPR-K).
    Route::get('parental-consent/{child}',  [\App\Http\Controllers\PrivacyController::class, 'showParentalConsent'])->name('parental-consent');
    Route::post('parental-consent/{child}', [\App\Http\Controllers\PrivacyController::class, 'recordParentalConsent'])->name('parental-consent.store');
});

// ===========================================================================
// MATERNAL WELLNESS MODULE
// ===========================================================================

// Onboarding (auth only, feature-flagged but no consent required yet)
Route::middleware(['auth', 'feature:maternal_module'])->prefix('maternal')->name('maternal.')->group(function () {
    Route::get('onboarding', [\App\Http\Controllers\Maternal\OnboardingController::class, 'create'])->name('onboarding');
    Route::post('onboarding', [\App\Http\Controllers\Maternal\OnboardingController::class, 'store'])->name('onboarding.store');
});

// All other maternal routes require consent
Route::middleware(['auth', 'feature:maternal_module', 'maternal.consent'])->prefix('maternal')->name('maternal.')->group(function () {
    // Dashboard
    Route::get('/', [\App\Http\Controllers\Maternal\DashboardController::class, 'index'])->name('dashboard');

    // Profile management
    Route::get('profile', [\App\Http\Controllers\Maternal\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [\App\Http\Controllers\Maternal\ProfileController::class, 'update'])->name('profile.update');
    Route::post('profile/pause', [\App\Http\Controllers\Maternal\ProfileController::class, 'pause'])->name('profile.pause');
    Route::post('profile/resume', [\App\Http\Controllers\Maternal\ProfileController::class, 'resume'])->name('profile.resume');
    Route::post('profile/loss', [\App\Http\Controllers\Maternal\ProfileController::class, 'markLoss'])->name('profile.loss');

    // Journey (week-by-week timeline)
    Route::get('journey', [\App\Http\Controllers\Maternal\JourneyController::class, 'index'])->name('journey');
    Route::get('journey/week/{week}', [\App\Http\Controllers\Maternal\JourneyController::class, 'week'])->name('journey.week')->where('week', '[0-9]+');

    // Content browsing
    Route::get('content', [\App\Http\Controllers\Maternal\ContentController::class, 'index'])->name('content.index');
    Route::get('content/{maternalContent}', [\App\Http\Controllers\Maternal\ContentController::class, 'show'])->name('content.show');
    Route::post('content/{maternalContent}/start', [\App\Http\Controllers\Maternal\ContentController::class, 'start'])->name('content.start');
    Route::post('content/{maternalContent}/complete', [\App\Http\Controllers\Maternal\ContentController::class, 'complete'])->name('content.complete');

    // Exercise plans
    Route::get('exercises', [\App\Http\Controllers\Maternal\ExerciseController::class, 'index'])->name('exercises.index');
    Route::get('exercises/{maternalExercisePlan}', [\App\Http\Controllers\Maternal\ExerciseController::class, 'show'])->name('exercises.show');

    // Nutrition & meal plans
    Route::get('nutrition', [\App\Http\Controllers\Maternal\NutritionController::class, 'index'])->name('nutrition.index');
    Route::get('nutrition/{maternalMealPlan}', [\App\Http\Controllers\Maternal\NutritionController::class, 'show'])->name('nutrition.show');

    // Herbs & natural remedies
    Route::get('herbs', [\App\Http\Controllers\Maternal\HerbController::class, 'index'])->name('herbs.index');

    // Breastfeeding education
    Route::get('breastfeeding', [\App\Http\Controllers\Maternal\BreastfeedingController::class, 'index'])->name('breastfeeding.index');

    // Newborn care & training
    Route::get('newborn', [\App\Http\Controllers\Maternal\NewbornController::class, 'index'])->name('newborn.index');

    // Cultural techniques
    Route::get('techniques/{culture}', [\App\Http\Controllers\Maternal\TechniqueController::class, 'index'])->name('techniques.index')
        ->where('culture', 'chinese|japanese|ayurvedic|general');

    // Journal
    Route::get('journal', [\App\Http\Controllers\Maternal\JournalController::class, 'index'])->name('journal.index');
    Route::get('journal/create', [\App\Http\Controllers\Maternal\JournalController::class, 'create'])->name('journal.create');
    Route::post('journal', [\App\Http\Controllers\Maternal\JournalController::class, 'store'])->name('journal.store');
    Route::get('journal/{maternalJournal}', [\App\Http\Controllers\Maternal\JournalController::class, 'show'])->name('journal.show');

    // Emergency signs reference
    Route::get('emergency-signs', [\App\Http\Controllers\Maternal\EmergencySignController::class, 'index'])->name('emergency-signs');
});

// Admin: maternal content management
Route::middleware(['auth', 'role:Admin'])->prefix('admin/maternal')->name('admin.maternal.')->group(function () {
    Route::get('content', [\App\Http\Controllers\Admin\MaternalContentController::class, 'index'])->name('content.index');
    Route::get('content/create', [\App\Http\Controllers\Admin\MaternalContentController::class, 'create'])->name('content.create');
    Route::post('content', [\App\Http\Controllers\Admin\MaternalContentController::class, 'store'])->name('content.store');
    Route::get('content/{maternalContent}/edit', [\App\Http\Controllers\Admin\MaternalContentController::class, 'edit'])->name('content.edit');
    Route::put('content/{maternalContent}', [\App\Http\Controllers\Admin\MaternalContentController::class, 'update'])->name('content.update');
    Route::delete('content/{maternalContent}', [\App\Http\Controllers\Admin\MaternalContentController::class, 'destroy'])->name('content.destroy');
    Route::post('content/{maternalContent}/approve', [\App\Http\Controllers\Admin\MaternalContentController::class, 'approve'])->name('content.approve');
    Route::post('content/{maternalContent}/reject', [\App\Http\Controllers\Admin\MaternalContentController::class, 'reject'])->name('content.reject');
    Route::post('content/{maternalContent}/generate-animations', [\App\Http\Controllers\Admin\MaternalContentController::class, 'generateAnimations'])->name('content.generateAnimations');
});

// ===========================================================================
// PRACTITIONER PORTAL
// ===========================================================================

// Practitioner setup (no active check — they need to set up profile first)
Route::middleware(['auth', 'feature:practitioner_portal'])->prefix('practitioner')->name('practitioner.')->group(function () {
    Route::get('setup', [\App\Http\Controllers\Practitioner\ProfileController::class, 'setup'])->name('profile.setup');
    Route::post('setup', [\App\Http\Controllers\Practitioner\ProfileController::class, 'storeSetup'])->name('profile.storeSetup');
});

// Practitioner protected routes (require active profile)
Route::middleware(['auth', 'feature:practitioner_portal', 'practitioner.active'])->prefix('practitioner')->name('practitioner.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Practitioner\DashboardController::class, 'index'])->name('dashboard');
    Route::get('profile/edit', [\App\Http\Controllers\Practitioner\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [\App\Http\Controllers\Practitioner\ProfileController::class, 'update'])->name('profile.update');
    Route::get('reviews', [\App\Http\Controllers\Practitioner\ContentReviewController::class, 'index'])->name('reviews.index');
    Route::get('reviews/{maternalContent}', [\App\Http\Controllers\Practitioner\ContentReviewController::class, 'show'])->name('reviews.show');
    Route::post('reviews/{maternalContent}', [\App\Http\Controllers\Practitioner\ContentReviewController::class, 'store'])->name('reviews.store');
});

// Admin: practitioner management
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('practitioners', [\App\Http\Controllers\Admin\PractitionerController::class, 'index'])->name('practitioners.index');
    Route::post('practitioners/{practitionerProfile}/suspend', [\App\Http\Controllers\Admin\PractitionerController::class, 'suspend'])->name('practitioners.suspend');
    Route::post('practitioners/{practitionerProfile}/unsuspend', [\App\Http\Controllers\Admin\PractitionerController::class, 'unsuspend'])->name('practitioners.unsuspend');
});

