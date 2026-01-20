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
                <!-- Name & Phone Row -->
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
                </div>
                
                <!-- Email Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address <span class="text-gray-400">(optional)</span></label>
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
                        <p class="mt-1 text-xs text-gray-500">Login credentials will be sent to this email</p>
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

            <!-- Teacher Roles Section -->
            <div class="px-8 py-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center mb-4">
                    <span class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </span>
                    Teacher Roles
                </h3>
                <p class="text-sm text-gray-600 mb-4">Select all roles that apply to this teacher (optional)</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Class Teacher -->
                    <label class="role-option relative flex items-start p-4 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition-all">
                        <input type="checkbox" name="is_class_teacher" value="1" class="hidden" {{ old('is_class_teacher') ? 'checked' : '' }}>
                        <div class="role-checkbox w-6 h-6 border-2 border-gray-300 rounded-lg mr-3 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-white hidden" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="font-semibold text-gray-900">Class Teacher</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Can manage class students and attendance</p>
                        </div>
                    </label>
                    
                    <!-- HOD -->
                    <label class="role-option relative flex items-start p-4 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-green-400 hover:bg-green-50 transition-all">
                        <input type="checkbox" name="is_hod" value="1" class="hidden" {{ old('is_hod') ? 'checked' : '' }}>
                        <div class="role-checkbox w-6 h-6 border-2 border-gray-300 rounded-lg mr-3 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-white hidden" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-semibold text-gray-900">Head of Department</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Department management responsibilities</p>
                        </div>
                    </label>
                    
                    <!-- Sport Director -->
                    <label class="role-option relative flex items-start p-4 rounded-xl border-2 border-gray-200 cursor-pointer hover:border-orange-400 hover:bg-orange-50 transition-all">
                        <input type="checkbox" name="is_sport_director" value="1" class="hidden" {{ old('is_sport_director') ? 'checked' : '' }}>
                        <div class="role-checkbox w-6 h-6 border-2 border-gray-300 rounded-lg mr-3 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-white hidden" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-orange-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-semibold text-gray-900">Sport Director</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Manages sports activities and teams</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Teacher Session Section -->
            <div class="px-8 py-6 border-t border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center mb-4">
                    <span class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    Work Session
                </h3>
                <p class="text-sm text-gray-600 mb-4">Select which session(s) this teacher works</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Morning Session -->
                    <label for="session_morning" class="session-option relative flex items-start p-4 rounded-xl border-2 {{ old('session', 'both') == 'morning' ? 'border-amber-500 bg-amber-50' : 'border-gray-200' }} cursor-pointer hover:border-amber-400 hover:bg-amber-50 transition-all">
                        <input type="radio" name="session" value="morning" id="session_morning" class="sr-only peer" {{ old('session', 'both') == 'morning' ? 'checked' : '' }}>
                        <div class="session-radio w-6 h-6 border-2 {{ old('session', 'both') == 'morning' ? 'border-amber-500 bg-amber-500' : 'border-gray-300' }} rounded-full mr-3 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <div class="w-2 h-2 bg-white rounded-full {{ old('session', 'both') == 'morning' ? '' : 'hidden' }}"></div>
                        </div>
                        <div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-amber-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span class="font-semibold text-gray-900">Morning Only</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Works morning session only</p>
                        </div>
                    </label>
                    
                    
                    <!-- Afternoon Session -->
                    <label for="session_afternoon" class="session-option relative flex items-start p-4 rounded-xl border-2 {{ old('session', 'both') == 'afternoon' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }} cursor-pointer hover:border-indigo-400 hover:bg-indigo-50 transition-all">
                        <input type="radio" name="session" value="afternoon" id="session_afternoon" class="sr-only peer" {{ old('session', 'both') == 'afternoon' ? 'checked' : '' }}>
                        <div class="session-radio w-6 h-6 border-2 {{ old('session', 'both') == 'afternoon' ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300' }} rounded-full mr-3 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <div class="w-2 h-2 bg-white rounded-full {{ old('session', 'both') == 'afternoon' ? '' : 'hidden' }}"></div>
                        </div>
                        <div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                <span class="font-semibold text-gray-900">Afternoon Only</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Works afternoon session only</p>
                        </div>
                    </label>
                    
                    <!-- Both Sessions -->
                    <label for="session_both" class="session-option relative flex items-start p-4 rounded-xl border-2 {{ old('session', 'both') == 'both' ? 'border-purple-500 bg-purple-50' : 'border-gray-200' }} cursor-pointer hover:border-purple-400 hover:bg-purple-50 transition-all">
                        <input type="radio" name="session" value="both" id="session_both" class="sr-only peer" {{ old('session', 'both') == 'both' ? 'checked' : '' }}>
                        <div class="session-radio w-6 h-6 border-2 {{ old('session', 'both') == 'both' ? 'border-purple-500 bg-purple-500' : 'border-gray-300' }} rounded-full mr-3 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <div class="w-2 h-2 bg-white rounded-full {{ old('session', 'both') == 'both' ? '' : 'hidden' }}"></div>
                        </div>
                        <div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="font-semibold text-gray-900">Both Sessions</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Works full day (default)</p>
                        </div>
                    </label>
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

        // Role checkbox functionality
        $('.role-option').on('click', function() {
            const $input = $(this).find('input[type="checkbox"]');
            const isChecked = $input.prop('checked');
            $input.prop('checked', !isChecked);
            
            const $checkbox = $(this).find('.role-checkbox');
            const $checkIcon = $checkbox.find('svg');
            
            if (!isChecked) {
                // Check the box
                if ($(this).find('input[name="is_class_teacher"]').length) {
                    $(this).removeClass('border-gray-200').addClass('border-blue-500 bg-blue-50');
                    $checkbox.removeClass('border-gray-300').addClass('border-blue-500 bg-blue-500');
                } else if ($(this).find('input[name="is_hod"]').length) {
                    $(this).removeClass('border-gray-200').addClass('border-green-500 bg-green-50');
                    $checkbox.removeClass('border-gray-300').addClass('border-green-500 bg-green-500');
                } else {
                    $(this).removeClass('border-gray-200').addClass('border-orange-500 bg-orange-50');
                    $checkbox.removeClass('border-gray-300').addClass('border-orange-500 bg-orange-500');
                }
                $checkIcon.removeClass('hidden');
            } else {
                // Uncheck the box
                $(this).removeClass('border-blue-500 border-green-500 border-orange-500 bg-blue-50 bg-green-50 bg-orange-50').addClass('border-gray-200');
                $checkbox.removeClass('border-blue-500 border-green-500 border-orange-500 bg-blue-500 bg-green-500 bg-orange-500').addClass('border-gray-300');
                $checkIcon.addClass('hidden');
            }
        });

        // Set initial state for role checkboxes
        $('.role-option input[type="checkbox"]:checked').each(function() {
            $(this).closest('.role-option').trigger('click');
            $(this).closest('.role-option').trigger('click');
        });

        function updateSessionRadio() {
            // Remove active state from all options
            $('.session-option').each(function() {
                $(this).removeClass('border-amber-500 border-indigo-500 border-purple-500 bg-amber-50 bg-indigo-50 bg-purple-50').addClass('border-gray-200');
                $(this).find('.session-radio').removeClass('border-amber-500 border-indigo-500 border-purple-500 bg-amber-500 bg-indigo-500 bg-purple-500').addClass('border-gray-300');
                $(this).find('.session-radio div').addClass('hidden');
            });
            
            // Add active state to checked option
            $('input[name="session"]:checked').each(function() {
                const $label = $(this).closest('.session-option');
                const value = $(this).val();
                const $radio = $label.find('.session-radio');
                
                if (value === 'morning') {
                    $label.removeClass('border-gray-200').addClass('border-amber-500 bg-amber-50');
                    $radio.removeClass('border-gray-300').addClass('border-amber-500 bg-amber-500');
                } else if (value === 'afternoon') {
                    $label.removeClass('border-gray-200').addClass('border-indigo-500 bg-indigo-50');
                    $radio.removeClass('border-gray-300').addClass('border-indigo-500 bg-indigo-500');
                } else {
                    $label.removeClass('border-gray-200').addClass('border-purple-500 bg-purple-50');
                    $radio.removeClass('border-gray-300').addClass('border-purple-500 bg-purple-500');
                }
                
                $radio.find('div').removeClass('hidden');
            });
        }
        
        // Attach change event listener
        $('input[name="session"]').on('change', updateSessionRadio);
        
        // Set initial state
        updateSessionRadio();
    })
</script>
@endpush