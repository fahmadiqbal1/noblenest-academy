<?php

use Illuminate\Support\Facades\Route;

// ============================================================================
// HEALTH CHECK ENDPOINTS — NO AUTH REQUIRED
// Used by load balancers, Kubernetes probes, deployment validation.
// ============================================================================
Route::get('/health', [\App\Http\Controllers\HealthCheckController::class, 'check'])
    ->withoutMiddleware(['web', 'auth'])
    ->name('health.check');

Route::get('/health/detailed', [\App\Http\Controllers\HealthCheckController::class, 'detailed'])
    ->withoutMiddleware(['web', 'auth'])
    ->name('health.detailed');

// ============================================================================
// PUBLIC PAGES
// ============================================================================
Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/noble', [\App\Http\Controllers\HomeController::class, 'index'])->name('noble.home');

Route::view('/privacy', 'privacy')->name('privacy');
Route::view('/terms', 'terms')->name('terms');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::view('/for-schools', 'pages.for-schools')->name('for-schools'); // Phase 7 will wire the institutional inquiry form.

Route::get('/milestones', [\App\Http\Controllers\MilestoneWallController::class, 'index'])->name('milestones.wall');
Route::get('/pricing', [\App\Http\Controllers\PricingController::class, 'index'])->name('pricing');

// Phase 1 — admin-only style guide (visual regression baseline).
Route::get('/_styleguide', fn () => view('_styleguide'))
    ->middleware(['auth', 'role:Admin'])
    ->name('admin.styleguide');

// ============================================================================
// AUTH
// ============================================================================
Route::get('/register', [\App\Http\Controllers\AuthController::class, 'showRegister'])->name('register')->middleware('guest');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->middleware(['guest', 'throttle:register']);
Route::get('/login', [\App\Http\Controllers\AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login'])->middleware('throttle:auth');
Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', fn () => view('auth.forgot-password'))->middleware('guest')->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLink'])->middleware(['guest', 'throttle:password-reset'])->name('password.email');
Route::get('/reset-password/{token}', fn ($token) => view('auth.reset-password', ['token' => $token]))->middleware('guest')->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->middleware('guest')->name('password.update');

// ============================================================================
// LANGUAGE & SETTINGS
// ============================================================================
Route::post('/set-language', [\App\Http\Controllers\SettingsController::class, 'setLanguage'])->name('set-language');
Route::post('/dismiss-onboarding', [\App\Http\Controllers\SettingsController::class, 'dismissOnboarding'])->name('dismiss-onboarding');

Route::get('/lang/{lang}', function (\Illuminate\Http\Request $request, $lang) {
    $allowed = ['en', 'fr', 'ru', 'zh', 'es', 'ko', 'ur', 'ar'];
    if (! in_array($lang, $allowed, true)) {
        abort(400, 'Invalid language code');
    }
    $request->session()->put('lang', $lang);

    return redirect()->back(302, [], route('noble.home'));
})->name('lang.switch');

Route::post('/theme-toggle', function () {
    $current = session('theme', 'professional');
    $next = $current === 'playful' ? 'professional' : 'playful';
    session(['theme' => $next]);

    return response()->noContent();
})->name('theme.toggle');

// ============================================================================
// AI ASSISTANT (AJAX)
// ============================================================================
Route::post('/ai/assistant/message', [\App\Http\Controllers\ChatController::class, 'message'])
    ->middleware('throttle:ai-assistant')
    ->name('ai.assistant.message');

