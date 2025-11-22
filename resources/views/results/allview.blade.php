@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Student Result</h1>
    
    <div class="flex items-center bg-gray-200 rounded-tl rounded-tr mb-2">
        <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-semibold">Subjects</div>
        <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-semibold">Score</div>
        <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-semibold">Comment</div>
        <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-semibold">Grade</div>
        <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-semibold">Actions</div> <!-- Added Actions column -->
    </div>

    @forelse($studentResults as $studentResult)
        <div class="flex items-center justify-between border border-gray-200 mb-2 p-2">
            <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium">
                {{ $studentResult->subject->name }}  
            </div>
            <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium">
                {{ $studentResult->marks }}
            </div>
            <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium">
                {{ $studentResult->comment ?? 'N/A' }}
            </div>
            <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium">
                {{ $studentResult->mark_grade ?? 'N/A' }}
            </div>
            <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium"> <!-- Actions column -->
                <a href="{{ route('results.edit', $studentResult->subject_id ) }}" class="text-blue-500 hover:underline">Edit</a>
            </div>
            <form action="{{ route('results.delete', $studentResult->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-4 py-1 rounded">Delete</button>
            </form>
        </div>
    @empty
        <div class="flex items-center justify-between border border-gray-200 mb-2 p-2">
            <div class="w-1/4 text-left text-red-500 py-2 px-4 font-medium">
                Not Entered
            </div>
            <div class="w-1/4 text-left text-red-500 py-2 px-4 font-medium">
                Not Entered
            </div>
            <div class="w-1/4 text-left text-red-500 py-2 px-4 font-medium">
                Not Entered
            </div>
            <div class="w-1/4 text-left text-red-500 py-2 px-4 font-medium">
                Not Entered
            </div>
            <div class="w-1/4 text-left text-red-500 py-2 px-4 font-medium">
                Not Entered
            </div>
        </div>
    @endforelse
</div>
@endsection