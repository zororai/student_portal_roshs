@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-purple-50 to-pink-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-600 via-violet-600 to-indigo-600 px-8 py-6">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-2">{{ $classes->class_name }}</h1>
                            <p class="text-purple-100 text-sm flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                                {{ $classes->students->count() }} {{ Str::plural('Student', $classes->students->count()) }}
                            </p>
                        </div>
                        
                        @if($lastRecord)
                        <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-xl px-6 py-3 shadow-lg border-2 border-white">
                            <p class="text-white text-sm font-bold">Current Period</p>
                            <p class="text-white text-xs mt-1 font-semibold">{{ ucfirst($lastRecord->result_period) }} Term {{ $lastRecord->year }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Status Banner -->
                <div class="px-8 py-4 {{ $exists ? 'bg-green-50 border-l-4 border-green-500' : 'bg-amber-50 border-l-4 border-amber-500' }}">
                    <div class="flex items-center">
                        @if($exists)
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-green-800 font-semibold">Results Available</p>
                                <p class="text-green-600 text-sm">Results exist for the latest period. You can view or update them.</p>
                            </div>
                        @else
                            <svg class="w-6 h-6 text-amber-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="text-amber-800 font-semibold">No Results Found</p>
                                <p class="text-amber-600 text-sm">No results found for the latest period. Please add results for your students.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($classes->students as $student)
                @php
                    $studentResult = $results->where('student_id', $student->id)->first();
                @endphp
                
                <div class="group bg-white rounded-xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-purple-300 transform hover:-translate-y-1">
                    <!-- Student Card Header -->
                    <div class="bg-gradient-to-br from-purple-500 via-violet-500 to-indigo-500 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-full p-2">
                                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                            
                            @if($studentResult)
                                <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full flex items-center">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Entered
                                </span>
                            @else
                                <span class="px-3 py-1 bg-amber-500 text-white text-xs font-bold rounded-full">
                                    Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Student Card Body -->
                    <div class="px-6 py-5">
                        <h3 class="text-lg font-bold text-gray-800 mb-3 line-clamp-2 min-h-[3.5rem]">
                            {{ $student->user->name }}
                        </h3>
                        
                        @if($student->roll_number)
                            <div class="flex items-center text-sm text-gray-500 mb-4">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                Roll No: <span class="font-semibold text-gray-700 ml-1">{{ $student->roll_number }}</span>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="space-y-2">
                            @if($studentResult)
                                {{-- Student has results - show View button --}}
                                <a href="{{ route('student.results', $student->id) }}" 
                                   class="flex items-center justify-center w-full bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    View Results
                                </a>
                            @else
                                {{-- Student doesn't have results - show Add button --}}
                                <a href="{{ route('results.studentsubject', $student->id) }}" 
                                   class="flex items-center justify-center w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Result
                                </a>
                            @endif
                        </div>
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

        <!-- Back Button -->
        <div class="mt-8 flex justify-center">
            <a href="{{ url()->previous() }}" 
               class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border border-gray-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>
</div>
@endsection