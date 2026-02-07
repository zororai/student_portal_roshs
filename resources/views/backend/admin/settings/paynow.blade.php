@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Paynow Settings</h1>
            <p class="text-gray-600 mt-1">Configure Paynow payment gateway credentials</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Settings Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form action="{{ route('admin.settings.paynow.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Paynow ID -->
                <div>
                    <label for="paynow_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Paynow Integration ID <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="paynow_id" 
                           id="paynow_id" 
                           value="{{ old('paynow_id', $setting->paynow_id ?? '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Enter Paynow Integration ID"
                           required>
                    <p class="mt-1 text-sm text-gray-500">Your Paynow merchant integration ID</p>
                </div>

                <!-- Paynow Key -->
                <div>
                    <label for="paynow_key" class="block text-sm font-medium text-gray-700 mb-2">
                        Paynow Integration Key <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="paynow_key" 
                           id="paynow_key" 
                           value="{{ old('paynow_key', $setting->paynow_key ?? '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Enter Paynow Integration Key"
                           required>
                    <p class="mt-1 text-sm text-gray-500">Your Paynow merchant integration key</p>
                </div>

                <!-- Environment -->
                <div>
                    <label for="environment" class="block text-sm font-medium text-gray-700 mb-2">
                        Environment <span class="text-red-500">*</span>
                    </label>
                    <select name="environment" 
                            id="environment" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            required>
                        <option value="sandbox" {{ old('environment', $setting->environment ?? 'sandbox') == 'sandbox' ? 'selected' : '' }}>
                            Sandbox (Testing)
                        </option>
                        <option value="production" {{ old('environment', $setting->environment ?? '') == 'production' ? 'selected' : '' }}>
                            Production (Live)
                        </option>
                    </select>
                    <p class="mt-1 text-sm text-gray-500">Select sandbox for testing, production for live payments</p>
                </div>

                <!-- Active Status -->
                <div class="flex items-center pt-8">
                    <input type="checkbox" 
                           name="is_active" 
                           id="is_active" 
                           value="1"
                           {{ old('is_active', $setting->is_active ?? true) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_active" class="ml-2 block text-sm text-gray-700">
                        Enable Paynow payments
                    </label>
                </div>

                <!-- Return URL -->
                <div>
                    <label for="return_url" class="block text-sm font-medium text-gray-700 mb-2">
                        Return URL (Optional)
                    </label>
                    <input type="url" 
                           name="return_url" 
                           id="return_url" 
                           value="{{ old('return_url', $setting->return_url ?? '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="https://yoursite.com/payments/return">
                    <p class="mt-1 text-sm text-gray-500">URL where users return after payment</p>
                </div>

                <!-- Result URL -->
                <div>
                    <label for="result_url" class="block text-sm font-medium text-gray-700 mb-2">
                        Result URL (Optional)
                    </label>
                    <input type="url" 
                           name="result_url" 
                           id="result_url" 
                           value="{{ old('result_url', $setting->result_url ?? '') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="https://yoursite.com/payments/result">
                    <p class="mt-1 text-sm text-gray-500">URL where Paynow sends payment results</p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                <button type="button" 
                        onclick="testConnection()" 
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Test Connection
                </button>
                
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg shadow-md hover:from-indigo-700 hover:to-purple-700 transition-all">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Information Card -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mt-6 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Important:</strong> These credentials are stored securely in the database. Make sure to use sandbox credentials for testing before switching to production.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function testConnection() {
    const btn = event.target.closest('button');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Testing...';

    fetch('{{ route("admin.settings.paynow.test") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('✓ ' + data.message + '\nEnvironment: ' + data.environment + '\nPaynow ID: ' + data.paynow_id);
        } else {
            alert('✗ ' + data.message);
        }
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    })
    .catch(error => {
        alert('✗ Connection test failed: ' + error.message);
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}
</script>
@endsection
