<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use Inertia\Inertia;

use App\Models\Kart;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/home', function () {
    return Inertia::render('Home', [
        'karts' => Kart::all()
    ]);
})->middleware(['auth', 'verified'])->name('home');

Route::get('/about', function () {
    return Inertia::render('About', [
        'title' => 'Welcom to page about us!',
        'content' => 'This text frob backend Laravel.'
    ]);
})->middleware(['auth'])->name('about');

Route::middleware('auth')->group(function () {
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/karts/{kart}/booked-times', [BookingController::class, 'bookedTimes'])->name('karts.booked-times');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
