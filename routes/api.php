<?php
use App\Models\Task;

Route::get('/api/tasks', function () {
    return Task::select('id', 'title', 'due_date as start')->get();
});
