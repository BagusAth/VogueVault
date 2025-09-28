<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Home route - redirect to login if not authenticated
Route::get('/', function () {
    return auth()->check() 
        ? (auth()->user()->role === 'admin' 
            ? redirect()->route('admin.dashboard') 
            : redirect()->route('customer.home'))
        : view('Login');
})->name('home');

// Search route
Route::get('/search', [HomeController::class, 'search'])->name('search');

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Protected Routes - Admin Dashboard
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

// Protected Routes - Customer Dashboard/Home
Route::middleware(['auth', 'customer'])->group(function () {
    Route::get('/customer/home', [HomeController::class, 'index'])->name('customer.home');
    Route::get('/customer/dashboard', function () {
        return view('cust.dashboard');
    })->name('customer.dashboard');
});
