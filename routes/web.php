<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\GlassController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/products', [ProductController::class, 'store']);
Route::get('/products', [ProductController::class, 'index']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);

Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
Route::post('/history', [HistoryController::class, 'store'])->name('history.store');
Route::get('/history/{history}', [HistoryController::class, 'show'])->name('history.show');
Route::put('/history/{history}', [HistoryController::class, 'update'])->name('history.update');
Route::delete('/history/{history}', [HistoryController::class, 'destroy'])->name('history.destroy');

Route::get('/glass', [GlassController::class, 'index'])->name('glass.index');
Route::post('/glass', [GlassController::class, 'store'])->name('glass.store');
Route::get('/glass/{glass}', [GlassController::class, 'show'])->name('glass.show');
Route::put('/glass/{glass}', [GlassController::class, 'update'])->name('glass.update');
Route::delete('/glass/{glass}', [GlassController::class, 'destroy'])->name('glass.destroy');

Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
Route::post('/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show'])->name('prescriptions.show');
Route::put('/prescriptions/{prescription}', [PrescriptionController::class, 'update'])->name('prescriptions.update');
Route::delete('/prescriptions/{prescription}', [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');

Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');
Route::delete('/patients/{patient}', [PatientController::class, 'destroy'])->name('patients.destroy');

Route::get('/', function () {
    return view('welcome');
});
