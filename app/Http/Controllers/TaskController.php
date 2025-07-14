<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $completedTasks = Task::where('completed', true)->get();
        $pendingTasks = Task::where('completed', false)->get();
        return view('tasks.index', compact('completedTasks', 'pendingTasks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'due_date' => 'nullable|date',
        ]);

        Task::create([
            'title' => $request->title,
            'due_date' => $request->due_date,
        ]);

        return redirect('/')->with('success', 'Görev başarıyla eklendi!');
    }

    public function destroy($id)
    {
        Task::destroy($id);
        return redirect('/')->with('success', 'Görev başarıyla silindi!');
    }

    public function toggleComplete($id)
    {
        $task = Task::findOrFail($id);
        $task->completed = !$task->completed;
        $task->save();

        return redirect('/')->with('success', 'Görev durumu güncellendi!');
    }

    // API için görevleri JSON formatında döndür
    public function getTasksJson()
    {
        $tasks = Task::all();
        
        $events = $tasks->map(function($task) {
            return [
                'id' => $task->id,
                'title' => $task->title,
                'start' => $task->due_date, // due_date'i start olarak kullan
                'backgroundColor' => $task->completed ? '#28a745' : '#007bff', // Tamamlanmış yeşil, bekleyen mavi
                'borderColor' => $task->completed ? '#28a745' : '#007bff',
                'textColor' => '#ffffff'
            ];
        });

        return response()->json($events);
    }
}