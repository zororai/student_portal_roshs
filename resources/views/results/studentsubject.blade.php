@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Student Add Results</h1>
    
    

    <form action="{{ route('teacher.results.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label for="result_period" class="block text-gray-700 font-bold mb-2">Select Result Period:</label>
            <select name="result_period" id="result_period" class="border rounded px-2 py-1">
                <option value="">Select a Term</option>
                <option value="first">First Term</option>
                <option value="second">Second Term</option>
                <option value="third">Third Term</option>
            </select>
        </div>
        <div class="flex items-center bg-gray-200 rounded-tl rounded-tr mb-2">
            <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-semibold">Subjects</div>
            <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-semibold">Score</div>
            <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-semibold">Comment</div>
            <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-semibold">Grade</div>
        </div>
        <input type="hidden" name="class_id" value="{{ $class->id }}">
        <input type="hidden" name="teacher_id" value="{{ auth()->user()->teacher->id }}">

      
            @foreach($class->subjects as $subject)
                <div class="flex items-center justify-between border border-gray-200 mb-2 p-2">
                    <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium">
                        {{ $subject->name }}
                    </div>
                    <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium">
                        <input type="number" name="results[{{ $student->id }}][{{ $subject->id }}][marks]" required class="border rounded px-2 py-1 w-full">
                    </div>
                    <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium">
                        <input type="text" name="results[{{ $student->id }}][{{ $subject->id }}][comment]" class="border rounded px-2 py-1 w-full">
                    </div>
                    <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium">
                        <input type="text" name="results[{{ $student->id }}][{{ $subject->id }}][mark_grade]" class="border rounded px-2 py-1 w-full">
                    </div>
                </div>
            @endforeach
        

        <button type="submit" class="mt-4 bg-blue-500 text-white rounded px-4 py-2">Save Results</button>
    </form>
</div>
@endsection