<?php

use App\Http\Controllers\Api\Admin\AdminBookingController;
use App\Http\Controllers\Api\Admin\AdminClientController;
use App\Http\Controllers\Api\Admin\AdminContractController;
use App\Http\Controllers\Api\Admin\AdminDashboardController;
use App\Http\Controllers\Api\Admin\AdminDepositController;
use App\Http\Controllers\Api\Admin\AdminInvoiceController;
use App\Http\Controllers\Api\Admin\AdminLandingController;
use App\Http\Controllers\Api\Admin\AdminMenuCategoryController;
use App\Http\Controllers\Api\Admin\AdminMenuItemController;
use App\Http\Controllers\Api\Admin\AdminPaymentController;
use App\Http\Controllers\Api\Admin\AdminReportController;
use App\Http\Controllers\Api\Admin\AdminServiceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\DepositController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\LandingController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/landing', [LandingController::class, 'index']);
Route::get('/menu/public', [MenuController::class, 'publicMenu']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::put('/auth/profile', [AuthController::class, 'updateProfile']);

    // Contract
    Route::get('/contracts/active', [ContractController::class, 'getActive']);
    Route::post('/contracts/agree', [ContractController::class, 'agree']);
    Route::get('/contracts/download', [ContractController::class, 'download']);

    // Verified client routes
    Route::middleware('verified.client')->group(function () {
        Route::get('/menu', [MenuController::class, 'index']);
        Route::get('/services', [ServiceController::class, 'index']);

        Route::apiResource('/bookings', BookingController::class)->only(['index', 'show', 'store']);

        Route::get('/invoices', [InvoiceController::class, 'clientIndex']);
        Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
        Route::get('/invoices/{invoice}/download', [InvoiceController::class, 'download']);

        Route::get('/payments', [PaymentController::class, 'clientIndex']);
        Route::get('/deposit', [DepositController::class, 'show']);
    });

    // Admin routes
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index']);

        // Clients
        Route::get('/clients', [AdminClientController::class, 'index']);
        Route::get('/clients/{user}', [AdminClientController::class, 'show']);
        Route::post('/clients/{user}/approve', [AdminClientController::class, 'approve']);
        Route::post('/clients/{user}/reject', [AdminClientController::class, 'reject']);
        Route::put('/clients/{user}', [AdminClientController::class, 'update']);

        // Contracts
        Route::get('/contracts', [AdminContractController::class, 'index']);
        Route::post('/contracts', [AdminContractController::class, 'upload']);
        Route::put('/contracts/{contract}/activate', [AdminContractController::class, 'activate']);

        // Menu
        Route::apiResource('/menu-categories', AdminMenuCategoryController::class);
        Route::apiResource('/menu-items', AdminMenuItemController::class);
        Route::apiResource('/services', AdminServiceController::class);

        // Bookings
        Route::get('/bookings', [AdminBookingController::class, 'index']);
        Route::get('/bookings/{booking}', [AdminBookingController::class, 'show']);
        Route::post('/bookings/{booking}/approve', [AdminBookingController::class, 'approve']);
        Route::post('/bookings/{booking}/reject', [AdminBookingController::class, 'reject']);
        Route::put('/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus']);

        // Invoices
        Route::get('/invoices', [AdminInvoiceController::class, 'index']);
        Route::get('/invoices/{invoice}', [AdminInvoiceController::class, 'show']);
        Route::get('/invoices/{invoice}/download', [AdminInvoiceController::class, 'download']);

        // Payments
        Route::get('/payments', [AdminPaymentController::class, 'index']);
        Route::post('/payments', [AdminPaymentController::class, 'store']);
        Route::delete('/payments/{payment}', [AdminPaymentController::class, 'destroy']);

        // Deposits
        Route::get('/deposits', [AdminDepositController::class, 'index']);
        Route::post('/deposits', [AdminDepositController::class, 'store']);

        // Reports
        Route::get('/reports/clients', [AdminReportController::class, 'clients']);
        Route::get('/reports/invoices', [AdminReportController::class, 'invoices']);

        // Landing settings
        Route::get('/landing-settings', [AdminLandingController::class, 'index']);
        Route::post('/landing-settings', [AdminLandingController::class, 'update']);
        Route::post('/landing-settings/upload-image', [AdminLandingController::class, 'uploadImage']);
    });
});
