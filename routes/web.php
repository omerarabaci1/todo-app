<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get(uri: '/', action: [TaskController::class, 'index']);
Route::post(uri: '/tasks', action: [TaskController::class, 'store'])->name('tasks.store');
Route::delete(uri: '/tasks/{id}', action: [TaskController::class, 'destroy']);
Route::patch(uri: '/tasks/{id}/toggle', action: [TaskController::class, 'toggleComplete'])->name('tasks.toggle');
Route::get(uri: '/calendar', action: fn(): View => view('calendar'));

// API rotası - takvim için görevleri JSON formatında döndür
Route::get('/api/tasks', [TaskController::class, 'getTasksJson']);