<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CastController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\CastMovieController;
use App\Http\Controllers\ReviewController;

Route::prefix('v1')->group(function () {

    // Public
    Route::get('/cast-movie', [CastMovieController::class, 'index']); 
    Route::get('/cast-movie/{id}', [CastMovieController::class, 'show']);
    Route::get('/movie', [MovieController::class, 'index']);
    Route::get('/movie/{id}', [MovieController::class, 'show']);
    Route::get('/genre', [GenreController::class, 'index']);
    Route::get('/genre/{id}', [GenreController::class, 'show']);
    Route::get('/cast', [CastController::class, 'index']);
    Route::get('/cast/{id}', [CastController::class, 'show']);

    // Auth
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/generate-otp-code', [AuthController::class, 'generateOtpCode']);
    Route::post('/auth/verification-email', [AuthController::class, 'verificationEmail']);

    // Butuh token
    Route::middleware(['auth.api'])->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::put('/user', [AuthController::class, 'update']);
        
        // Admin only
        Route::middleware('admin')->group(function () {
            Route::post('/cast-movie', [CastMovieController::class, 'store']);
            Route::post('/cast-movie/{id}', [CastMovieController::class, 'update'])->name('castmovie.update')->defaults('_method','PUT');
            Route::post('/cast-movie/{id}/delete', [CastMovieController::class, 'destroy'])->name('castmovie.destroy')->defaults('_method','DELETE');
            Route::post('/movie', [MovieController::class, 'store']);
            Route::post('/movie/{id}', [MovieController::class, 'update'])->name('movie.update')->defaults('_method','PUT');
            Route::post('/movie/{id}/delete', [MovieController::class, 'destroy'])->name('movie.destroy')->defaults('_method','DELETE');
            Route::post('/cast', [CastController::class, 'store']);
            Route::post('/cast/{id}', [CastController::class, 'update'])->name('cast.update')->defaults('_method','PUT');
            Route::post('/cast/{id}/delete', [CastController::class, 'destroy'])->name('cast.destroy')->defaults('_method','DELETE');
            Route::post('/genre', [GenreController::class, 'store']);
            Route::post('/genre/{id}', [GenreController::class, 'update'])->name('genre.update')->defaults('_method','PUT');
            Route::post('/genre/{id}/delete', [GenreController::class, 'destroy'])->name('genre.destroy')->defaults('_method','DELETE');
            
            // Role
            Route::get('/role', [RoleController::class, 'index']);
            Route::post('/role', [RoleController::class, 'store']);
            Route::get('/role/{id}', [RoleController::class, 'show']);
            Route::put('/role/{id}', [RoleController::class, 'update']);
            Route::delete('/role/{id}', [RoleController::class, 'destroy']);
        });

        // Verified user
        Route::middleware('verifikasi')->group(function () {
            Route::post('/review', [ReviewController::class, 'store']);
        });
    });
});
