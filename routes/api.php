<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\GlassController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ResetPasswordController;

Route::post('/password/email', [ResetPasswordController::class, 'sendResetLinkEmail']);

Route::post('/password/reset', [ResetPasswordController::class, 'resetPassword']);


Route::post('/login', [LoginController::class, 'authenticate']);
Route::get('/authlogin', [AdminController::class, 'login']);
Route::post('/authlogin', [AdminController::class, 'login']);
Route::post('/storeadmin', [AdminController::class, 'store']);

Route::post('/products', [ProductController::class, 'store']);
Route::get('/products', [ProductController::class, 'index']);
Route::get('/allProducts', [ProductController::class, 'newIndex']);
Route::get('/viewProduct/{id}', [ProductController::class, 'show']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);
Route::get('/products/latest', [ProductController::class, 'getLatestProducts']);

Route::post('/reserve', [ReservationController::class, 'store']);
Route::post('/adminReserve', [ReservationController::class, 'adminReserve']);
Route::get('/reservations', [ReservationController::class, 'accepted']);
Route::get('/reservations/pending', [ReservationController::class, 'pending']);
Route::get('/reservations/picked_up', [ReservationController::class, 'pickedUp']);
Route::post('/reservations/{id}/send-email', [ReservationController::class, 'sendEmail']);

Route::get('/reservations/total', [ReservationController::class, 'index']);
Route::get('/reservations/counts', [ReservationController::class, 'reservationStatusCounts']);
Route::get('/accounts/created-per-month', [ReservationController::class, 'accountsCreatedPerMonth']);

Route::put('/reservations/{id}/accept', [ReservationController::class, 'accept']);
Route::put('/reservations/{id}/decline', [ReservationController::class, 'decline']);
Route::put('/reservations/{id}/picked_up', [ReservationController::class, 'pickUp']);

Route::get('/reservations/{patientId}', [ReservationController::class, 'getUserReservations']);
Route::delete('/reservations/{reservationId}', [ReservationController::class, 'cancelReservation']);

Route::prefix('patients')->group(function () {

    Route::get('/{patient_id}/history', [HistoryController::class, 'index']);
    Route::post('/{patient_id}/history', [HistoryController::class, 'store']);
    Route::get('/{patient_id}/history/{history_id}', [HistoryController::class, 'show']);
    Route::put('/{patient_id}/history/{history_id}', [HistoryController::class, 'update']);
    Route::delete('/{patient_id}/history/{history_id}', [HistoryController::class, 'destroy']);

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
    Route::get('/{id}', [PatientController::class, 'show']);   
    Route::put('/{patient}', [PatientController::class, 'update']);
    Route::delete('/{patient}', [PatientController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
