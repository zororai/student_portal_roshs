@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="bg-white rounded-xl shadow-lg p-8 text-center">
        <div class="w-20 h-20 mx-auto bg-yellow-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Submission Received</h1>
        <p class="text-gray-600 mb-6">Your submission for <strong>{{ $exercise->title }}</strong> has been received.</p>

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <p class="text-sm text-gray-500">Submitted on</p>
            <p class="text-lg font-medium text-gray-900">
                {{ $submission->submitted_at ? $submission->submitted_at->format('F d, Y \a\t H:i') : 'N/A' }}
            </p>
        </div>

        <div class="bg-yellow-50 rounded-lg p-4 mb-8">
            <p class="text-yellow-800">
                <strong>Pending Review</strong><br>
                Your teacher is reviewing submissions. Results will be available once marking is complete.
            </p>
        </div>

        <a href="{{ route('student.exercises.index') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Exercises
        </a>
    </div>
</div>
@endsection
