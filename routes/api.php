<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\GlassController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProductController;

Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{product}', [ProductController::class, 'show']);
Route::put('/products/{product}', [ProductController::class, 'update']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);

Route::get('/history', [HistoryController::class, 'index']);
Route::post('/history', [HistoryController::class, 'store']);
Route::get('/history/{history}', [HistoryController::class, 'show']);
Route::put('/history/{history}', [HistoryController::class, 'update']);
Route::delete('/history/{history}', [HistoryController::class, 'destroy']);

Route::prefix('patients')->group(function () {
    Route::get('/{patient_id}/glasses', [GlassController::class, 'index']);
    Route::post('/{patient_id}/glasses', [GlassController::class, 'store']);
    Route::get('/{patient_id}/glasses/{glass_id}', [GlassController::class, 'show']);
    Route::put('/{patient_id}/glasses/{glass_id}', [GlassController::class, 'update']);
    Route::delete('/{patient_id}/glasses/{glass_id}', [GlassController::class, 'destroy']);

    Route::get('/{patient_id}/prescriptions', [PrescriptionController::class, 'index']);
    Route::post('/{patient_id}/prescriptions', [PrescriptionController::class, 'store']);
    Route::get('/{patient_id}/prescriptions/{prescription_id}', [PrescriptionController::class, 'show']);
    Route::put('/{patient_id}/prescriptions/{prescription_id}', [PrescriptionController::class, 'update']);
    Route::delete('/{patient_id}/prescriptions/{prescription_id}', [PrescriptionController::class, 'destroy']);

    Route::get('/', [PatientController::class, 'index']);
    Route::post('/', [PatientController::class, 'store']);
    Route::get('/{patient}', [PatientController::class, 'show']);
    Route::put('/{patient}', [PatientController::class, 'update']);
    Route::delete('/{patient}', [PatientController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
