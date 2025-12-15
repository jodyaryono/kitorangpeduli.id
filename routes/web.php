<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\QuestionnaireController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth routes (guest only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login/send-otp', [AuthController::class, 'sendOtp'])->name('login.send-otp');
    Route::post('/login/verify-otp', [AuthController::class, 'verifyOtp'])->name('login.verify-otp');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

// Logout
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// Profile routes (authenticated respondents only)
Route::get('/profile', [AuthController::class, 'showProfile'])->name('profile.show');
Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

// Questionnaire routes
Route::get('/questionnaire/{id}/start', [QuestionnaireController::class, 'start'])->name('questionnaire.start');
Route::post('/questionnaire/{id}/autosave', [QuestionnaireController::class, 'autosave'])->name('questionnaire.autosave');
Route::post('/questionnaire/{id}/submit', [QuestionnaireController::class, 'submit'])->name('questionnaire.submit');
Route::get('/questionnaire/success/{responseId}', [QuestionnaireController::class, 'success'])->name('questionnaire.success');

// Officer data entry standalone page (outside Filament UI)
Route::middleware(['auth'])->group(function () {
    Route::get('/officer-entry', [\App\Http\Controllers\OfficerEntryController::class, 'show'])->name('officer.entry');
    Route::get('/officer-entry/questionnaire/{id}', [\App\Http\Controllers\OfficerEntryController::class, 'selectQuestionnaire'])->name('officer.entry.questionnaire');
    Route::post('/officer-entry', [\App\Http\Controllers\OfficerEntryController::class, 'store'])->name('officer.entry.store');
    Route::get('/officer-entry/respondent/create', [\App\Http\Controllers\OfficerEntryController::class, 'createRespondent'])->name('officer.respondent.create');
    Route::post('/officer-entry/respondent', [\App\Http\Controllers\OfficerEntryController::class, 'storeRespondent'])->name('officer.respondent.store');
});

// Export routes (admin only)
Route::middleware(['auth'])->prefix('export')->name('export.')->group(function () {
    Route::get('/respondents', [ExportController::class, 'exportRespondents'])->name('respondents');
    Route::get('/responses', [ExportController::class, 'exportResponses'])->name('responses');
    Route::get('/answers/{questionnaireId}', [ExportController::class, 'exportAnswers'])->name('answers');
});

// Report AI routes (authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/laporan-ai', [\App\Http\Controllers\LaporanAIController::class, 'index'])
        ->name('laporan-ai.index');
    Route::get('/admin/laporan-ai/quick-ideas', [\App\Http\Controllers\LaporanAIController::class, 'quickIdeas'])
        ->name('admin.laporan-ai.quick-ideas');
    Route::post('/admin/laporan-ai/submit', [\App\Http\Controllers\AiReportController::class, 'submit'])
        ->name('admin.laporan-ai.submit');
    Route::post('/api/tts/generate', [\App\Http\Controllers\TTSController::class, 'generate'])
        ->name('api.tts.generate');
});
