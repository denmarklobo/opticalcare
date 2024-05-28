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

Route::get('/glass', [GlassController::class, 'index']);
Route::post('/glass', [GlassController::class, 'store']);
Route::get('/glass/{glass}', [GlassController::class, 'show']);
Route::put('/glass/{glass}', [GlassController::class, 'update']);
Route::delete('/glass/{glass}', [GlassController::class, 'destroy']);

Route::get('/patients/{patientId}/prescriptions', [PrescriptionController::class, 'index']);
Route::post('/patients/{patientId}/prescriptions', [PrescriptionController::class, 'store']);
Route::get('/patients/{patientId}/prescriptions/{prescription}', [PrescriptionController::class, 'show']);
Route::put('/patients/{patientId}/prescriptions/{prescription}', [PrescriptionController::class, 'update']);
Route::delete('/patients/{patientId}/prescriptions/{prescription}', [PrescriptionController::class, 'destroy']);

Route::get('/patients', [PatientController::class, 'index']);
Route::post('/patients', [PatientController::class, 'store']);
Route::get('/patients/{patient}', [PatientController::class, 'show']);
Route::put('/patients/{patient}', [PatientController::class, 'update']);
Route::delete('/patients/{patient}', [PatientController::class, 'destroy']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
