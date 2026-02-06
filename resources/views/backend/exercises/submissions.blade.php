@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('exercises.show', $exercise->id) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Exercise
        </a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900">Submissions: {{ $exercise->title }}</h1>
        <p class="text-gray-600">{{ $exercise->class->class_name }} | {{ $exercise->subject->subject_name }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500">{{ $students->count() }} students in class</span>
            <span class="text-sm text-gray-500">|</span>
            <span class="text-sm text-green-600">{{ $submissions->whereIn('status', ['submitted', 'marked'])->count() }} submitted</span>
        </div>
        @if($exercise->type == 'quiz')
            <form action="{{ route('exercises.auto-mark', $exercise->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    Auto-Mark MCQ Questions
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Student</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Submitted At</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase">Score</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($students as $student)
                    @php
                        $submission = $submissions->get($student->id);
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-medium">
                                    {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900">{{ $student->user->name ?? 'Unknown' }}</p>
                                    <p class="text-sm text-gray-500">{{ $student->roll_number }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($submission)
                                {!! $submission->status_badge !!}
                            @else
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Not Started</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $submission && $submission->submitted_at ? $submission->submitted_at->format('M d, Y H:i') : '-' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($submission && $submission->status == 'marked')
                                <span class="font-semibold text-gray-900">{{ $submission->total_score }}/{{ $exercise->total_marks }}</span>
                                <span class="text-sm text-gray-500">({{ $submission->getPercentageScore() }}%)</span>
                            @elseif($submission && $submission->total_score !== null)
                                <span class="text-gray-600">{{ $submission->total_score }}/{{ $exercise->total_marks }}</span>
                                <span class="text-xs text-yellow-600">(partial)</span>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($submission && in_array($submission->status, ['submitted', 'marked']))
                                <a href="{{ route('exercises.submissions.mark', [$exercise->id, $submission->id]) }}" 
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                    </svg>
                                    {{ $submission->status == 'marked' ? 'Review' : 'Mark' }}
                                </a>
                            @elseif($submission && $submission->status == 'in_progress')
                                <span class="text-sm text-yellow-600">In Progress</span>
                            @else
                                <span class="text-sm text-gray-400">No submission</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
