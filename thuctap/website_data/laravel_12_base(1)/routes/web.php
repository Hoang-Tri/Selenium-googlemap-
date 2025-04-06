<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Models\Category;
use App\Http\Controllers\UserGoogleMapsController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\UserReviewController;

// Hiển thị trang login
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/dashboard', function () {
    // Trang dashboard
    return view('dashboard');
})->name('dashboard');

// Xử lý đăng nhập khi người dùng gửi form
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Register
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// Logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

// Dashboard (No auth required)
Route::get('/dashboard', function () {
    $userCount = \App\Models\User::count(); // Đếm số lượng user
    $locationCount = \App\Models\Location::count();
    $usersreviewCount = \App\Models\UserReview::count();
    return view('dashboard', compact('userCount','locationCount', 'usersreviewCount'));
})->name('dashboard');

//user
Route::middleware(['auth'])->group(function () {
    Route::resource('users', UserController::class);
});
Route::resource('users', UserController::class);

//Số lượng user
Route::get('/user-count', [UserController::class, 'count']);

// googlemaps
Route::get('/google', [UserGoogleMapsController::class, 'index']);

//location
Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');

//user-review
Route::get('/users-reviews', [UserReviewController::class, 'index'])->name('usersreviews.index');
//upload file
Route::get('/upload', [FileUploadController::class, 'index'])->name('upload.index'); 
Route::post('/upload', [FileUploadController::class, 'upload'])->name('upload.file');