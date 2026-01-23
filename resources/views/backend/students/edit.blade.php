@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Student</h1>
                    <p class="mt-1 text-sm text-gray-500">Update student information and details</p>
                </div>
                <a href="{{ route('student.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Students
                </a>
            </div>

            <form action="{{ route('student.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Profile Card -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden sticky top-8">
                            <!-- Profile Header -->
                            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8">
                                <div class="flex flex-col items-center">
                                    <div class="relative group">
                                        <img id="preview-image" class="w-28 h-28 rounded-full border-4 border-white shadow-lg object-cover" 
                                             src="{{ asset('images/profile/' . $student->user->profile_picture) }}" 
                                             alt="{{ $student->user->name }}">
                                        <label for="profile_picture" class="absolute inset-0 flex items-center justify-center bg-black/50 rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity duration-200">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </label>
                                        <input type="file" name="profile_picture" id="profile_picture" class="hidden" accept="image/*" onchange="previewImage(this)">
                                    </div>
                                    <h2 class="mt-4 text-xl font-bold text-white">{{ $student->user->name }}</h2>
                                    <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm">
                                        {{ $student->roll_number }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Quick Stats -->
                            <div class="px-6 py-5 border-t border-gray-100">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-3">Current Class</p>
                                <div class="flex items-center text-gray-900">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="font-semibold">{{ $student->class->class_name ?? 'Not Assigned' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Section -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Basic Information Card -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Basic Information
                                </h3>
                            </div>
                            <div class="px-6 py-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Name -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                        <input name="name" type="text" value="{{ $student->user->name }}" 
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" 
                                               placeholder="Enter full name">
                                        @error('name')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <!-- Email -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                        <input name="email" type="email" value="{{ $student->user->email }}" 
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" 
                                               placeholder="Enter email address">
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <!-- Roll Number -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Roll Number</label>
                                        <input name="roll_number" type="text" value="{{ $student->roll_number }}" 
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" 
                                               placeholder="Enter roll number">
                                        @error('roll_number')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <!-- Phone -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                        <input name="phone" type="text" value="{{ $student->phone }}" 
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" 
                                               placeholder="Enter phone number">
                                        @error('phone')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <!-- Date of Birth -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                        <input name="dateofbirth" id="datepicker-se" type="text" value="{{ $student->dateofbirth }}" autocomplete="off"
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" 
                                               placeholder="Select date">
                                        @error('dateofbirth')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <!-- Gender -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                        <div class="flex items-center space-x-6 mt-3">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="gender" value="male" {{ ($student->gender == 'male') ? 'checked' : '' }} 
                                                       class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700">Male</span>
                                            </label>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="gender" value="female" {{ ($student->gender == 'female') ? 'checked' : '' }} 
                                                       class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700">Female</span>
                                            </label>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="gender" value="other" {{ ($student->gender == 'other') ? 'checked' : '' }} 
                                                       class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                                <span class="ml-2 text-sm text-gray-700">Other</span>
                                            </label>
                                        </div>
                                        @error('gender')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information Card -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Address Information
                                </h3>
                            </div>
                            <div class="px-6 py-6">
                                <div class="grid grid-cols-1 gap-6">
                                    <!-- Current Address -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Address</label>
                                        <input name="current_address" type="text" value="{{ $student->current_address }}" 
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" 
                                               placeholder="Enter current address">
                                        @error('current_address')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    
                                    <!-- Permanent Address -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Permanent Address</label>
                                        <input name="permanent_address" type="text" value="{{ $student->permanent_address }}" 
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" 
                                               placeholder="Enter permanent address">
                                        @error('permanent_address')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Information Card -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    Academic Information
                                </h3>
                            </div>
                            <div class="px-6 py-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Assign Class -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Class</label>
                                        <div class="relative">
                                            <select name="class_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 appearance-none bg-white">
                                                <option value="">-- Select Class --</option>
                                                @foreach ($classes as $class)
                                                    <option value="{{ $class->id }}" {{ ($class->id === $student->class_id) ? 'selected' : '' }}>
                                                        {{ $class->class_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Student Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Student Type</label>
                                        <div class="relative">
                                            <select name="student_type" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 appearance-none bg-white">
                                                <option value="day" {{ ($student->student_type == 'day') ? 'selected' : '' }}>Day</option>
                                                <option value="boarding" {{ ($student->student_type == 'boarding') ? 'selected' : '' }}>Boarding</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Curriculum Type -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Curriculum</label>
                                        <div class="relative">
                                            <select name="curriculum_type" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 appearance-none bg-white">
                                                <option value="zimsec" {{ ($student->curriculum_type == 'zimsec') ? 'selected' : '' }}>ZIMSEC</option>
                                                <option value="cambridge" {{ ($student->curriculum_type == 'cambridge') ? 'selected' : '' }}>Cambridge</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- New Student -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Student Status</label>
                                        <div class="relative">
                                            <select name="is_new_student" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 appearance-none bg-white">
                                                <option value="0" {{ (!$student->is_new_student) ? 'selected' : '' }}>Existing Student</option>
                                                <option value="1" {{ ($student->is_new_student) ? 'selected' : '' }}>New Student</option>
                                            </select>
                                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Scholarship Percentage -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Scholarship %</label>
                                        <input name="scholarship_percentage" type="number" min="0" max="100" step="1" value="{{ $student->scholarship_percentage ?? 0 }}" 
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" 
                                               placeholder="0">
                                        @error('scholarship_percentage')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Parents/Guardians Card -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Parents / Guardians
                                </h3>
                            </div>
                            <div class="px-6 py-6">
                                @php
                                    $studentParentsList = $student->parents;
                                @endphp
                                
                                @if($studentParentsList->count() > 0)
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Current Parents</label>
                                    <div class="space-y-2">
                                        @foreach($studentParentsList as $existingParent)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold mr-3">
                                                    {{ strtoupper(substr($existingParent->user->name ?? 'P', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $existingParent->user->name ?? 'N/A' }}</p>
                                                    <p class="text-xs text-gray-500">{{ $existingParent->phone ?? 'No phone' }}</p>
                                                </div>
                                            </div>
                                            <input type="hidden" name="existing_parent_ids[]" value="{{ $existingParent->id }}">
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                <div class="border-t border-gray-200 pt-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-3">Add New Parent</label>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Parent Name</label>
                                            <input name="new_parent_name" type="text" value="{{ old('new_parent_name') }}" 
                                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" 
                                                   placeholder="Enter parent's full name">
                                            @error('new_parent_name')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Parent Email</label>
                                            <input name="new_parent_email" type="email" value="{{ old('new_parent_email') }}" 
                                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" 
                                                   placeholder="Enter parent's email">
                                            @error('new_parent_email')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Parent Phone</label>
                                            <input name="new_parent_phone" type="text" value="{{ old('new_parent_phone') }}" 
                                                   class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200" 
                                                   placeholder="Enter parent's phone number">
                                            @error('new_parent_phone')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs font-medium text-gray-500 mb-1">Parent Gender</label>
                                            <select name="new_parent_gender" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors duration-200 appearance-none bg-white">
                                                <option value="">-- Select Gender --</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>
                                    </div>
                                    <p class="mt-3 text-xs text-gray-500">Leave blank if not adding a new parent. Fill in name, email, and phone to create a new parent.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end space-x-4 pt-4">
                            <a href="{{ route('student.index') }}" class="px-6 py-3 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200">
                                Cancel
                            </a>
                            <button type="submit" class="px-8 py-3 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold shadow-lg hover:from-indigo-600 hover:to-purple-700 hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Student
                            </button>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Image preview function
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-image').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $(function() {
        $("#datepicker-se").datepicker({ dateFormat: 'yy-mm-dd' });
    })
</script>
@endpush
