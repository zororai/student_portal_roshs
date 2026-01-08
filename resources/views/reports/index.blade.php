@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 -m-6 p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Academic Results</h1>
                    <p class="text-gray-500 mt-1">View your child's academic performance</p>
                </div>
            </div>
            <button onclick="printResults()" class="mt-4 sm:mt-0 inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg font-semibold">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Results
            </button>
        </div>

        @if(isset($error))
        <div class="bg-red-50 border border-red-200 rounded-2xl p-6 mb-6">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <p class="text-red-700 font-medium">{{ $error }}</p>
            </div>
        </div>
        @endif

        <div id="printArea">
            <!-- Print Header (hidden on screen) -->
            <div class="hidden print:block text-center mb-8">
                <img src="{{ asset('images/logo.png') }}" class="mx-auto h-24 mb-4">
                <h1 class="text-2xl font-bold">Academic Results Report</h1>
            </div>

            @if(isset($students) && $students->count() > 0)
                @foreach($students as $student)
                    @php
                        $studentResults = $results->where('student_id', $student->id);
                        $groupedResults = $studentResults->groupBy(function($item) {
                            return $item->year . ' - ' . ucfirst($item->result_period);
                        });
                        $totalMarks = $studentResults->sum('marks');
                        $avgMark = $studentResults->count() > 0 ? round($totalMarks / $studentResults->count()) : 0;
                    @endphp

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
                        <!-- Student Header -->
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center mr-4">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-bold">{{ $student->user->name ?? 'Unknown Student' }}</h2>
                                        <div class="flex items-center gap-3 mt-1">
                                            <span class="px-3 py-1 bg-white/20 rounded-full text-sm font-medium">{{ $student->class->class_name ?? 'No Class' }}</span>
                                            @if($studentResults->count() > 0)
                                            <span class="text-indigo-100 text-sm">{{ $studentResults->count() }} subjects</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @if($studentResults->count() > 0)
                                <div class="text-right">
                                    <div class="text-3xl font-bold">{{ $avgMark }}%</div>
                                    <div class="text-indigo-200 text-sm">Average Score</div>
                                </div>
                                @endif
                            </div>
                        </div>

                        @if($studentResults->count() > 0)
                            <div class="p-6">
                                @foreach($groupedResults as $period => $periodResults)
                                    @php
                                        $periodAvg = $periodResults->count() > 0 ? round($periodResults->sum('marks') / $periodResults->count()) : 0;
                                    @endphp
                                    <div class="mb-6 last:mb-0">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-lg font-bold text-gray-800 flex items-center">
                                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                                {{ $period }}
                                            </h3>
                                            <span class="px-4 py-2 bg-gradient-to-r {{ $periodAvg >= 75 ? 'from-green-500 to-emerald-500' : ($periodAvg >= 50 ? 'from-yellow-500 to-orange-500' : 'from-red-500 to-pink-500') }} text-white rounded-xl font-bold text-sm">
                                                Term Average: {{ $periodAvg }}%
                                            </span>
                                        </div>
                                        
                                        <div class="bg-gray-50 rounded-xl overflow-hidden">
                                            <table class="min-w-full">
                                                <thead>
                                                    <tr class="bg-gray-100">
                                                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Subject</th>
                                                        <th class="text-center py-3 px-4 font-semibold text-gray-600 text-sm">Score</th>
                                                        <th class="text-center py-3 px-4 font-semibold text-gray-600 text-sm">Grade</th>
                                                        <th class="text-left py-3 px-4 font-semibold text-gray-600 text-sm">Comment</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200">
                                                    @foreach($periodResults as $result)
                                                        <tr class="hover:bg-white transition-colors">
                                                            <td class="py-3 px-4">
                                                                <div class="flex items-center">
                                                                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center mr-3">
                                                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                                        </svg>
                                                                    </div>
                                                                    <span class="font-medium text-gray-800">{{ $result->subject->name ?? 'N/A' }}</span>
                                                                </div>
                                                            </td>
                                                            <td class="py-3 px-4 text-center">
                                                                <span class="text-lg font-bold {{ $result->marks >= 75 ? 'text-green-600' : ($result->marks >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                                                    {{ $result->marks }}%
                                                                </span>
                                                            </td>
                                                            <td class="py-3 px-4 text-center">
                                                                <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-sm font-bold
                                                                    @if($result->mark_grade == 'A') bg-green-100 text-green-700
                                                                    @elseif($result->mark_grade == 'B') bg-blue-100 text-blue-700
                                                                    @elseif($result->mark_grade == 'C') bg-yellow-100 text-yellow-700
                                                                    @elseif($result->mark_grade == 'D') bg-orange-100 text-orange-700
                                                                    @else bg-red-100 text-red-700
                                                                    @endif">
                                                                    {{ $result->mark_grade ?? 'N/A' }}
                                                                </span>
                                                            </td>
                                                            <td class="py-3 px-4 text-gray-600 text-sm">{{ $result->comment ?? '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-12 text-center">
                                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">No Results Available</h3>
                                <p class="text-gray-500">No academic results have been published for this student yet.</p>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                @if($results->count() > 0)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6">
                            <div class="grid gap-4">
                                @foreach($results as $studentResult)
                                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $studentResult->subject->name ?? 'N/A' }}</p>
                                                <p class="text-sm text-gray-500">{{ ucfirst($studentResult->result_period ?? 'N/A') }} {{ $studentResult->year ?? '' }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <div class="text-right">
                                                <p class="text-lg font-bold {{ $studentResult->marks >= 75 ? 'text-green-600' : ($studentResult->marks >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $studentResult->marks }}%</p>
                                                <p class="text-xs text-gray-500">Score</p>
                                            </div>
                                            <span class="inline-flex items-center justify-center w-10 h-10 rounded-xl text-sm font-bold
                                                @if($studentResult->mark_grade == 'A') bg-green-100 text-green-700
                                                @elseif($studentResult->mark_grade == 'B') bg-blue-100 text-blue-700
                                                @elseif($studentResult->mark_grade == 'C') bg-yellow-100 text-yellow-700
                                                @else bg-red-100 text-red-700
                                                @endif">
                                                {{ $studentResult->mark_grade ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">No Results Found</h3>
                        <p class="text-gray-500">Academic results will appear here once they are published.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        #printArea, #printArea * {
            visibility: visible;
        }
        #printArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .bg-gradient-to-r {
            background: #4f46e5 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>

<!-- JavaScript for Printing -->
<script>
    function printResults() {
        window.print();
    }
</script>

@endsection
