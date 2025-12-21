@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Parent</h1>
                    <p class="mt-1 text-sm text-gray-500">Update parent information and linked students</p>
                </div>
                <a href="{{ route('parents.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Parents
                </a>
            </div>

            <form action="{{ route('parents.update', $parent->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Profile Card -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden sticky top-8">
                            <div class="bg-gradient-to-r from-orange-500 to-amber-600 px-6 py-8">
                                <div class="flex flex-col items-center">
                                    <div class="relative group">
                                        <img id="preview-image" class="w-28 h-28 rounded-full border-4 border-white shadow-lg object-cover" 
                                             src="{{ asset('images/profile/' . $parent->user->profile_picture) }}" 
                                             alt="{{ $parent->user->name }}">
                                        <label for="profile_picture" class="absolute inset-0 flex items-center justify-center bg-black/50 rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity duration-200">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </label>
                                        <input type="file" name="profile_picture" id="profile_picture" class="hidden" accept="image/*" onchange="previewImage(this)">
                                    </div>
                                    <h2 class="mt-4 text-xl font-bold text-white">{{ $parent->user->name }}</h2>
                                    <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                        </svg>
                                        Parent/Guardian
                                    </span>
                                </div>
                            </div>
                            <div class="px-6 py-5">
                                <div class="space-y-3">
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-sm truncate">{{ $parent->user->email }}</span>
                                    </div>
                                    <div class="flex items-center text-gray-600">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        <span class="text-sm">{{ $parent->phone }}</span>
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
                                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Basic Information
                                </h3>
                            </div>
                            <div class="px-6 py-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                        <input name="name" type="text" value="{{ $parent->user->name }}" 
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200">
                                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                        <input name="email" type="email" value="{{ $parent->user->email }}" 
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200">
                                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                        <input name="phone" type="text" value="{{ $parent->phone }}" 
                                               class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200">
                                        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender</label>
                                        <div class="flex items-center space-x-6 mt-3">
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="gender" value="male" {{ ($parent->gender == 'male') ? 'checked' : '' }} 
                                                       class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                <span class="ml-2 text-sm text-gray-700">Male</span>
                                            </label>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="gender" value="female" {{ ($parent->gender == 'female') ? 'checked' : '' }} 
                                                       class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
                                                <span class="ml-2 text-sm text-gray-700">Female</span>
                                            </label>
                                            <label class="inline-flex items-center cursor-pointer">
                                                <input type="radio" name="gender" value="other" {{ ($parent->gender == 'other') ? 'checked' : '' }} 
                                                       class="w-4 h-4 text-orange-600 border-gray-300 focus:ring-orange-500">
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
                                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Address Information
                                </h3>
                            </div>
                            <div class="px-6 py-6 space-y-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Address</label>
                                    <input name="current_address" type="text" value="{{ $parent->current_address }}" 
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200">
                                    @error('current_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Permanent Address</label>
                                    <input name="permanent_address" type="text" value="{{ $parent->permanent_address }}" 
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200">
                                    @error('permanent_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Students Card -->
                        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    Linked Students
                                </h3>
                            </div>
                            <div class="px-6 py-6">
                                @if($students->count() > 0)
                                    <p class="text-sm text-gray-500 mb-4">Select the students linked to this parent:</p>
                                    @php $currentStudentIds = $parent->students->pluck('id')->toArray(); @endphp
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-64 overflow-y-auto">
                                        @foreach($students as $student)
                                            <label class="flex items-center p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 hover:border-orange-300 hover:bg-orange-50 {{ in_array($student->id, old('student_ids', $currentStudentIds)) ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                                                <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                                       class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                                       {{ in_array($student->id, old('student_ids', $currentStudentIds)) ? 'checked' : '' }}>
                                                <img src="{{ asset('images/profile/'.$student->user->profile_picture) }}"
                                                     class="h-10 w-10 rounded-full object-cover ml-3"
                                                     alt="{{ $student->user->name }}">
                                                <div class="ml-3">
                                                    <p class="text-sm font-semibold text-gray-900">{{ $student->user->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $student->roll_number }} @if($student->class) • {{ $student->class->class_name }} @endif</p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        <p class="mt-2 text-sm text-gray-500">No students available</p>
                                    </div>
                                @endif
                                @error('student_ids')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-4 pt-4">
                            <a href="{{ route('parents.index') }}" class="px-6 py-3 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200">
                                Cancel
                            </a>
                            <button type="submit" class="px-8 py-3 rounded-lg bg-gradient-to-r from-orange-500 to-amber-600 text-white font-semibold shadow-lg hover:from-orange-600 hover:to-amber-700 hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Parent
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
                document.getElementById('preview-image').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
