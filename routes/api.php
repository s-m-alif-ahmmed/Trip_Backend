<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\SystemSetting\SystemSettingController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\SocialLoginController;
use App\Http\Controllers\API\Auth\ProfileUpdateController;
use App\Http\Controllers\API\DynamicPage\DynamicPageController;
use App\Http\Controllers\API\Trip\ProposeTripController;
use App\Http\Controllers\API\Search\SearchTripController;
use App\Http\Controllers\API\Booking\TripBookingController;
use App\Http\Controllers\API\PriceManage\PriceManageController;
use App\Http\Controllers\API\Dashboard\DashboardController;


// Route for System Setting
Route::get('/system-setting', [SystemSettingController::class, 'systemSetting']);
Route::get('/price-manage', [PriceManageController::class, 'index']);

//Social login test routes
Route::post('/social/login',[SocialLoginController::class, 'socialLogin']);
Route::post('/auth/apple/callback',[SocialLoginController::class, 'redirectCallbackApple']);

// Dynamic Pages routes
Route::get('/terms-and-conditions', [DynamicPageController::class, 'terms']);
Route::get('/privacy-policy', [DynamicPageController::class, 'privacy']);

// Search Trip routes
Route::get('/search/trip', [SearchTripController::class, 'search']);
Route::get('/search/trip/show/{id}', [SearchTripController::class, 'show']);

// Booking Trip routes
Route::post('/booking', [TripBookingController::class, 'booking']);
Route::get('/booking/payment-confirmation', [TripBookingController::class, 'paymentConfirmation'])->name('booking.payment-confirmation');

Route::middleware(['guest'])->group(function () {

    //  Authentication routes
    Route::post('login', [LoginController::class, 'login']);
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('resend_otp', [RegisterController::class, 'resend_otp']);
    Route::post('verify_otp', [RegisterController::class, 'verify_otp']);
    Route::post('forgot-password', [RegisterController::class, 'forgot_password']);
    Route::post('forgot-verify-otp', [RegisterController::class, 'forgot_verify_otp']);
    Route::post('reset-password', [RegisterController::class, 'reset_password']);
});

Route::middleware(['auth:sanctum'])->group(function ($router) {
    // common routes
    Route::get('/user-detail', [LoginController::class, 'userDetails']);
    Route::get('/dashboard/overview', [DashboardController::class, 'index']);
    Route::post('/logout', [LoginController::class, 'logout']);

    // profile update routes
    Route::post('/change-password', [ProfileUpdateController::class, 'changePassword']);
    Route::post('/account-delete', [ProfileUpdateController::class, 'accountDelete']);
    Route::post('/profile/update', [ProfileUpdateController::class, 'updateDetails']);

    Route::get('/trips', [ProposeTripController::class, 'index']);
    Route::get('/user-bookings', [ProposeTripController::class, 'myBookings']);
    Route::get('/user-bookings/{id}', [ProposeTripController::class, 'showBooking']);
    Route::get('/trips/{id}', [ProposeTripController::class, 'show']);
    Route::post('/trips', [ProposeTripController::class, 'store']);
    Route::post('/trips/{trip}', [ProposeTripController::class, 'update']);
    Route::delete('/trips/{trip}', [ProposeTripController::class, 'destroy']);

});
