

@extends('layouts.app')

@section('content')

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

    


@endsection



