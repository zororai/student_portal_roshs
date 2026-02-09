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
            <h1 class="text-2xl font-bold text-gray-800">Images & Logo</h1>
        </div>
        <p class="text-gray-600">Upload and manage website images and logo</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded mb-4">
        <strong>How to change images:</strong> Click "Choose Image" to select a new file, then click "Save Changes" at the bottom.
    </div>

    <div class="bg-white rounded-lg shadow-md">
        <form action="{{ route('admin.website.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($settings as $setting)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label for="{{ $setting->key }}" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ $setting->label }}
                        </label>
                        @if($setting->description)
                        <p class="text-xs text-gray-500 mb-3">{{ $setting->description }}</p>
                        @endif
                        
                        <!-- Current Image Preview -->
                        <div class="mb-3">
                            @if($setting->value)
                            <div class="relative inline-block">
                                <img 
                                    src="{{ asset($setting->value) }}" 
                                    alt="{{ $setting->label }}" 
                                    class="max-w-full h-24 object-contain border rounded-lg bg-white p-2"
                                    id="preview_{{ $setting->key }}"
                                >
                            </div>
                            @else
                            <div class="w-full h-24 bg-gray-200 rounded-lg flex items-center justify-center" id="placeholder_{{ $setting->key }}">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            @endif
                        </div>
                        
                        <!-- File Input -->
                        <div class="relative">
                            <input 
                                type="file" 
                                name="{{ $setting->key }}" 
                                id="{{ $setting->key }}" 
                                accept="image/*"
                                class="hidden"
                                onchange="previewImage(this, '{{ $setting->key }}')"
                            >
                            <label 
                                for="{{ $setting->key }}" 
                                id="label_{{ $setting->key }}"
                                class="w-full flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 transition-colors"
                            >
                                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <span class="text-sm text-gray-600" id="labeltext_{{ $setting->key }}">Choose Image</span>
                            </label>
                        </div>
                        <p class="text-xs text-gray-400 mt-2" id="hint_{{ $setting->key }}">Recommended: PNG or JPG, max 2MB</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end space-x-3">
                <a href="{{ route('admin.website.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input, key) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.getElementById('preview_' + key);
            let placeholder = document.getElementById('placeholder_' + key);
            
            if (preview) {
                preview.src = e.target.result;
            } else if (placeholder) {
                placeholder.innerHTML = '<img src="' + e.target.result + '" alt="Preview" class="max-w-full h-24 object-contain border rounded-lg bg-white p-2" id="preview_' + key + '">';
            }
        }
        reader.readAsDataURL(file);
        
        // Update label to show selected filename
        const label = document.getElementById('label_' + key);
        const labelText = document.getElementById('labeltext_' + key);
        const hint = document.getElementById('hint_' + key);
        
        if (label) {
            label.classList.remove('border-gray-300');
            label.classList.add('border-green-500', 'bg-green-50');
        }
        if (labelText) {
            labelText.textContent = file.name.length > 20 ? file.name.substring(0, 17) + '...' : file.name;
            labelText.classList.remove('text-gray-600');
            labelText.classList.add('text-green-700');
        }
        if (hint) {
            hint.textContent = 'File selected! Click "Save Changes" to upload.';
            hint.classList.remove('text-gray-400');
            hint.classList.add('text-green-600', 'font-medium');
        }
    }
}
</script>
@endsection
