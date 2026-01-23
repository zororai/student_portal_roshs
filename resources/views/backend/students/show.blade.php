@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Student Profile</h1>
                    <p class="mt-1 text-sm text-gray-500">View complete student information and enrolled subjects</p>
                </div>
                <a href="{{ route('student.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Students
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Profile Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <!-- Profile Header with Gradient -->
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8">
                            <div class="flex flex-col items-center">
                                <img class="w-28 h-28 rounded-full border-4 border-white shadow-lg object-cover" 
                                     src="{{ asset('images/profile/' . $student->user->profile_picture) }}" 
                                     alt="{{ $student->user->name }}">
                                <h2 class="mt-4 text-xl font-bold text-white">{{ $student->user->name }}</h2>
                                <span class="mt-1 inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 text-white backdrop-blur-sm">
                                    {{ $student->roll_number }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Quick Info -->
                        <div class="px-6 py-5">
                            <div class="space-y-4">
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm">{{ $student->user->email }}</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span class="text-sm">{{ $student->phone }}</span>
                                </div>
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="text-sm font-medium">{{ $student->class->class_name ?? 'No class assigned' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details Section -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Personal Information Card -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Personal Information
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                                <div class="bg-gray-50 rounded-lg px-4 py-3">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ ucfirst($student->gender) }}</dd>
                                </div>
                                <div class="bg-gray-50 rounded-lg px-4 py-3">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Birth</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($student->dateofbirth)->format('M d, Y') }}</dd>
                                </div>
                                <div class="bg-gray-50 rounded-lg px-4 py-3">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Student Type</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($student->student_type ?? 'day') == 'boarding' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ ucfirst($student->student_type ?? 'Day') }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="bg-gray-50 rounded-lg px-4 py-3">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Student Status</dt>
                                    <dd class="mt-1">
                                        @if($student->is_new_student)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                                                </svg>
                                                New Student
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                Existing Student
                                            </span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="bg-gray-50 rounded-lg px-4 py-3">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Curriculum</dt>
                                    <dd class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ ($student->curriculum_type ?? 'zimsec') == 'cambridge' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                                            {{ strtoupper($student->curriculum_type ?? 'ZIMSEC') }}
                                        </span>
                                    </dd>
                                </div>
                                <div class="bg-gray-50 rounded-lg px-4 py-3">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Scholarship</dt>
                                    <dd class="mt-1">
                                        @if(($student->scholarship_percentage ?? 0) > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                </svg>
                                                {{ $student->scholarship_percentage }}%
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500">None</span>
                                        @endif
                                    </dd>
                                </div>
                                @if($student->national_id)
                                <div class="bg-gray-50 rounded-lg px-4 py-3">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">National ID</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $student->national_id }}</dd>
                                </div>
                                @endif
                                @if($student->created_at)
                                <div class="bg-gray-50 rounded-lg px-4 py-3">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Enrollment Date</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $student->created_at->format('M d, Y') }}</dd>
                                </div>
                                @endif
                                <div class="bg-gray-50 rounded-lg px-4 py-3 sm:col-span-2">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Current Address</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $student->current_address ?: 'Not provided' }}</dd>
                                </div>
                                <div class="bg-gray-50 rounded-lg px-4 py-3 sm:col-span-2">
                                    <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider">Permanent Address</dt>
                                    <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $student->permanent_address ?: 'Not provided' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Parents/Guardians Card -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Parents / Guardians
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            @if($student->parents->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @foreach($student->parents as $index => $parent)
                                        <div class="relative rounded-xl border-2 p-5 transition-all duration-200 hover:shadow-md {{ $parent->registration_completed ? 'border-green-200 bg-gradient-to-br from-green-50 to-emerald-50' : 'border-amber-200 bg-gradient-to-br from-amber-50 to-yellow-50' }}">
                                            <!-- Status Badge -->
                                            <div class="absolute -top-2 -right-2">
                                                @if($parent->registration_completed)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-500 text-white shadow-sm">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Verified
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500 text-white shadow-sm">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Pending
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <div class="flex items-center mb-3">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $parent->registration_completed ? 'from-green-400 to-emerald-500' : 'from-amber-400 to-yellow-500' }} flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                                    {{ strtoupper(substr($parent->user->name, 0, 1)) }}
                                                </div>
                                                <div class="ml-3">
                                                    <h4 class="font-semibold text-gray-900">{{ $parent->user->name }}</h4>
                                                    <p class="text-xs text-gray-500">Parent #{{ $index + 1 }}</p>
                                                </div>
                                            </div>
                                            
                                            <div class="space-y-2 text-sm">
                                                <div class="flex items-center text-gray-600">
                                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                                    </svg>
                                                    {{ $parent->phone }}
                                                </div>
                                                @if($parent->registration_completed)
                                                    <div class="flex items-center text-gray-600">
                                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                        </svg>
                                                        {{ $parent->user->email }}
                                                    </div>
                                                @else
                                                    <div class="flex items-start text-amber-700 bg-amber-100 rounded-lg p-2 mt-2">
                                                        <svg class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                        </svg>
                                                        <span class="text-xs">Awaiting registration via SMS</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No parents linked to this student</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Subjects Card -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Enrolled Subjects
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            @if(($class->subjects ?? collect())->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Code</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subject</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Teacher</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach ($class->subjects ?? [] as $subject)
                                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        {{ $subject->subject_code }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $subject->name }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-xs font-bold">
                                                            {{ strtoupper(substr($subject->teacher->user->name ?? 'N', 0, 1)) }}
                                                        </div>
                                                        <span class="ml-3 text-sm text-gray-600">{{ $subject->teacher->user->name ?? 'Not assigned' }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No subjects assigned to this class</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Payment History Card -->
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Payment History
                                @if(isset($payments) && $payments->count() > 0)
                                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        ${{ number_format($payments->sum('amount_paid'), 2) }} Total
                                    </span>
                                @endif
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            @if(isset($payments) && $payments->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Term</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reference</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach ($payments as $payment)
                                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                    {{ $payment->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        {{ ucfirst($payment->resultsStatus->result_period ?? 'N/A') }} {{ $payment->resultsStatus->year ?? '' }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="text-sm font-bold text-green-600">${{ number_format($payment->amount_paid, 2) }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                    {{ ucfirst($payment->payment_method ?? 'Cash') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $payment->reference ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-12">
                                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No payment records found</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
