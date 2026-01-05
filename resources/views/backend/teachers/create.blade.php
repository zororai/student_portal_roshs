@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Add New Teacher</h1>
                <p class="mt-2 text-sm text-gray-600">Enter basic details to create a new teacher account</p>
            </div>
            <a href="{{ route('teacher.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <!-- Info Alert -->
    <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="text-sm text-blue-700"><strong>Quick Setup:</strong> Enter basic teacher information. A default password (12345678) will be assigned.</p>
                <p class="text-sm text-blue-600 mt-1">The teacher will complete their profile (DOB, addresses, profile picture) and change password on first login.</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('teacher.store') }}" method="POST">
            @csrf
            
            <!-- Personal Information Section -->
            <div class="px-8 py-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </span>
                    Basic Information
                </h3>
            </div>
            
            <div class="px-8 py-6 space-y-6">
                <!-- Name & Email Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text" name="name" value="{{ old('name') }}" 
                                class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror"
                                placeholder="Enter full name">
                        </div>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email" name="email" value="{{ old('email') }}" 
                                class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('email') border-red-500 @enderror"
                                placeholder="teacher@example.com">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Phone & Gender Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <input type="text" name="phone" value="{{ old('phone') }}" 
                                class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('phone') border-red-500 @enderror"
                                placeholder="+263 7X XXX XXXX">
                        </div>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">This will be used as the login username</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender <span class="text-red-500">*</span></label>
                        <div class="flex flex-wrap gap-3">
                            <label class="gender-option relative flex items-center p-3 rounded-lg border-2 border-gray-300 cursor-pointer hover:border-blue-400 transition-colors flex-1">
                                <input type="radio" name="gender" value="male" class="hidden" {{ old('gender') == 'male' ? 'checked' : '' }}>
                                <div class="radio-indicator w-5 h-5 border-2 border-gray-300 rounded-full mr-2 flex items-center justify-center">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full hidden"></div>
                                </div>
                                <svg class="w-4 h-4 text-blue-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Male</span>
                            </label>
                            <label class="gender-option relative flex items-center p-3 rounded-lg border-2 border-gray-300 cursor-pointer hover:border-pink-400 transition-colors flex-1">
                                <input type="radio" name="gender" value="female" class="hidden" {{ old('gender') == 'female' ? 'checked' : '' }}>
                                <div class="radio-indicator w-5 h-5 border-2 border-gray-300 rounded-full mr-2 flex items-center justify-center">
                                    <div class="w-2 h-2 bg-pink-500 rounded-full hidden"></div>
                                </div>
                                <svg class="w-4 h-4 text-pink-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                </svg>
                                <span class="text-sm font-medium text-gray-700">Female</span>
                            </label>
                        </div>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Login Credentials Info -->
            <div class="px-8 py-6 border-t border-gray-100 bg-gradient-to-r from-green-50 to-blue-50">
                <div class="flex items-start">
                    <span class="w-10 h-10 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-4 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </span>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-900">Login Credentials</h4>
                        <p class="text-sm text-gray-600 mt-1">The teacher will log in using:</p>
                        <ul class="text-sm text-gray-600 mt-2 space-y-1">
                            <li class="flex items-center"><span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span><strong>Username:</strong>&nbsp;Phone Number</li>
                            <li class="flex items-center"><span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span><strong>Password:</strong>&nbsp;12345678 (must change on first login)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-4">
                <a href="{{ route('teacher.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Teacher
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Gender radio button functionality
        $('.gender-option').on('click', function() {
            // Remove active state from all options
            $('.gender-option').removeClass('border-blue-500 border-pink-500 border-purple-500').addClass('border-gray-300');
            $('.radio-indicator div').addClass('hidden');
            
            // Add active state to clicked option
            const $input = $(this).find('input[type="radio"]');
            $input.prop('checked', true);
            
            const value = $input.val();
            if (value === 'male') {
                $(this).removeClass('border-gray-300').addClass('border-blue-500');
            } else if (value === 'female') {
                $(this).removeClass('border-gray-300').addClass('border-pink-500');
            } else {
                $(this).removeClass('border-gray-300').addClass('border-purple-500');
            }
            
            $(this).find('.radio-indicator div').removeClass('hidden');
        });

        // Set initial state if there's an old value
        $('input[name="gender"]:checked').closest('.gender-option').trigger('click');
    })
</script>
@endpush