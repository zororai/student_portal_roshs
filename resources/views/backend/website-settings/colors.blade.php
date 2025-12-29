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
            <h1 class="text-2xl font-bold text-gray-800">Theme Colors</h1>
        </div>
        <p class="text-gray-600">Customize your website's color scheme</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md">
        <form action="{{ route('admin.website.update') }}" method="POST">
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
                        
                        <div class="flex items-center space-x-3">
                            <input 
                                type="color" 
                                name="{{ $setting->key }}" 
                                id="{{ $setting->key }}" 
                                value="{{ $setting->value }}"
                                class="w-16 h-12 border border-gray-300 rounded-lg cursor-pointer"
                            >
                            <input 
                                type="text" 
                                value="{{ $setting->value }}"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg bg-white"
                                onchange="document.getElementById('{{ $setting->key }}').value = this.value"
                                oninput="document.getElementById('{{ $setting->key }}').value = this.value"
                            >
                        </div>
                        
                        <!-- Color Preview -->
                        <div class="mt-3">
                            <div class="h-8 rounded" style="background-color: {{ $setting->value }}"></div>
                        </div>
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

    <!-- Color Preview Section -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Live Preview</h2>
        <div class="border rounded-lg overflow-hidden">
            <div class="p-4" style="background-color: {{ $settings->where('key', 'header_bg_color')->first()->value ?? '#ffffff' }}">
                <span class="font-bold" style="color: {{ $settings->where('key', 'primary_color')->first()->value ?? '#2d5016' }}">Header Preview</span>
            </div>
            <div class="p-8 bg-white">
                <h3 class="text-xl font-bold mb-2" style="color: {{ $settings->where('key', 'primary_color')->first()->value ?? '#2d5016' }}">Primary Color Text</h3>
                <p class="mb-4" style="color: {{ $settings->where('key', 'secondary_color')->first()->value ?? '#1a365d' }}">Secondary color text example</p>
                <button class="px-4 py-2 rounded text-white" style="background-color: {{ $settings->where('key', 'accent_color')->first()->value ?? '#d69e2e' }}">Accent Button</button>
            </div>
            <div class="p-4 text-white" style="background-color: {{ $settings->where('key', 'footer_bg_color')->first()->value ?? '#1a202c' }}">
                Footer Preview
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('input[type="color"]').forEach(colorInput => {
    colorInput.addEventListener('input', function() {
        this.nextElementSibling.value = this.value;
    });
});
</script>
@endsection
