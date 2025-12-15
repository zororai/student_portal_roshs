@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Student Assessment Records</h1>
                    <p class="mt-2 text-sm text-gray-600">View performance by assessment type for each subject</p>
                </div>
            </div>
        </div>

        <!-- Performance by Assessment Type per Subject -->
        @if(isset($subjectPerformance) && count($subjectPerformance) > 0)
            <div class="space-y-8">
                @foreach($subjectPerformance as $subjectData)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <!-- Subject Header -->
                        <div class="bg-gradient-to-r from-indigo-500 to-blue-600 px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-white">{{ $subjectData['subject']->name }}</h3>
                                    <p class="text-indigo-100 text-sm">Performance by Assessment Type</p>
                                </div>
                            </div>
                        </div>

                        <!-- Assessment Type Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Assessment Type</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Taken</th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Performance</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($subjectData['stats'] as $stat)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $stat['type'] }}</td>
                                            <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $stat['taken'] }}</td>
                                            <td class="px-6 py-4 text-center">
                                                @if($stat['taken'] > 0 && $stat['performance'] > 0)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $stat['performance'] >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $stat['performance'] }}%
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">--</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-gray-500 text-lg font-medium">No subjects assigned</p>
                <p class="text-gray-400 text-sm mt-1">You don't have any subjects assigned to you yet</p>
            </div>
        @endif

        <!-- Quick Actions -->
        @if(count($classes) > 0)
            <div class="mt-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($classes as $class)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-4">
                                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $class->class_name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $class->students_count }} Students</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <a href="{{ route('teacher.assessment.list', $class->id) }}" class="block w-full px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg text-center transition-colors">
                                    Create Assessment
                                </a>
                                <a href="{{ route('teacher.assessment.marks', $class->id) }}" class="block w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg text-center transition-colors">
                                    Add Marks
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Recent Assessments Section -->
        @if(count($recentAssessments) > 0)
            <div class="mt-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Recent Assessments</h2>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    @foreach($recentAssessments as $assessment)
                        <div class="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors last:border-b-0">
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
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
