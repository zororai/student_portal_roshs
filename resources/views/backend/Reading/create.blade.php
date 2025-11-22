@extends('layouts.app')

@section('content')
    <div class="add-reading">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-gray-700 uppercase font-bold">Add New Reading</h2>
        </div>

        <form action="{{ route('readings.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded border-b-4 border-gray-300 p-6">
            @csrf
            <input type="hidden" name="subject_id" value="{{ $subject_id }}"> <!-- Hidden field for subject_id -->

            <div class="mb-4">
                <label for="name" class="form-label font-semibold">Name</label>
                <input type="text" name="name" class="form-control border rounded w-full p-2" required>
            </div>

            <div class="mb-4">
                <label for="description" class="form-label font-semibold">Description</label>
                <textarea name="description" class="form-control border rounded w-full p-2" rows="4"></textarea>
            </div>

            <div class="mb-4">
                <label for="path" class="form-label font-semibold">Upload File</label>
                <input type="file" name="path" class="form-control border rounded w-full p-2" required>
            </div>

            <div class="mb-4">
                <label for="youtube_link" class="form-label font-semibold">YouTube Link</label>
                <input type="url" name="youtube_link" class="form-control border rounded w-full p-2">
            </div>

            <button type="submit" class="bg-green-500 text-white text-sm uppercase py-2 px-4 rounded">
                Create Reading
            </button>
        </form>
    </div>
@endsection