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

                <div class="space-y-8">
                    <!-- Teacher Credentials Template -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="inline-flex items-center px-2 py-1 rounded bg-blue-100 text-blue-700 text-xs mr-2">Teacher Creation</span>
                            Teacher Account Credentials SMS <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-500 mb-2">Placeholders: <code class="bg-gray-200 px-1 rounded">{name}</code> <code class="bg-gray-200 px-1 rounded">{phone}</code> <code class="bg-gray-200 px-1 rounded">{password}</code></p>
                        <textarea name="teacher_credentials_template" rows="3"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('teacher_credentials_template') border-red-500 @enderror"
                            placeholder="Enter SMS template...">{{ old('teacher_credentials_template', $teacherCredentialsTemplate) }}</textarea>
                        @error('teacher_credentials_template')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Student/Parent Registration Template -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="inline-flex items-center px-2 py-1 rounded bg-green-100 text-green-700 text-xs mr-2">Student Registration</span>
                            Student/Parent Registration SMS <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-500 mb-2">Placeholders: <code class="bg-gray-200 px-1 rounded">{student_name}</code> <code class="bg-gray-200 px-1 rounded">{url}</code></p>
                        <textarea name="student_parent_registration_template" rows="3"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('student_parent_registration_template') border-red-500 @enderror"
                            placeholder="Enter SMS template...">{{ old('student_parent_registration_template', $studentParentRegistrationTemplate) }}</textarea>
                        @error('student_parent_registration_template')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Parent Password Reset Template -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="inline-flex items-center px-2 py-1 rounded bg-yellow-100 text-yellow-700 text-xs mr-2">Password Reset</span>
                            Parent Password Reset SMS <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-500 mb-2">Placeholders: <code class="bg-gray-200 px-1 rounded">{name}</code> <code class="bg-gray-200 px-1 rounded">{email}</code> <code class="bg-gray-200 px-1 rounded">{password}</code></p>
                        <textarea name="parent_password_reset_template" rows="3"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('parent_password_reset_template') border-red-500 @enderror"
                            placeholder="Enter SMS template...">{{ old('parent_password_reset_template', $parentPasswordResetTemplate) }}</textarea>
                        @error('parent_password_reset_template')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teacher Password Reset Template -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="inline-flex items-center px-2 py-1 rounded bg-yellow-100 text-yellow-700 text-xs mr-2">Password Reset</span>
                            Teacher Password Reset SMS <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-500 mb-2">Placeholders: <code class="bg-gray-200 px-1 rounded">{name}</code> <code class="bg-gray-200 px-1 rounded">{email}</code> <code class="bg-gray-200 px-1 rounded">{password}</code></p>
                        <textarea name="teacher_password_reset_template" rows="3"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('teacher_password_reset_template') border-red-500 @enderror"
                            placeholder="Enter SMS template...">{{ old('teacher_password_reset_template', $teacherPasswordResetTemplate) }}</textarea>
                        @error('teacher_password_reset_template')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Admin User Credentials Template -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="inline-flex items-center px-2 py-1 rounded bg-purple-100 text-purple-700 text-xs mr-2">Admin Users</span>
                            Admin User Credentials SMS <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-500 mb-2">Placeholders: <code class="bg-gray-200 px-1 rounded">{name}</code> <code class="bg-gray-200 px-1 rounded">{email}</code> <code class="bg-gray-200 px-1 rounded">{password}</code></p>
                        <textarea name="admin_user_credentials_template" rows="3"
                            class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('admin_user_credentials_template') border-red-500 @enderror"
                            placeholder="Enter SMS template...">{{ old('admin_user_credentials_template', $adminUserCredentialsTemplate) }}</textarea>
                        @error('admin_user_credentials_template')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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

