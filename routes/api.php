<?php

use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

//Topic
Route::get('/topics', [TopicController::class, 'index'])->name('topics');
Route::get('/topics/{topic}', [TopicController::class, 'show'])->name('topics.show');
Route::post('/topics', [TopicController::class, 'store'])->name('topics.store');
Route::put('/topics/{topic}', [TopicController::class, 'update'])->name('topics.update');
Route::delete('/topics/{topic}', [TopicController::class, 'destroy'])->name('topics.destroy');
