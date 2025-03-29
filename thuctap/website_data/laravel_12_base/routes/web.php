<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PageController;


// login
Route::get('/', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//Dăng ký
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

// Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
// Route::post('/register', [RegisterController::class, 'register'])->name('register.post');



//Dashbosrd
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

//logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');

//user
Route::middleware(['auth'])->group(function () {
    Route::resource('users', UserController::class);
});
Route::resource('users', UserController::class);

Route::middleware(['auth'])->group(function () {
    Route::resource('posts', PostController::class);
});
Route::resource('posts', PostController::class);

Route::middleware(['auth'])->group(function () {
    Route::resource('pages', PageController::class);
});
Route::resource('pages', PageController::class);