<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CitizenTypeController;
use App\Http\Controllers\Api\KartuKeluargaController;
use App\Http\Controllers\Api\QuestionnaireController;
use App\Http\Controllers\Api\RespondentController;
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
Route::post('/respondents/register', [RespondentController::class, 'register']);

// Master data (public)
Route::get('/citizen-types', [CitizenTypeController::class, 'index']);

// Wilayah (public)
Route::prefix('wilayah')->group(function () {
    Route::get('/provinces', [WilayahController::class, 'provinces']);
    Route::get('/regencies/{province}', [WilayahController::class, 'regencies']);
    Route::get('/districts/{regency}', [WilayahController::class, 'districts']);
    Route::get('/villages/{district}', [WilayahController::class, 'villages']);
    Route::get('/search-villages/{query}', [WilayahController::class, 'searchVillages']);
});

// KK check (public)
Route::post('/kartu-keluarga/check', [KartuKeluargaController::class, 'check']);
Route::post('/kartu-keluarga', [KartuKeluargaController::class, 'store']);

// Protected routes (require auth)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Respondent profile
    Route::get('/respondents/profile', [RespondentController::class, 'profile']);
    Route::put('/respondents/location', [RespondentController::class, 'updateLocation']);

    // KK members
    Route::get('/kartu-keluarga/{kartuKeluarga}/members', [KartuKeluargaController::class, 'members']);

    // Questionnaires
    Route::get('/questionnaires', [QuestionnaireController::class, 'index']);
    Route::get('/questionnaires/{questionnaire}', [QuestionnaireController::class, 'show']);
    Route::post('/questionnaires/{questionnaire}/start', [QuestionnaireController::class, 'start']);

    // Responses
    Route::put('/responses/{response}/answer', [QuestionnaireController::class, 'saveAnswer']);
    Route::post('/responses/{response}/complete', [QuestionnaireController::class, 'complete']);
});
