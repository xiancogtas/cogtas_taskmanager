<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $status = is_string($status) && in_array($status, ['all', 'pending', 'completed'], true)
            ? $status
            : 'all';
        $search = $request->string('q')->trim()->toString();

        $tasks = Task::query()
            ->when($status !== 'all', fn (Builder $query): Builder => $query->where('status', $status))
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $totalTasks = Task::count();
        $completedTasks = Task::where('status', 'completed')->count();

        return view('tasks.index', [
            'tasks' => $tasks,
            'status' => $status,
            'search' => $search,
            'totalTasks' => $totalTasks,
            'pendingTasks' => Task::where('status', 'pending')->count(),
            'completedTasks' => $completedTasks,
            'completionRate' => $totalTasks === 0 ? 0 : (int) round($completedTasks / $totalTasks * 100),
        ]);
    }

    public function create(): View
    {
        return view('tasks.form');
    }

    public function store(Request $request): RedirectResponse
    {
        Task::create($request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['nullable', 'date'],
        ]));

        return redirect()->route('tasks.index')->with('success', 'Task added to your orbit.');
    }

    public function edit(Task $task): View
    {
        return view('tasks.form', ['task' => $task]);
    }

    public function update(Request $request, Task $task): RedirectResponse
    {
        $task->update($request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['nullable', 'date'],
        ]));

        return redirect()->route('tasks.index')->with('success', 'Task details updated.');
    }

    public function updateStatus(Task $task): RedirectResponse
    {
        $task->update([
            'status' => $task->status === 'pending' ? 'completed' : 'pending',
        ]);

        return back()->with('success', 'Task status updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task removed.');
    }
}