// ============================================================================
// ONBOARDING (Parent) — Phase 5 rebuild planned
// ============================================================================
Route::middleware(['auth'])->group(function () {
    // Legacy single-page route — redirects to /onboarding/step/1.
    Route::get('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'show'])->name('onboarding');
    Route::post('/onboarding', [\App\Http\Controllers\OnboardingController::class, 'store'])->name('onboarding.store');

    // Phase 5 — 5-step flow.
    Route::get('/onboarding/step/1', [\App\Http\Controllers\OnboardingController::class, 'showStep1'])->name('onboarding.step1');
    Route::post('/onboarding/step/1', [\App\Http\Controllers\OnboardingController::class, 'storeStep1'])->name('onboarding.step1.store');
    Route::get('/onboarding/step/2', [\App\Http\Controllers\OnboardingController::class, 'showStep2'])->name('onboarding.step2');
    Route::post('/onboarding/step/2', [\App\Http\Controllers\OnboardingController::class, 'storeStep2'])->name('onboarding.step2.store');
    Route::get('/onboarding/step/3', [\App\Http\Controllers\OnboardingController::class, 'showStep3'])->name('onboarding.step3');
    Route::post('/onboarding/step/3', [\App\Http\Controllers\OnboardingController::class, 'storeStep3'])->name('onboarding.step3.store');
    Route::get('/onboarding/step/4', [\App\Http\Controllers\OnboardingController::class, 'showStep4'])->name('onboarding.step4');
    Route::post('/onboarding/step/4', [\App\Http\Controllers\OnboardingController::class, 'storeStep4'])->name('onboarding.step4.store');
    Route::get('/onboarding/step/5/{child}', [\App\Http\Controllers\OnboardingController::class, 'showStep5'])->name('onboarding.step5');
    Route::post('/onboarding/step/5/{child}', [\App\Http\Controllers\OnboardingController::class, 'completeStep5'])->name('onboarding.step5.complete');

    // Parent PIN entry — the gate itself, so no parent.pin middleware here.
    Route::get('/parent/pin', [\App\Http\Controllers\ParentPinController::class, 'show'])->name('parent.pin.show');
    Route::post('/parent/pin', [\App\Http\Controllers\ParentPinController::class, 'verify'])
        ->middleware('throttle:pin-verify')
        ->name('parent.pin.verify');

    Route::view('/profile', 'profile')->name('profile');
    Route::view('/checkout', 'checkout')->name('checkout');
});

// ============================================================================
// PARENT — children CRUD + dashboard
// ============================================================================
Route::middleware(['auth', 'role:Parent'])->group(function () {
    Route::get('/children', [\App\Http\Controllers\Parent\ChildController::class, 'index'])->name('children.index');
    Route::get('/children/create', [\App\Http\Controllers\Parent\ChildController::class, 'create'])->name('children.create');
    Route::post('/children', [\App\Http\Controllers\Parent\ChildController::class, 'store'])->name('children.store');
    Route::get('/children/{child}/edit', [\App\Http\Controllers\Parent\ChildController::class, 'edit'])->name('children.edit');
    Route::put('/children/{child}', [\App\Http\Controllers\Parent\ChildController::class, 'update'])->name('children.update');
    Route::delete('/children/{child}', [\App\Http\Controllers\Parent\ChildController::class, 'destroy'])->name('children.destroy');
});

Route::middleware(['auth', 'role:Parent'])->prefix('parent')->name('parent.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Parent\DashboardController::class, 'index'])->name('dashboard');
    Route::get('child/{child}', [\App\Http\Controllers\Parent\DashboardController::class, 'child'])->name('child');
    Route::get('milestones', [\App\Http\Controllers\Parent\MilestoneController::class, 'index'])->name('milestones');
    Route::post('child/{child}/milestone/{milestone}/toggle', [\App\Http\Controllers\Parent\MilestoneController::class, 'toggle'])->name('milestone.toggle');
});

// ============================================================================
// CHILD — dashboard + assessment + activity feed (under-13 = COPPA-gated)
// ============================================================================
Route::middleware(['auth', 'role:Parent', 'parental.consent'])->group(function () {
    Route::get('/child/{child}/dashboard', [\App\Http\Controllers\Child\DashboardController::class, 'show'])->name('child.dashboard');
    Route::get('/child/{child}/assessment', [\App\Http\Controllers\AssessmentController::class, 'index'])->name('child.assessment');
});

Route::middleware(['auth', 'parental.consent'])->group(function () {
    Route::get('/child/{child}/activities', [\App\Http\Controllers\ChildActivityController::class, 'index'])->name('child.activities');
    Route::post('/child/{child}/activities/{activity}/complete', [\App\Http\Controllers\ChildActivityController::class, 'complete'])->name('child.activity.complete');
});

// ============================================================================
// ACTIVITIES (subscription-gated)
// ============================================================================
Route::middleware(['auth', 'subscription.active'])->group(function () {
    Route::get('/activities', [\App\Http\Controllers\ActivityController::class, 'index'])->name('activities.index');
    Route::get('/activities/{activity}', [\App\Http\Controllers\ActivityController::class, 'show'])->name('activities.show');

    Route::get('/activities/{activity}/tracing', [\App\Http\Controllers\ActivityController::class, 'showTracing'])->name('activities.tracing');
    Route::post('/activities/{activity}/trace', [\App\Http\Controllers\ActivityController::class, 'saveTrace']);

    Route::get('/activities/{activity}/drawing', [\App\Http\Controllers\ActivityController::class, 'showDrawing'])->name('activities.drawing');
    Route::post('/activities/{activity}/drawing', [\App\Http\Controllers\ActivityController::class, 'saveDrawing']);

    Route::get('/activities/{activity}/puzzle', [\App\Http\Controllers\ActivityController::class, 'showPuzzle'])->name('activities.puzzle');
    Route::post('/activities/{activity}/puzzle/complete', [\App\Http\Controllers\ActivityController::class, 'savePuzzleComplete'])->name('activities.puzzle.complete');

    Route::get('/activities/{activity}/video', [\App\Http\Controllers\ActivityController::class, 'showVideo'])->name('activities.video');
    Route::get('/activities/{activity}/slides', [\App\Http\Controllers\ActivityController::class, 'showSlides'])->name('activities.slides');
});

