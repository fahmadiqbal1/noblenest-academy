// Routes appended by Noble Nest LMS scaffolder. Safe to re-run; wrapped by installer markers.

// Home (Noble Nest landing) — separate from existing '/' to avoid conflicts
Route::get('/noble', [\App\Http\Controllers\HomeController::class, 'index'])->name('noble.home');

// AI Assistant endpoint (AJAX)
Route::post('/ai/assistant/message', [\App\Http\Controllers\ChatController::class, 'message'])->name('ai.assistant.message');

// Admin — Course management (basic CRUD)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);
});
