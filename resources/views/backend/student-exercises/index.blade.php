@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Exercises</h1>
        <p class="mt-2 text-sm text-gray-600">View and complete assigned quizzes, classwork, and homework</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($exercises as $exercise)
            @php
                $submission = $submissions->get($exercise->id);
                $isOverdue = $exercise->due_date && now()->gt($exercise->due_date);
                $isSubmitted = $submission && in_array($submission->status, ['submitted', 'marked']);
            @endphp
            <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            @if($exercise->type == 'quiz')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">Quiz</span>
                            @elseif($exercise->type == 'classwork')
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Classwork</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-orange-100 text-orange-800">Homework</span>
                            @endif
                        </div>
                        @if($submission)
                            {!! $submission->status_badge !!}
                        @endif
                    </div>

                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $exercise->title }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ $exercise->subject->name ?? 'Subject' }}</p>

                    <div class="space-y-2 text-sm text-gray-600 mb-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            {{ $exercise->questions->count() }} questions | {{ $exercise->total_marks }} marks
                        </div>
                        @if($exercise->duration_minutes)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $exercise->duration_minutes }} minutes
                            </div>
                        @endif
                        @if($exercise->due_date)
                            <div class="flex items-center {{ $isOverdue && !$isSubmitted ? 'text-red-600' : '' }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Due: {{ $exercise->due_date->format('M d, Y H:i') }}
                                @if($isOverdue && !$isSubmitted)
                                    <span class="ml-2 text-xs text-red-600">(Overdue)</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if($submission && $submission->status == 'marked' && $exercise->show_results)
                        <div class="bg-green-50 rounded-lg p-3 mb-4">
                            <p class="text-sm text-green-700">
                                Score: <span class="font-bold">{{ $submission->total_score }}/{{ $exercise->total_marks }}</span>
                                ({{ $submission->getPercentageScore() }}%)
                            </p>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    @if($isSubmitted)
                        <a href="{{ route('student.exercises.results', $exercise->id) }}" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            View Results
                        </a>
                    @elseif($submission && $submission->status == 'in_progress')
                        <a href="{{ route('student.exercises.attempt', $exercise->id) }}" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Continue
                        </a>
                    @elseif($isOverdue)
                        <button disabled class="w-full inline-flex items-center justify-center px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Closed
                        </button>
                    @else
                        <a href="{{ route('student.exercises.attempt', $exercise->id) }}" 
                            class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Start Exercise
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12 bg-white rounded-xl shadow-lg">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No exercises assigned</h3>
                    <p class="mt-1 text-sm text-gray-500">Check back later for new assignments.</p>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
