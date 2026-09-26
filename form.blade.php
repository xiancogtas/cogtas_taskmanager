@extends('layouts.app')

@php($isEditing = isset($task))

@section('title', $isEditing ? 'Edit task' : 'New task')
@section('eyebrow', $isEditing ? 'EDIT TASK' : 'NEW TASK')

@section('content')
    <div class="form-page-heading">
        <a class="back-link" href="{{ route('tasks.index') }}"><span aria-hidden="true">&#8592;</span> Back to overview</a>
        <p class="eyebrow muted-eyebrow">{{ $isEditing ? 'REFINE YOUR PLAN' : 'SET A NEW COURSE' }}</p>
        <h1>{{ $isEditing ? 'Edit your task.' : 'A new idea, in orbit.' }}</h1>
        <p class="form-intro">{{ $isEditing ? 'Update the details and keep moving.' : 'Give your next step a name and a place to land.' }}</p>
    </div>

    <form class="task-form" method="POST" action="{{ $isEditing ? route('tasks.update', $task) : route('tasks.store') }}">
        @csrf
        @if ($isEditing)@method('PUT')@endif

        <div class="form-field">
            <label for="title">Task name <span>*</span></label>
            <input id="title" name="title" type="text" maxlength="120" required autofocus value="{{ old('title', $task->title ?? '') }}" placeholder="What needs to get done?">
            @error('title')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-field">
            <label for="description">A few details <span class="optional-label">OPTIONAL</span></label>
            <textarea id="description" name="description" rows="5" maxlength="1000" placeholder="Add a note for future you...">{{ old('description', $task->description ?? '') }}</textarea>
            @error('description')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-field date-field">
            <label for="due_date">Due date <span class="optional-label">OPTIONAL</span></label>
            <input id="due_date" name="due_date" type="date" value="{{ old('due_date', isset($task) && $task->due_date ? $task->due_date->format('Y-m-d') : '') }}">
            @error('due_date')<span class="field-error">{{ $message }}</span>@enderror
        </div>

        <div class="form-actions">
            <a class="button button-quiet" href="{{ route('tasks.index') }}">Cancel</a>
            <button class="button button-primary" type="submit">{{ $isEditing ? 'Save changes' : 'Add task' }} <span aria-hidden="true">&#8594;</span></button>
        </div>
    </form>
@endsection