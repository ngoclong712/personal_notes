<?php

use App\Http\Controllers\DeadlineController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TopicController;
use Illuminate\Support\Facades\Route;

//Topic
Route::get('/topics', [TopicController::class, 'index'])->name('topics');
Route::get('/topics/{topic}', [TopicController::class, 'show'])->name('topics.show');
Route::post('/topics', [TopicController::class, 'store'])->name('topics.store');
Route::put('/topics/{topic}', [TopicController::class, 'update'])->name('topics.update');
Route::delete('/topics/{topic}', [TopicController::class, 'destroy'])->name('topics.destroy');
Route::post('/topics/import', [TopicController::class, 'import'])->name('topics.import');

Route::get('/notes', [NoteController::class, 'index'])->name('notes');
Route::get('/notes/{note}', [NoteController::class, 'show'])->name('notes.show');
Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
Route::put('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
Route::post('/notes/import', [NoteController::class, 'import'])->name('notes.import');

Route::get('/deadlines', [DeadlineController::class, 'index'])->name('deadlines');
Route::get('/deadlines/{id}', [DeadlineController::class, 'show'])->name('deadlines.show');
Route::post('/deadlines', [DeadlineController::class, 'store'])->name('deadlines.store');
Route::put('/deadlines/{id}', [DeadlineController::class, 'update'])->name('deadlines.update');
Route::delete('/deadlines/{id}', [DeadlineController::class, 'destroy'])->name('deadlines.destroy');
