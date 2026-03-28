<?php

use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\UserController;
use App\Http\Controllers\Web\Backend\PriceManageController;
use App\Http\Controllers\Web\Backend\TripManageController;
use App\Http\Controllers\Web\Backend\TripBookingController;
use Illuminate\Support\Facades\Route;

// Route for Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Route for Users Page
Route::get('/user', [UserController::class, 'index'])->name('user.index');
Route::get('/user/create', [UserController::class, 'create'])->name('user.create');
Route::post('/user/store', [UserController::class, 'store'])->name('user.store');
Route::get('/user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
Route::patch('/user/update/{id}', [UserController::class, 'update'])->name('user.update');
Route::delete('/user/destroy/{id}', [UserController::class, 'destroy'])->name('user.destroy');

// Route for Price Manage Page
Route::get('/price-manage', [PriceManageController::class, 'index'])->name('price-manage.index');
Route::get('/price-manage/create', [PriceManageController::class, 'create'])->name('price-manage.create');
Route::post('/price-manage/store', [PriceManageController::class, 'store'])->name('price-manage.store');
Route::get('/price-manage/edit/{id}', [PriceManageController::class, 'edit'])->name('price-manage.edit');
Route::patch('/price-manage/update/{id}', [PriceManageController::class, 'update'])->name('price-manage.update');

// Route for Trip Manage Page
Route::get('/trip', [TripManageController::class, 'index'])->name('trip.index');
Route::get('/trip/show/{id}', [TripManageController::class, 'show'])->name('trip.show');
Route::get('/trip/edit/{id}', [TripManageController::class, 'edit'])->name('trip.edit');
Route::patch('/trip/update/{id}', [TripManageController::class, 'update'])->name('trip.update');
Route::post('/trip/status/{id}', [TripManageController::class, 'status'])->name('trip.status');
Route::delete('/trip/destroy/{id}', [TripManageController::class, 'destroy'])->name('trip.destroy');

// Route for Trip Booking Page
Route::get('/trip-booking', [TripBookingController::class, 'index'])->name('trip-booking.index');
Route::get('/trip-booking/pickup', [TripBookingController::class, 'pickupIndex'])->name('trip-booking.pickup.index');
Route::get('/trip-booking/show/{id}', [TripBookingController::class, 'show'])->name('trip-booking.show');
Route::post('/trip-booking/status/{id}', [TripBookingController::class, 'status'])->name('trip-booking.status');
Route::delete('/trip-booking/destroy/{id}', [TripBookingController::class, 'destroy'])->name('trip-booking.destroy');

