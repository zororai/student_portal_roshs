@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 -m-6 p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Student Assessments</h1>
                    <p class="text-gray-500 mt-1">View your child's assessment records</p>
                </div>
            </div>
            <a href="{{ route('home') }}" class="mt-4 sm:mt-0 inline-flex items-center px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>

        @if(isset($error))
        <!-- Error State -->
        <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-6">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-red-700 font-medium">{{ $error }}</p>
            </div>
        </div>
        @endif

        @if($students->isNotEmpty())
        <!-- Student Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            @foreach($students as $student)
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-indigo-200 rounded-xl flex items-center justify-center">
                        <span class="text-lg font-bold text-indigo-600">{{ substr($student->user->name ?? 'S', 0, 1) }}</span>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $student->user->name ?? 'Unknown' }}</h3>
                        <p class="text-sm text-gray-500">{{ $student->class->class_name ?? 'No Class' }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @if(isset($assessmentMarks) && $assessmentMarks->count() > 0)
        <!-- Assessment Records -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">Assessment Records</h2>
                    <span class="text-sm text-gray-500">{{ $assessmentMarks->count() }} records</span>
                </div>
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Subject</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Assessment</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Paper</th>
                            <th class="text-center py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Mark</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($assessmentMarks as $mark)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-6">
                                <p class="font-medium text-gray-800">{{ $mark->student->user->name ?? 'Unknown' }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-100 text-indigo-700">
                                    {{ $mark->assessment->subject->subject_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $mark->assessment->topic ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-400">{{ ucfirst($mark->assessment->assessment_type ?? '') }}</p>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-gray-700">{{ $mark->paper_name ?? 'Paper ' . ($mark->paper_index + 1) }}</p>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @php
                                    $percentage = $mark->total_marks > 0 ? ($mark->mark / $mark->total_marks) * 100 : 0;
                                    $colorClass = $percentage >= 75 ? 'bg-emerald-100 text-emerald-700' : ($percentage >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
                                @endphp
                                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ $colorClass }}">
                                    {{ number_format($mark->mark, 1) }}/{{ number_format($mark->total_marks, 0) }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-gray-600">{{ $mark->assessment->date ? $mark->assessment->date->format('d M Y') : 'N/A' }}</p>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-gray-100">
                @foreach ($assessmentMarks as $mark)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="font-medium text-gray-800">{{ $mark->assessment->topic ?? 'Assessment' }}</p>
                            <p class="text-sm text-gray-500">{{ $mark->assessment->subject->subject_name ?? 'N/A' }}</p>
                        </div>
                        @php
                            $percentage = $mark->total_marks > 0 ? ($mark->mark / $mark->total_marks) * 100 : 0;
                            $colorClass = $percentage >= 75 ? 'bg-emerald-100 text-emerald-700' : ($percentage >= 50 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700');
                        @endphp
                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold {{ $colorClass }}">
                            {{ number_format($mark->mark, 1) }}/{{ number_format($mark->total_marks, 0) }}
                        </span>
                    </div>
                    <div class="flex items-center text-sm text-gray-600 space-x-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ $mark->student->user->name ?? 'Unknown' }}
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $mark->assessment->date ? $mark->assessment->date->format('d M Y') : 'N/A' }}
                        </div>
                    </div>
                    @if($mark->comment)
                    <div class="mt-2 text-sm text-gray-500 bg-gray-50 rounded-lg p-2">
                        <strong>Comment:</strong> {{ $mark->comment }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        @else
        <!-- Empty State -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12">
            <div class="text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Assessment Records</h3>
                <p class="text-gray-500 max-w-sm mx-auto">There are no assessment records found for your child yet. Records will appear here once assessments are marked.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
