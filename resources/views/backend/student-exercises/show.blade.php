@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('student.exercises.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Exercises
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center space-x-3">
                        @if($exercise->type == 'quiz')
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-purple-100 text-purple-800">Quiz</span>
                        @elseif($exercise->type == 'classwork')
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">Classwork</span>
                        @else
                            <span class="px-3 py-1 text-sm font-medium rounded-full bg-orange-100 text-orange-800">Homework</span>
                        @endif
                    </div>
                    <h1 class="mt-3 text-2xl font-bold text-gray-900">{{ $exercise->title }}</h1>
                    <p class="text-gray-600">{{ $exercise->subject->name ?? 'Subject' }} | {{ $exercise->teacher->user->name ?? 'Teacher' }}</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-500">Questions</p>
                    <p class="text-xl font-bold text-gray-900">{{ $exercise->questions->count() }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-500">Total Marks</p>
                    <p class="text-xl font-bold text-gray-900">{{ $exercise->total_marks }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-500">Duration</p>
                    <p class="text-xl font-bold text-gray-900">{{ $exercise->duration_minutes ? $exercise->duration_minutes . ' min' : 'No limit' }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-500">Due Date</p>
                    <p class="text-lg font-bold {{ $exercise->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $exercise->due_date ? $exercise->due_date->format('M d') : 'None' }}
                    </p>
                </div>
            </div>

            @if($exercise->instructions)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Instructions</h3>
                    <div class="bg-blue-50 rounded-lg p-4 text-blue-800">
                        {!! nl2br(e($exercise->instructions)) !!}
                    </div>
                </div>
            @endif

            @if($exercise->isOverdue() && (!$submission || !in_array($submission->status, ['submitted', 'marked'])))
                <div class="bg-red-50 rounded-lg p-4 text-center">
                    <svg class="mx-auto h-12 w-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="mt-2 text-red-700 font-medium">This exercise is past its due date</p>
                    <p class="text-sm text-red-600">Submissions are no longer accepted</p>
                </div>
            @else
                <div class="text-center">
                    <a href="{{ route('student.exercises.attempt', $exercise->id) }}" 
                        class="inline-flex items-center px-8 py-4 bg-blue-600 text-white text-lg font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Start Exercise
                    </a>
                    <p class="mt-4 text-sm text-gray-500">
                        Once you start, make sure to submit before the due date.
                        @if($exercise->duration_minutes)
                            <br>You will have {{ $exercise->duration_minutes }} minutes to complete this exercise.
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
