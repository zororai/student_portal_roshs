@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-2">{{ $classes->class_name }}</h1>
                            <p class="text-blue-100 text-sm flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                                {{ $classes->students->count() }} {{ Str::plural('Student', $classes->students->count()) }}
                            </p>
                        </div>
                        <div class="hidden sm:block">
                            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl px-6 py-3">
                                <p class="text-white text-sm font-medium">Student Results</p>
                                <p class="text-blue-100 text-xs mt-1">View individual performance</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Grid -->
        @if($classes->students->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($classes->students as $student)
                    @php
                        $studentResult = $results->where('student_id', $student->id)->first();
                    @endphp
                    
                    <div class="group bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-300 transform hover:-translate-y-1">
                        <!-- Student Card Header -->
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 px-6 py-4">
                            <div class="flex items-center justify-center">
                                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-full p-3">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Student Card Body -->
                        <div class="px-6 py-5">
                            <h3 class="text-lg font-bold text-gray-800 text-center mb-1 line-clamp-2 min-h-[3.5rem] flex items-center justify-center">
                                {{ $student->user->name }}
                            </h3>
                            
                            @if($student->roll_number)
                                <p class="text-sm text-gray-500 text-center mb-4">
                                    Roll No: <span class="font-semibold text-gray-700">{{ $student->roll_number }}</span>
                                </p>
                            @else
                                <div class="mb-4 h-5"></div>
                            @endif

                            <!-- Action Button -->
                            <a href="{{ route('adminresults.yearsubject', $student->id) }}" 
                               class="flex items-center justify-center w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg group-hover:scale-105 transform">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View Results
                            </a>
                        </div>

                        <!-- Card Footer -->
                        <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                            <div class="flex items-center justify-center text-xs text-gray-500">
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                Academic Records
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <div class="flex flex-col items-center justify-center">
                    <div class="bg-gray-100 rounded-full p-6 mb-4">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-700 mb-2">No Students Found</h3>
                    <p class="text-gray-500 max-w-md">
                        There are currently no students enrolled in this class. Students will appear here once they are added to {{ $classes->class_name }}.
                    </p>
                </div>
            </div>
        @endif

        <!-- Back Button -->
        <div class="mt-8 flex justify-center">
            <a href="{{ url()->previous() }}" 
               class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border border-gray-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Classes
            </a>
        </div>
    </div>
</div>
@endsection