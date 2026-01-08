@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-700 px-8 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-full p-3 mr-4">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-1">{{ $classes->class_name }}</h1>
                                <p class="text-blue-100 text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                    </svg>
                                    {{ $classes->students->count() }} {{ Str::plural('Student', $classes->students->count()) }}
                                </p>
                            </div>
                        </div>
                        <div class="hidden sm:block">
                            <div class="bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl px-6 py-3 shadow-lg border-2 border-white border-opacity-30">
                                <p class="text-white text-sm font-bold">Student Results</p>
                                <p class="text-emerald-50 text-xs mt-1 font-medium">View individual performance</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($isClassTeacher) && $isClassTeacher && $classes->students->count() > 0)
            <!-- Class Teacher Comprehensive View -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
                <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
                    <h2 class="text-xl font-bold text-white flex items-center">
                        <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Class Results Overview (Class Teacher View)
                    </h2>
                    <p class="text-green-100 text-sm mt-1">All subjects and marks for all students</p>
                </div>
                
                <!-- Filter Controls -->
                <div class="px-6 py-4 bg-gray-50 border-b flex flex-wrap gap-4 items-center">
                    <div>
                        <label class="text-sm font-medium text-gray-700 mr-2">Filter by Year/Term:</label>
                        <select id="filterYearTerm" onchange="filterResults()" class="rounded-lg border-gray-300 text-sm">
                            <option value="all">All Results</option>
                            @foreach($years as $yearTerm)
                                <option value="{{ $yearTerm->year }}-{{ $yearTerm->result_period }}">{{ $yearTerm->year }} - {{ ucfirst($yearTerm->result_period) }} Term</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="classResultsTable">
                        <thead class="bg-gradient-to-r from-blue-600 to-indigo-600">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wider sticky left-0 bg-blue-600 z-10">Student</th>
                                @foreach($subjects as $subject)
                                    <th class="px-4 py-3 text-center text-xs font-bold text-white uppercase tracking-wider">{{ $subject->name }}</th>
                                @endforeach
                                <th class="px-4 py-3 text-center text-xs font-bold text-white uppercase tracking-wider bg-indigo-700">Average</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($classes->students as $student)
                                @php
                                    $studentResults = $classResults[$student->id] ?? collect();
                                    $totalMarks = 0;
                                    $subjectCount = 0;
                                @endphp
                                <tr class="hover:bg-blue-50 result-row" data-student="{{ $student->id }}">
                                    <td class="px-4 py-3 whitespace-nowrap sticky left-0 bg-white z-10">
                                        <div class="flex items-center">
                                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $student->user->name }}</div>
                                                @if($student->roll_number)
                                                    <div class="text-xs text-gray-500">Roll: {{ $student->roll_number }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @foreach($subjects as $subject)
                                        @php
                                            $result = $studentResults->where('subject_id', $subject->id)->first();
                                            if($result && $result->marks !== null) {
                                                $totalMarks += $result->marks;
                                                $subjectCount++;
                                            }
                                        @endphp
                                        <td class="px-4 py-3 text-center result-cell" data-year="{{ $result->year ?? '' }}" data-term="{{ $result->result_period ?? '' }}">
                                            @if($result)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                                                    @if($result->marks >= 80) bg-green-100 text-green-800
                                                    @elseif($result->marks >= 60) bg-blue-100 text-blue-800
                                                    @elseif($result->marks >= 40) bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ $result->marks }}%
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="px-4 py-3 text-center bg-gray-50">
                                        @if($subjectCount > 0)
                                            @php $avg = round($totalMarks / $subjectCount, 1); @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold
                                                @if($avg >= 80) bg-green-500 text-white
                                                @elseif($avg >= 60) bg-blue-500 text-white
                                                @elseif($avg >= 40) bg-yellow-500 text-white
                                                @else bg-red-500 text-white
                                                @endif">
                                                {{ $avg }}%
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
                function filterResults() {
                    var filter = document.getElementById('filterYearTerm').value;
                    var cells = document.querySelectorAll('.result-cell');
                    
                    if (filter === 'all') {
                        cells.forEach(function(cell) {
                            cell.style.opacity = '1';
                        });
                    } else {
                        var parts = filter.split('-');
                        var year = parts[0];
                        var term = parts[1];
                        
                        cells.forEach(function(cell) {
                            if (cell.dataset.year == year && cell.dataset.term == term) {
                                cell.style.opacity = '1';
                            } else if (cell.dataset.year && cell.dataset.term) {
                                cell.style.opacity = '0.3';
                            }
                        });
                    }
                }
            </script>
        @endif

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
                            <a href="{{ route('results.yearsubject', $student->id) }}" 
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