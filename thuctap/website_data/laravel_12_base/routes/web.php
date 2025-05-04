<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Models\User;
use App\Models\Location;
use App\Models\UserReview;
use App\Http\Controllers\UserGoogleMapsController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\UserReviewController;
use App\Http\Controllers\NguoidungController;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\DataExportController;

// Hiển thị form đăng nhập
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Xử lý đăng nhập
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// User dashboard (yêu cầu xác thực)
Route::get('/nguoidung/dashboard', function () {
    return view('nguoidung'); // Đây là file welcome.blade.php
})->middleware('auth')->name('nguoidung.dashboard');

// Admin dashboard 
Route::get('/dashboard', function () {
    $userCount = \App\Models\User::all()->count(); 
    $locationCount = \App\Models\Location::count();
    $usersreviewCount = \App\Models\UserReview::count();
    $locationsReviews = \App\Models\Location::withCount('userReviews')->get();
    return view('dashboard', compact('userCount','locationCount', 'usersreviewCount', 'locationsReviews'));
})->middleware('auth')->name('admin.dashboard');

// Register
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

Route::middleware(['auth'])->group(function () {
    // User resource routes
    Route::resource('users', UserController::class);

    // Số lượng user
    Route::get('/users-reviews', [UserReviewController::class, 'index'])->name('usersreviews.index');

    // Google Maps
    Route::get('/google', [UserGoogleMapsController::class, 'index']);

    // Location
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');

    // Upload file
    Route::get('/upload', [FileUploadController::class, 'index'])->name('upload.index');

    // Download file
    Route::get('/download/{location_id}', [App\Http\Controllers\FileUploadController::class, 'download']);
    Route::get('/review', [App\Http\Controllers\FileUploadController::class, 'index']);

    // Cấu hình api-key
    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

    // Tìm kiếm places
    Route::get('/places/search', [PlaceController::class, 'search']);

    // Xuất dữ liệu
    Route::get('/download-all-data', [DataExportController::class, 'download']);
});

// Các route cần đăng nhập
Route::middleware(['auth'])->group(function () {
    Route::get('/nguoidung/dashboard', [NguoidungController::class, 'showChart1'])->name('nguoidung.dashboard');
    Route::get('/nguoidung/show/{location_id}', [NguoidungController::class, 'showChart1'])->name('nguoidung.showChart1');
    Route::get('/nguoidung/chart/{id}', [NguoidungController::class, 'chart'])->name('nguoidung.chart');
    Route::get('/nguoidung/sosanh', [NguoidungController::class, 'showSoSanh'])->name('nguoidung.sosanh');
    Route::get('/sosanh/{id1}/{id2}', [NguoidungController::class, 'soSanhPlace'])->name('sosanh.place');
    Route::get('/nguoidung/chart-sosanh/{id}', [NguoidungController::class, 'chartSoSanh'])->name('nguoidung.chart_sosanh');

    // Cập nhật user
    Route::put('/user/update/{id}', [NguoidungController::class, 'update'])->name('user.update');

    // Lưu và xóa lịch sử
    Route::post('/save-history', [NguoidungController::class, 'saveHistory'])->name('user.saveHistory');
    Route::post('/nguoidung/clear-history', [NguoidungController::class, 'clearHistory'])->name('nguoidung.clearHistory');
});
