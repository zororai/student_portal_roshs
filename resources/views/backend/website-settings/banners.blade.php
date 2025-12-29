@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="mb-6">
        <div class="flex items-center mb-2">
            <a href="{{ route('admin.website.index') }}" class="text-blue-600 hover:text-blue-800 mr-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Homepage Banners</h1>
        </div>
        <p class="text-gray-600">Upload slider images for the homepage banner carousel</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md">
        <form action="{{ route('admin.website.banners.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @for($i = 1; $i <= 3; $i++)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Banner Slide {{ $i }}
                        </label>
                        <p class="text-xs text-gray-500 mb-3">Recommended size: 1920x600 pixels</p>
                        
                        <!-- Current Image Preview -->
                        <div class="mb-3">
                            @php 
                                $fieldName = "image_path_{$i}";
                                $imagePath = $banner && $banner->$fieldName ? $banner->$fieldName : null;
                            @endphp
                            @if($imagePath)
                            <div class="relative">
                                <img 
                                    src="{{ asset('storage/' . $imagePath) }}" 
                                    alt="Banner {{ $i }}" 
                                    class="w-full h-40 object-cover rounded-lg"
                                    id="preview_image_path_{{ $i }}"
                                >
                            </div>
                            @else
                            <div class="w-full h-40 bg-gray-200 rounded-lg flex items-center justify-center" id="placeholder_image_path_{{ $i }}">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            @endif
                        </div>
                        
                        <!-- File Input -->
                        <div class="relative">
                            <input 
                                type="file" 
                                name="image_path_{{ $i }}" 
                                id="image_path_{{ $i }}" 
                                accept="image/*"
                                class="hidden"
                                onchange="previewBanner(this, {{ $i }})"
                            >
                            <label 
                                for="image_path_{{ $i }}" 
                                class="w-full flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <span class="text-sm text-gray-600">{{ $imagePath ? 'Replace Image' : 'Upload Image' }}</span>
                            </label>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                <a href="{{ route('admin.website.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Save Banners
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Section -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Banner Preview</h2>
        <p class="text-sm text-gray-600 mb-4">These banners will appear as a slideshow on the homepage</p>
        <div class="border rounded-lg overflow-hidden">
            <div class="relative bg-gray-100" style="padding-bottom: 31.25%;">
                @if($banner)
                    @for($i = 1; $i <= 3; $i++)
                        @php $fieldName = "image_path_{$i}"; @endphp
                        @if($banner->$fieldName)
                        <div class="absolute inset-0 {{ $i > 1 ? 'hidden' : '' }}" id="slide_{{ $i }}">
                            <img src="{{ asset('storage/' . $banner->$fieldName) }}" alt="Slide {{ $i }}" class="w-full h-full object-cover">
                            <div class="absolute bottom-4 left-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded text-sm">
                                Slide {{ $i }}
                            </div>
                        </div>
                        @endif
                    @endfor
                @else
                <div class="absolute inset-0 flex items-center justify-center">
                    <p class="text-gray-500">No banners uploaded yet</p>
                </div>
                @endif
            </div>
        </div>
        @if($banner)
        <div class="flex justify-center mt-4 space-x-2">
            @for($i = 1; $i <= 3; $i++)
                @php $fieldName = "image_path_{$i}"; @endphp
                @if($banner->$fieldName)
                <button type="button" onclick="showSlide({{ $i }})" class="w-3 h-3 rounded-full bg-gray-300 hover:bg-blue-500 transition-colors slide-dot" data-slide="{{ $i }}"></button>
                @endif
            @endfor
        </div>
        @endif
    </div>
</div>

<script>
function previewBanner(input, index) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.getElementById('preview_image_path_' + index);
            let placeholder = document.getElementById('placeholder_image_path_' + index);
            
            if (preview) {
                preview.src = e.target.result;
            } else if (placeholder) {
                placeholder.innerHTML = '<img src="' + e.target.result + '" alt="Banner ' + index + '" class="w-full h-40 object-cover rounded-lg" id="preview_image_path_' + index + '">';
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function showSlide(index) {
    document.querySelectorAll('[id^="slide_"]').forEach(slide => slide.classList.add('hidden'));
    document.querySelectorAll('.slide-dot').forEach(dot => dot.classList.remove('bg-blue-500'));
    
    const slide = document.getElementById('slide_' + index);
    const dot = document.querySelector('.slide-dot[data-slide="' + index + '"]');
    
    if (slide) slide.classList.remove('hidden');
    if (dot) dot.classList.add('bg-blue-500');
}

// Initialize first dot as active
document.addEventListener('DOMContentLoaded', function() {
    const firstDot = document.querySelector('.slide-dot');
    if (firstDot) firstDot.classList.add('bg-blue-500');
});
</script>
@endsection
