

@extends('layouts.app')

@section('content')

    @if($student && $student->class && $student->class->subjects)
    <div class="flex items-center bg-gray-600">
        <div class="w-1/3 text-left text-white py-2 px-4 font-semibold">Code</div>
        <div class="w-1/3 text-left text-white py-2 px-4 font-semibold">Subject</div>
        <div class="w-1/3 text-right text-white py-2 px-4 font-semibold">Action</div>
        
    </div>
    @foreach ($student->class->subjects as $subject)
        <div class="flex items-center justify-between border border-gray-200 -mb-px">
            <div class="w-1/3 text-left text-gray-600 py-2 px-4 font-medium">{{ $subject->subject_code }}</div>
            <div class="w-1/3 text-left text-gray-600 py-2 px-4 font-medium">{{ $subject->name }}</div>
            <div class="w-1/3 text-right text-gray-600 py-2 px-4 font-medium"><a href="{{ route('subject.viewreading', $subject->id) }}">View Reading Materials</a></div>
        
        </div>
    @endforeach
    @else
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <p class="text-gray-500 font-medium">No subjects available</p>
        <p class="text-gray-400 text-sm mt-1">You have not been assigned to a class yet</p>
    </div>
    @endif

    


@endsection



