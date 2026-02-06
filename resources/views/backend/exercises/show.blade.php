@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('exercises.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Exercises
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center space-x-3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $exercise->title }}</h1>
                        {!! $exercise->status_badge !!}
                    </div>
                    <p class="mt-2 text-gray-600">{{ $exercise->class->class_name }} | {{ $exercise->subject->subject_name }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <form action="{{ route('exercises.toggle-publish', $exercise->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 {{ $exercise->is_published ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} rounded-lg transition-colors">
                            {{ $exercise->is_published ? 'Unpublish' : 'Publish' }}
                        </button>
                    </form>
                    <a href="{{ route('exercises.edit', $exercise->id) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Edit
                    </a>
                    <a href="{{ route('exercises.questions.edit', $exercise->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Manage Questions
                    </a>
                </div>
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Type</p>
                    <p class="text-lg font-semibold text-gray-900">{{ ucfirst($exercise->type) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Total Marks</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $exercise->total_marks }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Questions</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $exercise->questions->count() }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-500">Duration</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $exercise->duration_minutes ? $exercise->duration_minutes . ' min' : 'No limit' }}</p>
                </div>
            </div>

            @if($exercise->instructions)
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Instructions</h3>
                    <div class="bg-gray-50 rounded-lg p-4 text-gray-700">
                        {!! nl2br(e($exercise->instructions)) !!}
                    </div>
                </div>
            @endif

            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Due Date</h3>
                <p class="text-gray-700">{{ $exercise->due_date ? $exercise->due_date->format('F d, Y \a\t H:i') : 'No deadline set' }}</p>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Submission Statistics</h3>
                    <a href="{{ route('exercises.submissions', $exercise->id) }}" class="text-blue-600 hover:text-blue-800">
                        View All Submissions →
                    </a>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm text-blue-600">Total Students</p>
                        <p class="text-2xl font-bold text-blue-700">{{ $submissionStats['total_students'] }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-sm text-green-600">Submitted</p>
                        <p class="text-2xl font-bold text-green-700">{{ $submissionStats['submitted'] }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-sm text-purple-600">Marked</p>
                        <p class="text-2xl font-bold text-purple-700">{{ $submissionStats['marked'] }}</p>
                    </div>
                </div>
            </div>

            @if($exercise->questions->count() > 0)
                <div class="border-t border-gray-200 pt-6 mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Questions Preview</h3>
                        <form action="{{ route('exercises.toggle-results', $exercise->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm {{ $exercise->show_results ? 'text-green-600' : 'text-gray-500' }} hover:underline">
                                Results {{ $exercise->show_results ? 'visible' : 'hidden' }} to students
                            </button>
                        </form>
                    </div>
                    <div class="space-y-4">
                        @foreach($exercise->questions as $index => $question)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Question {{ $index + 1 }}</span>
                                        <span class="text-xs text-gray-400 ml-2">({{ $question->marks }} marks)</span>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-200 text-gray-600">
                                        {{ $question->getQuestionTypeLabel() }}
                                    </span>
                                </div>
                                <p class="mt-2 text-gray-900">{{ $question->question_text }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
