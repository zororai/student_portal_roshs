@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <h1 class="text-2xl font-bold text-white">InboxIQ SMS Test</h1>
            <p class="text-blue-100 text-sm mt-1">Test SMS sending directly from web interface</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mx-6 mt-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mx-6 mt-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form id="sms-test-form" action="{{ route('sms-test.send') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Phone Number -->
            <div>
                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">
                    Phone Number
                </label>
                <input 
                    type="text" 
                    name="phone" 
                    id="phone" 
                    value="{{ old('phone', $phone ?? '+263775219766') }}"
                    placeholder="+263775219766"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                    required
                >
                @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Format: +[country code][number] (e.g., +263775219766)</p>
            </div>

            <!-- Message -->
            <div>
                <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">
                    Message
                </label>
                <textarea 
                    name="message" 
                    id="message" 
                    rows="4"
                    placeholder="Enter your test message here..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('message') border-red-500 @enderror"
                    required
                >{{ old('message', 'Test message from ROHS Portal - ' . now()->format('Y-m-d H:i:s')) }}</textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Maximum 500 characters</p>
            </div>

            <!-- API Info -->
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">API Configuration</h3>
                <div class="space-y-1 text-xs text-gray-600">
                    <p><span class="font-medium">Endpoint:</span> https://api.inboxiq.co.zw/api/v1/send-sms</p>
                    <p><span class="font-medium">Username:</span> {{ env('INBOXIQ_USERNAME', 'Not configured') }}</p>
                    <p><span class="font-medium">API Key:</span> {{ env('INBOXIQ_API_KEY') ? substr(env('INBOXIQ_API_KEY'), 0, 10) . '...' : 'Not configured' }}</p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-gray-900">
                    ← Back to Dashboard
                </a>
                <button 
                    type="submit" 
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all"
                >
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                        <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                    </svg>
                    Send Test SMS
                </button>
            </div>
        </form>
    </div>

    <!-- Instructions -->
    <div class="mt-6 bg-blue-50 rounded-lg p-6 border border-blue-200">
        <h3 class="text-lg font-semibold text-blue-900 mb-3">How to Use</h3>
        <ol class="space-y-2 text-sm text-blue-800">
            <li class="flex items-start">
                <span class="font-bold mr-2">1.</span>
                <span>Enter the phone number in international format (starting with +)</span>
            </li>
            <li class="flex items-start">
                <span class="font-bold mr-2">2.</span>
                <span>Type your test message (or use the default)</span>
            </li>
            <li class="flex items-start">
                <span class="font-bold mr-2">3.</span>
                <span>Click "Send Test SMS" to send via InboxIQ API</span>
            </li>
            <li class="flex items-start">
                <span class="font-bold mr-2">4.</span>
                <span>Check the response message and verify SMS delivery on the phone</span>
            </li>
        </ol>
    </div>
</div>

@if($autoSubmit ?? false)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('sms-test-form').submit();
    });
</script>
@endif
@endsection
