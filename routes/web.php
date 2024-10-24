<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

//livewire Routes

Route::middleware('auth')->group(function () {
    Route::get('/chat',\App\Livewire\Chat\Index::class)->name('chat.index');
    Route::get('/chat/{query}',\App\Livewire\Chat\Chat::class)->name('chat');
    Route::get('/users',\App\Livewire\Users::class)->name('users');
});




