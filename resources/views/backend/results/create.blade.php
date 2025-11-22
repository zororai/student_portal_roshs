@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-lg font-bold text-gray-700">Edit Result for {{ $result->student->user->name }}</h2>

    <form action="{{ route('results.update', $result->id) }}" method="POST">
        @csrf
        @method('POST')

        <div class="mb-4">
            <label for="score" class="block text-gray-700">Marks</label>
            <input type="number" name="score" id="score" value="{{ $result->score }}" class="form-control" required>
        </div>

        <div class="mb-4">
            <label for="comment" class="block text-gray-700">Comment</label>
            <input type="text" name="comment" id="comment" value="{{ $result->comment }}" class="form-control">
        </div>

        <div class="mb-4">
            <label for="mark_grade" class="block text-gray-700">Grade</label>
            <input type="text" name="mark_grade" id="mark_grade" value="{{ $result->mark_grade }}" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Update Result</button>
    </form>
</div>
@endsection