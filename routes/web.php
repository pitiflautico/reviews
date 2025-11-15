<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NetworkController;
use Illuminate\Support\Facades\Route;

// Landing page - redirect to dashboard if authenticated
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});

// Google OAuth routes
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

// Dashboard - show user's networks
Route::get('/dashboard', function () {
    $networks = auth()->user()->networks ?? collect();
    return view('dashboard', compact('networks'));
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Network routes
    Route::resource('networks', NetworkController::class);

    // Network member management routes
    Route::get('networks/{network}/members', [App\Http\Controllers\NetworkMemberController::class, 'index'])
        ->name('networks.members.index');
    Route::get('networks/{network}/members/invite', [App\Http\Controllers\NetworkMemberController::class, 'invite'])
        ->name('networks.members.invite');
    Route::post('networks/{network}/members', [App\Http\Controllers\NetworkMemberController::class, 'store'])
        ->name('networks.members.store');
    Route::patch('networks/{network}/members/{user}/role', [App\Http\Controllers\NetworkMemberController::class, 'updateRole'])
        ->name('networks.members.updateRole');
    Route::delete('networks/{network}/members/{user}', [App\Http\Controllers\NetworkMemberController::class, 'destroy'])
        ->name('networks.members.destroy');

    // Review routes (nested under networks)
    Route::resource('networks.reviews', App\Http\Controllers\ReviewController::class);

    // Review comment routes
    Route::post('networks/{network}/reviews/{review}/comments', [App\Http\Controllers\ReviewCommentController::class, 'store'])
        ->name('networks.reviews.comments.store');
});

require __DIR__.'/auth.php';
