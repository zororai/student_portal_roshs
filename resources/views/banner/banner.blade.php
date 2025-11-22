@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 bg-white shadow-md rounded">
    <h2 class="text-gray-700 uppercase font-bold mb-4">Manage Banner Images</h2>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Banner Upload Form -->
    <form action="{{ route('banner.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            @if(isset($banner))
                <div class="text-center">
                    <label class="block font-semibold mb-2">Current Image 1:</label>
                    @if($banner->image_path_1)
                        <img src="{{ asset('storage/' . $banner->image_path_1) }}" class="w-48 h-32 object-cover rounded shadow">
                    @else
                        <p class="text-gray-500">No image available</p>
                    @endif
                </div>
                <div class="text-center">
                    <label class="block font-semibold mb-2">Current Image 2:</label>
                    @if($banner->image_path_2)
                        <img src="{{ asset('storage/' . $banner->image_path_2) }}" class="w-48 h-32 object-cover rounded shadow">
                    @else
                        <p class="text-gray-500">No image available</p>
                    @endif
                </div>
                <div class="text-center">
                    <label class="block font-semibold mb-2">Current Image 3:</label>
                    @if($banner->image_path_3)
                        <img src="{{ asset('storage/' . $banner->image_path_3) }}" class="w-48 h-32 object-cover rounded shadow">
                    @else
                        <p class="text-gray-500">No image available</p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Image Upload Inputs -->
        <div class="mb-4">
            <label class="block font-semibold mb-2">Upload New Image 1:</label>
            <input type="file" name="image_path_1" class="w-full px-3 py-2 border rounded shadow-sm">
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-2">Upload New Image 2:</label>
            <input type="file" name="image_path_2" class="w-full px-3 py-2 border rounded shadow-sm">
        </div>

        <div class="mb-4">
            <label class="block font-semibold mb-2">Upload New Image 3:</label>
            <input type="file" name="image_path_3" class="w-full px-3 py-2 border rounded shadow-sm">
        </div>

        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
            Save Banner
        </button>
    </form>
</div>
@endsection
