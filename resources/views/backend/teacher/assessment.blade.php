@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Student Assessment</h1>
                    <p class="mt-2 text-sm text-gray-600">Assess and evaluate student performance in your classes</p>
                </div>
            </div>
        </div>

        <!-- Classes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($classes as $class)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 bg-blue-100 rounded-lg">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $class->class_name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $class->students_count }} Students</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 space-y-2">
                            <a href="{{ route('teacher.assessment.list', $class->id) }}" class="block w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg text-center transition-colors">
                                Create Assessment
                            </a>
                            <a href="{{ route('teacher.assessment.marks', $class->id) }}" class="block w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg text-center transition-colors">
                                Add Assessment Marks
                            </a>
                            <a href="{{ route('teacher.assessment.marking.scheme', $class->id) }}" class="block w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-lg text-center transition-colors">
                                Assessment Marking Scheme
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">No classes assigned</p>
                        <p class="text-gray-400 text-sm mt-1">You don't have any classes assigned to you yet</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Recent Assessments Section -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Recent Assessments</h2>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                @forelse($recentAssessments as $assessment)
                    <div class="p-4 border-b border-gray-200 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        @if($assessment->assessment_type == 'Quiz') bg-blue-100 text-blue-800
                                        @elseif($assessment->assessment_type == 'Test') bg-purple-100 text-purple-800
                                        @elseif($assessment->assessment_type == 'Assignment') bg-green-100 text-green-800
                                        @elseif($assessment->assessment_type == 'Exam') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ $assessment->assessment_type }}
                                    </span>
                                    <h3 class="text-sm font-semibold text-gray-900">{{ $assessment->topic }}</h3>
                                </div>
                                <div class="mt-1 flex items-center space-x-4 text-xs text-gray-500">
                                    <span>{{ $assessment->subject->name ?? 'N/A' }}</span>
                                    <span>•</span>
                                    <span>{{ $assessment->class->class_name ?? 'N/A' }}</span>
                                    <span>•</span>
                                    <span>{{ $assessment->date ? $assessment->date->format('M d, Y') : 'N/A' }}</span>
                                </div>
                            </div>
                            <a href="{{ route('teacher.assessment.list', $assessment->class_id) }}" class="ml-4 inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                                View
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <p class="text-sm">No recent assessments</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
