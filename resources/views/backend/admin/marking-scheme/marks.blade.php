@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $assessment->subject->name ?? 'Assessment' }} - {{ $assessment->topic }}</h1>
                    <p class="mt-2 text-sm text-gray-600">View student marks for this assessment</p>
                </div>
                <a href="{{ route('admin.marking-scheme.assessments', $assessment->class_id) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Assessments
                </a>
            </div>
        </div>

        <!-- Assessment Info -->
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-600">Subject</p>
                    <p class="text-lg font-bold text-gray-900">{{ $assessment->subject->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Topic</p>
                    <p class="text-lg font-bold text-gray-900">{{ $assessment->topic }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Class</p>
                    <p class="text-lg font-bold text-gray-900">{{ $assessment->class->class_name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Date</p>
                    <p class="text-lg font-bold text-gray-900">{{ $assessment->date ? $assessment->date->format('D, d M Y') : 'N/A' }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 pt-4 border-t border-blue-200">
                <div>
                    <p class="text-sm font-medium text-gray-600">Assessment Type</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold mt-1
                        @if($assessment->assessment_type == 'exam') bg-red-100 text-red-800
                        @elseif($assessment->assessment_type == 'test') bg-yellow-100 text-yellow-800
                        @elseif($assessment->assessment_type == 'quiz') bg-green-100 text-green-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ ucfirst($assessment->assessment_type ?? 'Assessment') }}
                    </span>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Teacher</p>
                    <p class="text-lg font-bold text-gray-900">{{ $assessment->teacher->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Students</p>
                    <p class="text-lg font-bold text-gray-900">{{ $students->count() }}</p>
                </div>
            </div>
        </div>

        <!-- Papers Info -->
        @if($assessment->papers && count($assessment->papers) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Assessment Papers</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($assessment->papers as $index => $paper)
                        @if(isset($paper['name']) && isset($paper['total_marks']))
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <p class="text-sm font-bold text-gray-900">{{ $paper['name'] }}</p>
                                <p class="text-xs text-gray-600 mt-1">Total Marks: {{ $paper['total_marks'] ?? $paper['weight'] ?? 'N/A' }}</p>
                                @if(isset($paper['weight']))
                                    <p class="text-xs text-gray-600">Weight: {{ $paper['weight'] }}</p>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Student Marks Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sticky left-0 bg-gray-50">
                                #
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Student Name
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Roll Number
                            </th>
                            @if($assessment->papers && count($assessment->papers) > 0)
                                @foreach($assessment->papers as $index => $paper)
                                    @if(isset($paper['name']))
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider border-l border-gray-200">
                                            {{ $paper['name'] }}<br>
                                            <span class="text-gray-500 font-normal">(Out of {{ $paper['total_marks'] ?? $paper['weight'] ?? 'N/A' }})</span>
                                        </th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider border-l border-gray-200">
                                            Comment
                                        </th>
                                    @endif
                                @endforeach
                            @else
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider border-l border-gray-200">
                                    Mark
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider border-l border-gray-200">
                                    Comment
                                </th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($students as $index => $student)
                            @php
                                $studentMarks = $marks->get($student->id, collect());
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 sticky left-0 bg-white">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200" src="{{ asset('images/profile/' . ($student->user->profile_picture ?? 'avatar.png')) }}" alt="{{ $student->user->name ?? 'Student' }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ $student->user->name ?? 'Unknown' }}</div>
                                            <div class="text-xs text-gray-500">{{ $student->user->email ?? '' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                        {{ $student->roll_number ?? 'N/A' }}
                                    </span>
                                </td>
                                @if($assessment->papers && count($assessment->papers) > 0)
                                    @foreach($assessment->papers as $paperIndex => $paper)
                                        @if(isset($paper['name']))
                                            @php
                                                $paperMark = $studentMarks->where('paper_index', $paperIndex)->first();
                                            @endphp
                                            <td class="px-6 py-4 whitespace-nowrap text-center border-l border-gray-200">
                                                @if($paperMark)
                                                    @if($paperMark->absence_reason)
                                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            {{ $paperMark->absence_reason }}
                                                        </span>
                                                    @else
                                                        <span class="text-lg font-bold {{ $paperMark->mark >= ($paper['total_marks'] ?? $paper['weight'] ?? 100) * 0.5 ? 'text-green-600' : 'text-red-600' }}">
                                                            {{ $paperMark->mark }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 border-l border-gray-200">
                                                <div class="text-xs text-gray-600 max-w-xs truncate" title="{{ $paperMark->comment ?? '' }}">
                                                    {{ $paperMark->comment ?? '-' }}
                                                </div>
                                            </td>
                                        @endif
                                    @endforeach
                                @else
                                    @php
                                        $singleMark = $studentMarks->first();
                                    @endphp
                                    <td class="px-6 py-4 whitespace-nowrap text-center border-l border-gray-200">
                                        @if($singleMark)
                                            <span class="text-lg font-bold text-gray-900">{{ $singleMark->mark }}</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 border-l border-gray-200">
                                        <div class="text-xs text-gray-600">{{ $singleMark->comment ?? '-' }}</div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <p class="text-gray-500 text-lg font-medium">No students found</p>
                                        <p class="text-gray-400 text-sm mt-1">No students are enrolled in this class</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Statistics Summary -->
        @if($students->count() > 0 && $marks->count() > 0)
            <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Assessment Statistics</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    @php
                        $allMarks = $marks->flatten()->where('mark', '!=', null);
                        $totalStudentsWithMarks = $marks->count();
                        $avgMark = $allMarks->avg('mark');
                        $maxMark = $allMarks->max('mark');
                        $minMark = $allMarks->min('mark');
                    @endphp
                    <div class="bg-blue-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-blue-600">Students with Marks</p>
                        <p class="text-2xl font-bold text-blue-900">{{ $totalStudentsWithMarks }} / {{ $students->count() }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-green-600">Average Mark</p>
                        <p class="text-2xl font-bold text-green-900">{{ number_format($avgMark ?? 0, 1) }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-purple-600">Highest Mark</p>
                        <p class="text-2xl font-bold text-purple-900">{{ $maxMark ?? 'N/A' }}</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-orange-600">Lowest Mark</p>
                        <p class="text-2xl font-bold text-orange-900">{{ $minMark ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
