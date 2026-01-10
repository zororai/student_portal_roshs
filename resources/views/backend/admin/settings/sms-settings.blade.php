@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">SMS Settings</h1>
                <p class="mt-2 text-sm text-gray-600">Configure SMS notification format and message templates</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- SMS Count Card -->
    <div class="mb-6 bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Total SMS Sent</h3>
                        <p class="text-sm text-gray-500">Number of SMS messages sent through the system</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <span class="text-4xl font-bold text-blue-600">{{ number_format($smsSentCount) }}</span>
                        <p class="text-xs text-gray-500">messages</p>
                    </div>
                    <form action="{{ route('admin.settings.sms.reset-count') }}" method="POST" onsubmit="return confirm('Are you sure you want to reset the SMS count to 0?');">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-medium rounded-lg transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Reset Count
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('admin.settings.sms.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Phone Format Section -->
            <div class="px-8 py-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                    </span>
                    Phone Number Format
                </h3>
            </div>
            
            <div class="px-8 py-6 space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Default Country Code <span class="text-red-500">*</span></label>
                    <div class="relative max-w-xs">
                        <input type="text" name="country_code" value="{{ old('country_code', $countryCode) }}" 
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('country_code') border-red-500 @enderror"
                            placeholder="+263">
                    </div>
                    @error('country_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">Phone numbers without a country code will be prefixed with this value (e.g., 0771234567 becomes +263771234567)</p>
                </div>
            </div>

            <!-- Message Templates Section -->
            <div class="px-8 py-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center mb-4">
                    <span class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </span>
                    Message Templates
                </h3>
                
                <!-- Placeholders Info -->
                <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-700">Available Placeholders:</p>
                            <ul class="text-sm text-blue-600 mt-1 space-y-1">
                                <li><code class="bg-blue-100 px-1 rounded">{name}</code> - Teacher's full name</li>
                                <li><code class="bg-blue-100 px-1 rounded">{phone}</code> - Teacher's phone number (used as login)</li>
                                <li><code class="bg-blue-100 px-1 rounded">{password}</code> - Default password</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Teacher Credentials Template -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Teacher Account Credentials SMS <span class="text-red-500">*</span></label>
                        <textarea name="teacher_credentials_template" rows="4" id="teacher_template"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('teacher_credentials_template') border-red-500 @enderror"
                            placeholder="Enter SMS template...">{{ old('teacher_credentials_template', $teacherCredentialsTemplate) }}</textarea>
                        @error('teacher_credentials_template')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 flex items-center justify-between">
                            <p class="text-xs text-gray-500">Character count: <span id="char_count">0</span>/160 (SMS limit)</p>
                            <button type="button" onclick="previewMessage()" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Preview Message</button>
                        </div>
                    </div>

                    <!-- Preview Box -->
                    <div id="preview_box" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Message Preview</label>
                        <div class="bg-gray-100 border border-gray-200 rounded-lg p-4">
                            <div class="flex items-start">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Sample Message (using placeholder values)</p>
                                    <p id="preview_text" class="text-sm text-gray-800"></p>
                                    <p class="text-xs text-gray-500 mt-2">Length: <span id="preview_length">0</span> characters</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-4">
                <a href="{{ route('home') }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        const textarea = $('#teacher_template');
        const charCount = $('#char_count');
        
        function updateCharCount() {
            const length = textarea.val().length;
            charCount.text(length);
            if (length > 160) {
                charCount.addClass('text-red-600 font-semibold').removeClass('text-gray-500');
            } else {
                charCount.removeClass('text-red-600 font-semibold').addClass('text-gray-500');
            }
        }
        
        textarea.on('input', updateCharCount);
        updateCharCount();
    });
    
    function previewMessage() {
        const template = $('#teacher_template').val();
        
        $.ajax({
            url: '{{ route("admin.settings.sms.preview") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                template: template
            },
            success: function(response) {
                $('#preview_text').text(response.preview);
                $('#preview_length').text(response.character_count);
                $('#preview_box').removeClass('hidden');
            },
            error: function() {
                alert('Failed to generate preview');
            }
        });
    }
</script>
@endpush
