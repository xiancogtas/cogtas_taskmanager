@extends('layouts.app')

@section('title', 'Your tasks')
@section('eyebrow', 'TASK BOARD')

@section('content')
    <section class="dashboard-hero">
        <div class="hero-copy">
            <div class="eyebrow"><span class="eyebrow-star">+</span> YOUR PERSONAL TASK BOARD</div>
            <h1>Make space<br>for <em>what matters.</em></h1>
            <p>{{ now()->format('l, F j') }} <span class="hero-divider">/</span> One step at a time.</p>
        </div>
        <div class="hero-orbit" aria-hidden="true"><span class="orbit-ring ring-one"></span><span class="orbit-ring ring-two"></span><span class="orbit-planet"></span><span class="orbit-spark spark-one">+</span><span class="orbit-spark spark-two">+</span></div>
        <div class="hero-caption"><span class="live-dot"></span> YOUR DAY, IN ORBIT</div>
    </section>

    <div class="page-heading">
        <div><p class="eyebrow muted-eyebrow">OVERVIEW</p><h2>Today at a glance</h2></div>
        <a class="button button-primary" href="{{ route('tasks.create') }}"><span class="button-plus" aria-hidden="true">+</span> Add a task</a>
    </div>

    <section class="stats-grid" aria-label="Task summary">
        <article class="stat-card stat-total"><div class="stat-top"><span>ALL TASKS</span><span class="stat-icon">01</span></div><strong>{{ $totalTasks }}</strong><span class="stat-foot">Across your whole orbit</span></article>
        <article class="stat-card stat-pending"><div class="stat-top"><span>IN PROGRESS</span><span class="status-dot pending-dot"></span></div><strong>{{ $pendingTasks }}</strong><span class="stat-foot">Ready for your focus</span></article>
        <article class="stat-card stat-complete"><div class="stat-top"><span>COMPLETED</span><span class="status-dot complete-dot"></span></div><strong>{{ $completedTasks }}</strong><span class="stat-foot">{{ $completionRate }}% of your tasks</span><div class="progress-track"><span style="width: {{ $completionRate }}%"></span></div></article>
    </section>

    <section class="task-section">
        <div class="task-section-heading">
            <div><p class="eyebrow muted-eyebrow">YOUR LIST</p><h2>Task constellation <span class="count-pill">{{ $tasks->total() }}</span></h2></div>
            <form class="search-form" method="GET" action="{{ route('tasks.index') }}">
                @if ($status !== 'all')<input type="hidden" name="status" value="{{ $status }}">@endif
                <label class="sr-only" for="task-search">Search tasks</label>
                <svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="8.5" cy="8.5" r="5.5"/><path d="m13 13 4 4"/></svg>
                <input id="task-search" type="search" name="q" value="{{ $search }}" placeholder="Find a task">
            </form>
        </div>

        <div class="task-toolbar">
            <nav class="filter-tabs" aria-label="Filter tasks">
                <a class="filter-tab {{ $status === 'all' ? 'is-selected' : '' }}" href="{{ route('tasks.index') }}">All <span>{{ $totalTasks }}</span></a>
                <a class="filter-tab {{ $status === 'pending' ? 'is-selected' : '' }}" href="{{ route('tasks.index', ['status' => 'pending']) }}">In progress <span>{{ $pendingTasks }}</span></a>
                <a class="filter-tab {{ $status === 'completed' ? 'is-selected' : '' }}" href="{{ route('tasks.index', ['status' => 'completed']) }}">Completed <span>{{ $completedTasks }}</span></a>
            </nav>
            <span class="sort-caption">LATEST FIRST <span aria-hidden="true">&#8595;</span></span>
        </div>

        <div class="task-list">
            @forelse ($tasks as $task)
                <article class="task-row {{ $task->status === 'completed' ? 'is-completed' : '' }}">
                    <form class="task-check-form" method="POST" action="{{ route('tasks.status', $task) }}">
                        @csrf
                        @method('PATCH')
                        <button class="task-check {{ $task->status === 'completed' ? 'is-checked' : '' }}" type="submit" aria-label="{{ $task->status === 'pending' ? 'Mark complete' : 'Reopen task' }}: {{ $task->title }}" title="{{ $task->status === 'pending' ? 'Mark complete' : 'Reopen task' }}">
                            @if ($task->status === 'completed')<svg viewBox="0 0 20 20" aria-hidden="true"><path d="m5 10 3.2 3.2L15 6.5"/></svg>@endif
                        </button>
                    </form>
                    <div class="task-details">
                        <div class="task-title-line"><h3>{{ $task->title }}</h3><span class="task-status {{ $task->status }}">{{ $task->status === 'pending' ? 'In progress' : 'Completed' }}</span></div>
                        @if ($task->description)<p>{{ $task->description }}</p>@endif
                        <div class="task-meta">
                            @if ($task->due_date)<span class="due-date {{ $task->due_date->isPast() && ! $task->due_date->isToday() && $task->status === 'pending' ? 'is-overdue' : '' }}"><svg viewBox="0 0 20 20" aria-hidden="true"><rect x="3" y="5" width="14" height="12" rx="2"/><path d="M6.5 3v4M13.5 3v4M3 9h14"/></svg>{{ $task->due_date->isToday() ? 'Due today' : $task->due_date->format('M j, Y') }}</span>@endif
                            <span>Added {{ $task->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                    <div class="task-actions">
                        <a class="icon-button" href="{{ route('tasks.edit', $task) }}" aria-label="Edit {{ $task->title }}" title="Edit task"><svg viewBox="0 0 20 20" aria-hidden="true"><path d="m12.8 4.2 3 3M4 16l3.8-.8L16 7a2.1 2.1 0 0 0-3-3l-8.2 8.2L4 16Z"/></svg></a>
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Remove this task?')">
                            @csrf
                            @method('DELETE')
                            <button class="icon-button delete-button" type="submit" aria-label="Delete {{ $task->title }}" title="Delete task"><svg viewBox="0 0 20 20" aria-hidden="true"><path d="M4.5 6h11M8 6V4h4v2m2 0-.7 10H6.7L6 6m2.8 3v4m2.4-4v4"/></svg></button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <span class="empty-orbit" aria-hidden="true"><span></span></span>
                    <h3>{{ $search !== '' ? 'Nothing found out there.' : 'A little quiet out here.' }}</h3>
                    <p>{{ $search !== '' ? 'Try another search or clear your filters.' : 'Add a task and give your next idea a place to land.' }}</p>
                    @if ($search === '')<a class="button button-secondary" href="{{ route('tasks.create') }}">Create your first task <span aria-hidden="true">&#8594;</span></a>@endif
                </div>
            @endforelse
        </div>

        @if ($tasks->hasPages())<div class="pagination-wrap">{{ $tasks->links() }}</div>@endif
    </section>
@endsection