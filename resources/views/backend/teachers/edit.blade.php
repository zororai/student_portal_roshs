@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Teacher</h1>
                    <p class="mt-1 text-sm text-gray-500">Update teacher information and details</p>
                </div>
                <a href="{{ route('teacher.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Teachers
                </a>
            </div>

            <form action="{{ route('teacher.update', $teacher->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Profile Card -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden sticky top-8">
                            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-8">
                                <div class="flex flex-col items-center">
                                    <div class="relative group">
                                        <img id="preview-image" class="w-28 h-28 rounded-full border-4 border-white shadow-lg object-cover" 
                                             src="{{ asset('images/profile/' . $teacher->user->profile_picture) }}" 
                                             alt="{{ $teacher->user->name }}">
                                        <label for="profile_picture" class="absolute inset-0 flex items-center justify-center bg-black/50 rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity duration-200">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </label>
                                        <input type="file" name="profile_picture" id="profile_picture" class="hidden" accept="image/*" onchange="previewImage(this)">
                                    </div>
                                    <h2 class="mt-4 text-xl font-bold text-white">{{ $teacher->user->name }}</h2>
                                    <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0z"/>
                                        </svg>
                                        Teacher
                                    </span>
                                </div>
                            </div>
                            <div class="px-6 py-5">
                                <div class="space-y-3">
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-sm truncate">{{ $teacher->user->email }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <span class="text-sm">{{ $teacher->phone }}</span>
                                    </div>
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
                                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Basic Information
                                </h3>
                            </div>
                            <div class="px-6 py-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                        <input name="name" type="text" value="{{ $teacher->user->name }}" 
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200">
                                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                        <input name="email" type="email" value="{{ $teacher->user->email }}" 
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200">
                                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                        <input name="phone" type="text" value="{{ $teacher->phone }}" 
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200">
                                        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                        <input name="dateofbirth" id="datepicker-te" type="text" value="{{ $teacher->dateofbirth }}" autocomplete="off"
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200">
                                        @error('dateofbirth')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                        <div class="flex items-center space-x-6 mt-1">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="gender" value="male" {{ ($teacher->gender == 'male') ? 'checked' : '' }} 
                                                       class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                                                <span class="ml-2 text-sm text-gray-700">Male</span>
                                            </label>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="gender" value="female" {{ ($teacher->gender == 'female') ? 'checked' : '' }} 
                                                       class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                                                <span class="ml-2 text-sm text-gray-700">Female</span>
                                            </label>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="gender" value="other" {{ ($teacher->gender == 'other') ? 'checked' : '' }} 
                                                       class="w-4 h-4 text-emerald-600 border-gray-300 focus:ring-emerald-500">
                                                <span class="ml-2 text-sm text-gray-700">Other</span>
                                            </label>
                                        </div>
                                        @error('gender')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Address Information Card -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Address Information
                                </h3>
                            </div>
                            <div class="px-6 py-6 space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Address</label>
                                    <input name="current_address" type="text" value="{{ $teacher->current_address }}" 
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200">
                                    @error('current_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Permanent Address</label>
                                    <input name="permanent_address" type="text" value="{{ $teacher->permanent_address }}" 
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200">
                                    @error('permanent_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-4 pt-4">
                            <a href="{{ route('teacher.index') }}" class="px-6 py-3 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200">
                                Cancel
                            </a>
                            <button type="submit" class="px-8 py-3 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-600 text-gray-700  font-semibold shadow-lg hover:from-emerald-600 hover:to-teal-700 hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Teacher
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
        $("#datepicker-te").datepicker({ dateFormat: 'yy-mm-dd' });
    })
</script>
@endpush