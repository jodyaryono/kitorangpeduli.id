<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CitizenTypeController;
use App\Http\Controllers\Api\FamilyController;
use App\Http\Controllers\Api\FieldOfficerController;
use App\Http\Controllers\Api\PuskesmasController;
use App\Http\Controllers\Api\QuestionnaireController;
use App\Http\Controllers\Api\ResidentController;
use App\Http\Controllers\Api\WilayahController;
use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------------------------------
 * | API Routes
 * |--------------------------------------------------------------------------
 */

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/check-phone', [AuthController::class, 'checkPhone']);
    Route::post('/request-otp', [AuthController::class, 'requestOtp']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
});

// Registration (before auth)
Route::post('/residents/register', [ResidentController::class, 'register']);

// Master data (public)
Route::get('/citizen-types', [CitizenTypeController::class, 'index']);

// Occupations (public)
Route::get('/occupations', function() {
    return App\Models\Occupation::select('id', 'code', 'name')->orderBy('code')->get();
});

// Master Data - Educations (public)
Route::get('/educations', function() {
    return DB::table('educations')->select('id', 'code', 'name')->orderBy('code')->get();
});

// Master Data - Family Relations (public)
Route::get('/family-relations', function() {
    return DB::table('family_relations')->select('id', 'code', 'name')->orderBy('code')->get();
});

// Master Data - Marital Statuses (public)
Route::get('/marital-statuses', function() {
    return DB::table('marital_statuses')->select('id', 'code', 'name')->orderBy('code')->get();
});

// Master Data - Religions (public)
Route::get('/religions', function() {
    return DB::table('religions')->select('id', 'code', 'name')->orderBy('code')->get();
});

// Wilayah (public)
Route::prefix('wilayah')->group(function () {
    Route::get('/provinces', [WilayahController::class, 'provinces']);
    Route::get('/regencies/{province}', [WilayahController::class, 'regencies']);
    Route::get('/districts/{regency}', [WilayahController::class, 'districts']);
    Route::get('/villages/{district}', [WilayahController::class, 'villages']);
    Route::get('/search-villages/{query}', [WilayahController::class, 'searchVillages']);
});

// Puskesmas (public for read, protected for create)
Route::get('/puskesmas', [PuskesmasController::class, 'index']);
Route::get('/puskesmas/{id}', [PuskesmasController::class, 'show']);

// Field Officers (public for read)
Route::get('/field-officers', [FieldOfficerController::class, 'index']);
Route::get('/field-officers/{id}', [FieldOfficerController::class, 'show']);

// Family check (public)
Route::post('/families/check', [FamilyController::class, 'check']);
Route::post('/families', [FamilyController::class, 'store']);

// Protected routes (require auth)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Resident profile
    Route::get('/residents/profile', [ResidentController::class, 'profile']);
    Route::put('/residents/location', [ResidentController::class, 'updateLocation']);

    // Family members
    Route::get('/families/{family}/members', [FamilyController::class, 'members']);

    // Questionnaires
    Route::get('/questionnaires', [QuestionnaireController::class, 'index']);
    Route::get('/questionnaires/{questionnaire}', [QuestionnaireController::class, 'show']);
    Route::post('/questionnaires/{questionnaire}/start', [QuestionnaireController::class, 'start']);

    // Responses
    Route::put('/responses/{response}/answer', [QuestionnaireController::class, 'saveAnswer']);
    Route::post('/responses/{response}/complete', [QuestionnaireController::class, 'complete']);

    // Puskesmas (create on-the-fly)
    Route::post('/puskesmas', [PuskesmasController::class, 'store']);
});
