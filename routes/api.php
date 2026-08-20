<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiQuestionController;
use App\Http\Controllers\Api\DocumentGenerationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::get('/questions', [ApiQuestionController::class, 'index']);
Route::get('/questions/{id}', [ApiQuestionController::class, 'show']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/document/start', [DocumentGenerationController::class, 'startDocumentGeneration'])->name('api.document.start');
