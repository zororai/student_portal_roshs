@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
                Master Timetable
            </h1>
            <p class="text-gray-500 mt-1 ml-13">All class schedules on one sheet - {{ $academicYear }} Term {{ $term }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
            <a href="{{ route('admin.timetable.index') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Timetables
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" action="{{ route('admin.timetable.master') }}" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                <select name="year" class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                    @php $currentYear = date('Y'); @endphp
                    @for($y = $currentYear - 2; $y <= $currentYear + 1; $y++)
                        <option value="{{ $y }}" {{ $academicYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                <select name="term" class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500">
                    <option value="1" {{ $term == 1 ? 'selected' : '' }}>Term 1</option>
                    <option value="2" {{ $term == 2 ? 'selected' : '' }}>Term 2</option>
                    <option value="3" {{ $term == 3 ? 'selected' : '' }}>Term 3</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-xl font-semibold hover:bg-purple-700 transition-colors">
                Filter
            </button>
        </form>
    </div>

    @if(count($classTimetables) === 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">No timetables found for {{ $academicYear }} Term {{ $term }}</p>
            <p class="text-gray-400 text-sm mt-1">Generate timetables first to view the master schedule</p>
            <a href="{{ route('admin.timetable.create') }}" class="inline-flex items-center gap-2 mt-4 px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700 transition-colors">
                Generate Timetable
            </a>
        </div>
    @else
        <!-- Master Timetable Grid -->
        @foreach($days as $day)
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center text-white text-sm font-bold">
                        {{ substr($day, 0, 1) }}
                    </span>
                    {{ $day }}
                </h2>
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gradient-to-r from-purple-50 to-indigo-50">
                                    <th class="px-3 py-3 text-left font-semibold text-gray-700 border-b border-gray-200 sticky left-0 bg-purple-50 z-10 min-w-[100px]">
                                        Class
                                    </th>
                                    @foreach($allTimeSlots as $timeKey => $timeSlot)
                                        <th class="px-2 py-2 text-center font-medium text-gray-600 border-b border-gray-200 min-w-[90px]
                                            @if($timeSlot['slot_type'] === 'break') bg-amber-50
                                            @elseif($timeSlot['slot_type'] === 'lunch') bg-orange-50
                                            @endif">
                                            <div class="text-xs">
                                                {{ \Carbon\Carbon::parse($timeSlot['start_time'])->format('H:i') }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ \Carbon\Carbon::parse($timeSlot['end_time'])->format('H:i') }}
                                            </div>
                                            @if($timeSlot['slot_type'] === 'break')
                                                <span class="text-xs text-amber-600 font-semibold">BREAK</span>
                                            @elseif($timeSlot['slot_type'] === 'lunch')
                                                <span class="text-xs text-orange-600 font-semibold">LUNCH</span>
                                            @endif
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classTimetables as $classId => $data)
                                    <tr class="hover:bg-gray-50 border-b border-gray-100">
                                        <td class="px-3 py-2 font-semibold text-gray-800 sticky left-0 bg-white z-10 border-r border-gray-100">
                                            {{ $data['class']->class_name ?? $data['class']->name }}
                                        </td>
                                        @foreach($allTimeSlots as $timeKey => $timeSlot)
                                            @php
                                                $daySlots = $data['slots'][$day] ?? collect();
                                                $slot = $daySlots->first(function($s) use ($timeSlot) {
                                                    return $s->start_time == $timeSlot['start_time'] && $s->end_time == $timeSlot['end_time'];
                                                });
                                            @endphp
                                            <td class="px-1 py-1 text-center border-r border-gray-50
                                                @if($timeSlot['slot_type'] === 'break') bg-amber-50
                                                @elseif($timeSlot['slot_type'] === 'lunch') bg-orange-50
                                                @endif">
                                                @if($slot)
                                                    @if($slot->slot_type === 'break')
                                                        <span class="text-amber-600 text-xs font-medium">Break</span>
                                                    @elseif($slot->slot_type === 'lunch')
                                                        <span class="text-orange-600 text-xs font-medium">Lunch</span>
                                                    @elseif($slot->subject)
                                                        <div class="bg-gradient-to-br from-emerald-100 to-teal-100 rounded-lg px-1 py-1">
                                                            <div class="font-semibold text-emerald-800 text-xs truncate" title="{{ $slot->subject->name }}">
                                                                {{ \Illuminate\Support\Str::limit($slot->subject->name, 12) }}
                                                            </div>
                                                            @if($slot->teacher && $slot->teacher->user)
                                                                <div class="text-emerald-600 text-xs truncate" title="{{ $slot->teacher->user->name }}">
                                                                    {{ \Illuminate\Support\Str::limit($slot->teacher->user->name, 10) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-gray-400 text-xs">Free</span>
                                                    @endif
                                                @else
                                                    <span class="text-gray-300 text-xs">-</span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Legend -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mt-6">
            <h3 class="font-semibold text-gray-700 mb-3">Legend</h3>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-gradient-to-br from-emerald-100 to-teal-100 rounded"></div>
                    <span class="text-sm text-gray-600">Subject Lesson</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-amber-50 rounded border border-amber-200"></div>
                    <span class="text-sm text-gray-600">Break</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-orange-50 rounded border border-orange-200"></div>
                    <span class="text-sm text-gray-600">Lunch</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 bg-gray-100 rounded border border-gray-200"></div>
                    <span class="text-sm text-gray-600">Free Period</span>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    @page {
        size: A4 landscape;
        margin: 10mm;
    }
    
    @media print {
        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        html, body {
            width: 100%;
            height: auto;
            font-size: 7px !important;
            overflow: visible !important;
        }
        
        .no-print { display: none !important; }
        
        .container-fluid { 
            padding: 0 !important; 
            width: 100% !important;
            max-width: none !important;
        }
        
        .rounded-2xl { border-radius: 0 !important; }
        .shadow-sm, .shadow-lg, .shadow-xl { box-shadow: none !important; }
        
        /* Hide filter section and buttons when printing */
        form, button, a.inline-flex { display: none !important; }
        
        /* Keep header visible but compact */
        h1 { font-size: 14px !important; margin-bottom: 5px !important; }
        h2 { font-size: 11px !important; margin-bottom: 3px !important; }
        p { font-size: 8px !important; }
        
        /* Table fits page width */
        .overflow-x-auto {
            overflow: visible !important;
            width: 100% !important;
        }
        
        table {
            width: 100% !important;
            table-layout: fixed !important;
            font-size: 6px !important;
            page-break-inside: auto;
        }
        
        th, td {
            padding: 2px 1px !important;
            font-size: 6px !important;
            word-wrap: break-word !important;
            min-width: auto !important;
        }
        
        th:first-child, td:first-child {
            width: 60px !important;
            min-width: 60px !important;
        }
        
        tr { page-break-inside: avoid; page-break-after: auto; }
        
        /* Each day section on new page if needed */
        .mb-8 { 
            page-break-inside: avoid;
            margin-bottom: 10px !important;
        }
        
        /* Hide sticky positioning for print */
        .sticky { position: static !important; }
        
        /* Ensure backgrounds print */
        .bg-gradient-to-br, .bg-gradient-to-r,
        .bg-emerald-100, .bg-teal-100, .bg-amber-50, .bg-orange-50,
        .bg-purple-50, .bg-indigo-50 {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        
        /* Legend compact */
        .flex-wrap.gap-4 { gap: 10px !important; }
        .w-6.h-6 { width: 12px !important; height: 12px !important; }
    }
</style>
@endsection