// ============================================================================
// QUIZZES
// ============================================================================
Route::get('/quizzes', [\App\Http\Controllers\QuizController::class, 'index'])->name('quizzes.index');
Route::get('/quizzes/{quiz}', [\App\Http\Controllers\QuizController::class, 'show'])->name('quizzes.show');
Route::post('/quizzes/{quiz}/submit', [\App\Http\Controllers\QuizController::class, 'submit'])->name('quizzes.submit');

// ============================================================================
// PAYMENTS — Stripe (Phase 7 will add PayPal + PPP)
// ============================================================================
Route::middleware(['auth'])->group(function () {
    Route::post('/checkout/stripe', [\App\Http\Controllers\PaymentController::class, 'stripeCheckout'])->name('checkout.stripe');
    Route::post('/billing/portal', [\App\Http\Controllers\PaymentController::class, 'stripeBillingPortal'])->name('billing.portal');
    Route::get('/payment/success/{provider}', [\App\Http\Controllers\PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/cancel/{provider}', [\App\Http\Controllers\PaymentController::class, 'paymentCancel'])->name('payment.cancel');
});
Route::post('/webhook/stripe', [\App\Http\Controllers\PaymentController::class, 'stripeWebhook'])->name('webhook.stripe');

// ============================================================================
// PAYMENTS — PayPal (Phase 7 scaffold; live keys injected Phase 12/13)
// ============================================================================
Route::middleware(['auth'])->group(function () {
    Route::post('/checkout/paypal/create', [\App\Http\Controllers\PayPalController::class, 'create'])->name('paypal.create');
    Route::post('/checkout/paypal/{orderId}/capture', [\App\Http\Controllers\PayPalController::class, 'capture'])->name('paypal.capture');
});
Route::post('/webhook/paypal', [\App\Http\Controllers\PayPalController::class, 'webhook'])->name('webhook.paypal');

// ============================================================================
// INSTITUTIONAL LICENSING — Phase 7 MVP
// ============================================================================
Route::get('/institutional/invite/{token}', [\App\Http\Controllers\InstitutionalController::class, 'showInvite'])->name('institutional.invite.show');
Route::post('/institutional/invite/{token}', [\App\Http\Controllers\InstitutionalController::class, 'acceptInvite'])->name('institutional.invite.accept');

Route::middleware(['auth', 'role:school_admin'])->group(function () {
    Route::get('/school/dashboard', [\App\Http\Controllers\InstitutionalController::class, 'dashboard'])->name('school.dashboard');
    Route::post('/school/seats', [\App\Http\Controllers\InstitutionalController::class, 'assignSeats'])->name('school.seats.assign');
});

// ============================================================================
// NOTIFICATIONS
// ============================================================================
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::post('{id}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('read');
    Route::post('read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('read-all');
});

// ============================================================================
// PRIVACY / GDPR / COPPA
// ============================================================================
Route::middleware(['auth'])->prefix('privacy')->name('privacy.')->group(function () {
    Route::get('/', [\App\Http\Controllers\PrivacyController::class, 'index'])->name('dashboard');
    Route::get('parental-consent/{child}', [\App\Http\Controllers\PrivacyController::class, 'showParentalConsent'])->name('parental-consent');
    Route::post('parental-consent/{child}', [\App\Http\Controllers\PrivacyController::class, 'recordParentalConsent'])->name('parental-consent.store');

    // PIN-gated: data export + erase + signed download.
    Route::middleware('parent.pin')->group(function () {
        Route::get('export', [\App\Http\Controllers\PrivacyController::class, 'exportData'])->name('export');
        Route::delete('delete', [\App\Http\Controllers\PrivacyController::class, 'deleteData'])->name('delete');
    });

    // Signed download (the URL itself is the auth — short TTL).
    Route::get('export/{user}/{ts}', [\App\Http\Controllers\PrivacyController::class, 'downloadExport'])
        ->name('export.download')
        ->middleware('signed');
});

// ============================================================================
// ADMIN
// ============================================================================
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    // Courses / modules / quizzes / questions
    Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);
    Route::resource('modules', \App\Http\Controllers\Admin\ModuleController::class);
    Route::resource('quizzes', \App\Http\Controllers\Admin\QuizController::class)->except(['show']);
    Route::resource('quizzes.questions', \App\Http\Controllers\Admin\QuestionController::class)->except(['index', 'show']);

    // Activities
    Route::resource('activities', \App\Http\Controllers\Admin\ActivityController::class)->except(['show']);
    Route::post('activities/bulk-upload', [\App\Http\Controllers\Admin\ActivityController::class, 'bulkUpload'])->name('activities.bulkUpload');

    // Curriculum explorer + assignment
    Route::get('curriculum', [\App\Http\Controllers\Admin\CurriculumController::class, 'index'])->name('curriculum');
    Route::post('curriculum/add', [\App\Http\Controllers\Admin\CurriculumController::class, 'add'])->name('curriculum.add');
    Route::post('curriculum/remove', [\App\Http\Controllers\Admin\CurriculumController::class, 'remove'])->name('curriculum.remove');
    Route::post('curriculum/drag-assign', [\App\Http\Controllers\Admin\CurriculumController::class, 'dragAssign'])->name('curriculum.dragAssign');
    Route::post('curriculum/drag-remove', [\App\Http\Controllers\Admin\CurriculumController::class, 'dragRemove'])->name('curriculum.dragRemove');

    // User & child management
    Route::get('users', [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('users.index');
    Route::patch('users/{user}/role', [\App\Http\Controllers\Admin\AdminUserController::class, 'updateRole'])->name('users.updateRole');
    Route::get('children', [\App\Http\Controllers\Admin\AdminChildController::class, 'index'])->name('children.index');

    // Analytics
    Route::get('analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('dashboard', fn () => redirect()->route('admin.analytics.index'))->name('dashboard');
    Route::post('analytics/report', [\App\Http\Controllers\Admin\AnalyticsController::class, 'reportEmail'])->name('analytics.reportEmail');
    Route::get('analytics/most-liked', [\App\Http\Controllers\Admin\AnalyticsController::class, 'mostLiked'])->name('analytics.mostLiked');
    Route::get('analytics/monthly-completions', [\App\Http\Controllers\Admin\AnalyticsController::class, 'monthlyCompletions'])->name('analytics.monthlyCompletions');

    // Content batch + review pipeline
    Route::get('content-batch/create', [\App\Http\Controllers\Admin\ContentBatchController::class, 'create'])->name('content-batch.create');
    Route::post('content-batch', [\App\Http\Controllers\Admin\ContentBatchController::class, 'store'])->name('content-batch.store');
    Route::get('content-batch/{job}/preview', [\App\Http\Controllers\Admin\ContentBatchController::class, 'preview'])->name('content-batch.preview');
    Route::post('content-batch/{job}/publish', [\App\Http\Controllers\Admin\ContentBatchController::class, 'publish'])->name('content-batch.publish');
    Route::get('content-review', [\App\Http\Controllers\Admin\ContentReviewController::class, 'index'])->name('content-review.index');
    Route::post('content-review/{activity}/approve', [\App\Http\Controllers\Admin\ContentReviewController::class, 'approve'])->name('content-review.approve');
    Route::delete('content-review/{activity}', [\App\Http\Controllers\Admin\ContentReviewController::class, 'reject'])->name('content-review.reject');
    Route::post('content-review/approve-all', [\App\Http\Controllers\Admin\ContentReviewController::class, 'approveAll'])->name('content-review.approve-all');

    // AI Orchestrator
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
    Route::post('orchestrator/fill-gaps', [\App\Http\Controllers\Admin\OrchestratorController::class, 'fillGaps'])->name('orchestrator.fillGaps');
    Route::post('orchestrator/media', [\App\Http\Controllers\Admin\OrchestratorController::class, 'generateMedia'])->name('orchestrator.generateMedia');
    Route::get('orchestrator/media/{job}/status', [\App\Http\Controllers\Admin\OrchestratorController::class, 'mediaJobStatus'])->name('orchestrator.mediaJobStatus');

    // Horizon dashboard (admin only)
    Route::get('horizon', fn () => redirect('/horizon'))->name('horizon');

    // Phase 7 — Institutional invites (admin-only).
    Route::post('institutional/invite', [\App\Http\Controllers\InstitutionalController::class, 'adminCreateInvite'])->name('institutional.invite.create');
});
