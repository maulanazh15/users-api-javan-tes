<?php

use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum')->name('current.user');

Route::post('/login', [UserController::class, 'login'])->name('users.login');

Route::prefix('users')->group(function () {
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
});
