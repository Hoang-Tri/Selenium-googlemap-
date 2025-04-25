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

//user
Route::middleware(['auth'])->group(function () {
    Route::resource('users', UserController::class);
});
Route::resource('users', UserController::class);

//Số lượng user
Route::get('/users-reviews', [UserReviewController::class, 'index'])->name('usersreviews.index');

// googlemaps
Route::get('/google', [UserGoogleMapsController::class, 'index']);

//location
Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');

// Route::get('/nguoidung/dashboard', [LocationController::class, 'showChart'])
//     ->name('nguoidung.dashboard')->middleware('auth');
//upload file

Route::get('/upload', function () {
    return view('upload');
});

Route::get('/upload', [FileUploadController::class, 'index'])->name('upload.index');

Route::get('/download/{location_id}', [App\Http\Controllers\FileUploadController::class, 'download']);
Route::get('/review', [App\Http\Controllers\FileUploadController::class, 'index']);

// Route::get('/nguoidung/dashboard', [NguoidungController::class, 'index']);

Route::get('/nguoidung/dashboard', [NguoidungController::class, 'showChart1'])->name('nguoidung.dashboard');
Route::get('/nguoidung/show/{location_id}', [NguoidungController::class, 'showChart1'])->name('nguoidung.showChart1');

Route::get('/nguoidung/chart/{id}', [NguoidungController::class, 'chart'])->name('nguoidung.chart');

Route::get('/nguoidung/sosanh', [NguoidungController::class, 'showSoSanh'])->name('nguoidung.sosanh');

Route::get('/sosanh/{id1}/{id2}', [\NguoidungController::class, 'soSanhPlace'])->name('sosanh.place');

// cập nhật user
Route::put('/user/update/{id}', [NguoidungController::class, 'update'])->name('user.update');
// lưu history
Route::post('/save-history', [NguoidungController::class, 'saveHistory'])->name('user.saveHistory');
// xóa history
Route::post('/nguoidung/clear-history', [NguoidungController::class, 'clearHistory'])->name('nguoidung.clearHistory');

// cấu hình api-key
Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
Route::post('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

