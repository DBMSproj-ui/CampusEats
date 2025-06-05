<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

// Routes accessible only to GUEST (unauthenticated) users
Route::middleware('guest')->group(function () {

    // Show the registration form
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    // Handle submitted registration data
    Route::post('register', [RegisteredUserController::class, 'store']);

    // Show the login form
    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    // Handle login credentials
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Show the 'forgot password' form
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    // Handle submission to send password reset link via email
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // Show reset password form (user comes here from email link)
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    // Handle setting a new password
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Routes accessible only to AUTHENTICATED users
Route::middleware('auth')->group(function () {

    // Show email verification prompt if user hasn't verified yet
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    // Handle email verification when user clicks the verification link
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1']) // Link must be signed and rate-limited to 6 requests/min
        ->name('verification.verify');

    // Resend the email verification link (rate-limited)
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Show confirm password form (used before sensitive actions like account deletion)
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    // Handle password confirmation
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    // Handle password update for logged-in user
    Route::put('password', [PasswordController::class, 'update'])
        ->name('password.update');

    // Handle logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
