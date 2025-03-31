<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PageController;
// use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\CategoryController;


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

//logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/');
})->name('logout');


// category

//Route::get('/category/{link}', [CategoryController::class, 'view_demo']);

// Route::group(['prefix' => 'category'], function () {
//     Route::get('/', [CategoryController::class, 'index'])-> name('category.index');
//     Route::get('/add', [CategoryController::class, 'add']);
//     Route::post('/add', [CategoryController::class, 'add_post'])-> name('category.add_category');
// });

Route::get('/category', [CategoryController::class, 'index'])->name('category.index');
Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
Route::put('/category/{id}', [CategoryController::class, 'update'])->name('category.update');
Route::delete('/category/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');

//Dashbosrd
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
//category
// Route::middleware(['auth'])->group(function () {
//     Route::resource('category', CategoryController::class);
// });
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