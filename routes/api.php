<?php

use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

//Topic
Route::get('/topics', [TopicController::class, 'index'])->name('topics');
