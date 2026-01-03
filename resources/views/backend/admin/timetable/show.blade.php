@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                {{ $class->class_name }} Timetable
            </h1>
            <p class="text-gray-500 mt-1 ml-13">Weekly schedule for Form {{ $class->class_numeric }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center gap-3">
            <a href="{{ route('admin.timetable.edit', $class->id) }}" 
               class="inline-flex items-center gap-2 bg-amber-500 text-white px-4 py-2 rounded-xl font-semibold hover:bg-amber-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <a href="{{ route('admin.timetable.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-amber-100 border-l-4 border-amber-500 text-amber-700 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('warning') }}
            </div>
        </div>
    @endif

    <!-- Academic Period Info -->
    @if($settings)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
        <p class="text-sm text-blue-800">
            <span class="font-semibold">Viewing:</span> {{ $settings->academic_year }} - Term {{ $settings->term }}
        </p>
    </div>
    @endif

    <!-- Settings Summary -->
    @if($settings)
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Start</p>
            <p class="text-lg font-bold text-gray-800">{{ \Carbon\Carbon::parse($settings->start_time)->format('H:i') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Break</p>
            <p class="text-lg font-bold text-amber-600">{{ \Carbon\Carbon::parse($settings->break_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($settings->break_end)->format('H:i') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Lunch</p>
            <p class="text-lg font-bold text-orange-600">{{ \Carbon\Carbon::parse($settings->lunch_start)->format('H:i') }} - {{ \Carbon\Carbon::parse($settings->lunch_end)->format('H:i') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">End</p>
            <p class="text-lg font-bold text-gray-800">{{ \Carbon\Carbon::parse($settings->end_time)->format('H:i') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Duration</p>
            <p class="text-lg font-bold text-indigo-600">{{ $settings->subject_duration }} min</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <p class="text-xs text-gray-500 uppercase tracking-wide">Subjects</p>
            <p class="text-lg font-bold text-emerald-600">{{ $class->subjects->count() }}</p>
        </div>
    </div>
    @endif

    <!-- Timetable Grid -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px]">
                <thead>
                    <tr style="background: linear-gradient(to right, #10b981, #14b8a6) !important;">
                        <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider w-24" style="color: #616161ff !important; background-color: transparent !important;">Time</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider" style="color: #616161ff !important; background-color: transparent !important;">Monday</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider" style="color: #616161ff !important; background-color: transparent !important;">Tuesday</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider" style="color: #616161ff !important; background-color: transparent !important;">Wednesday</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider" style="color: #616161ff !important; background-color: transparent !important;">Thursday</th>
                        <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider" style="color: #616161ff !important; background-color: transparent !important;">Friday</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $daysArray = isset($days) && is_array($days) ? $days : ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
                        $maxSlots = 0;
                        foreach($daysArray as $day) {
                            if(isset($timetable[$day]) && $timetable[$day]->count() > $maxSlots) {
                                $maxSlots = $timetable[$day]->count();
                            }
                        }
                    @endphp

                    @if($maxSlots == 0)
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">No timetable generated yet</p>
                                    <p class="text-gray-400 text-sm mt-2">Click "Edit" to create a timetable for this class</p>
                                </div>
                            </td>
                        </tr>
                    @endif

                    @for($i = 0; $i < $maxSlots; $i++)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-medium text-gray-500">
                                @if(isset($timetable['Monday'][$i]))
                                    {{ \Carbon\Carbon::parse($timetable['Monday'][$i]->start_time)->format('H:i') }}
                                    <br>
                                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($timetable['Monday'][$i]->end_time)->format('H:i') }}</span>
                                @endif
                            </td>
                            @foreach($daysArray as $day)
                                <td class="px-2 py-2">
                                    @if(isset($timetable[$day][$i]))
                                        @php $slot = $timetable[$day][$i]; @endphp
                                        
                                        @if($slot->slot_type == 'break')
                                            <div class="bg-amber-100 border border-amber-200 rounded-xl p-3 text-center">
                                                <p class="font-semibold text-amber-700">☕ Break</p>
                                            </div>
                                        @elseif($slot->slot_type == 'lunch')
                                            <div class="bg-orange-100 border border-orange-200 rounded-xl p-3 text-center">
                                                <p class="font-semibold text-orange-700">🍽️ Lunch</p>
                                            </div>
                                        @elseif($slot->slot_type == 'subject')
                                            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 rounded-xl p-3 hover:shadow-md transition-shadow">
                                                <p class="font-semibold text-gray-800 text-sm truncate">
                                                    {{ $slot->subject->name ?? 'No Subject' }}
                                                </p>
                                                @if($slot->teacher)
                                                    <p class="text-xs text-gray-500 mt-1 truncate">
                                                        {{ $slot->teacher->user->name ?? '' }}
                                                    </p>
                                                @else
                                                    <p class="text-xs text-red-400 mt-1">No teacher assigned</p>
                                                @endif
                                            </div>
                                        @else
                                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 text-center">
                                                <p class="text-gray-400 text-sm">Free</p>
                                            </div>
                                        @endif
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-6 flex flex-wrap gap-4">
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 bg-gradient-to-br from-indigo-50 to-purple-50 border border-indigo-100 rounded"></div>
            <span class="text-sm text-gray-600">Subject</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 bg-amber-100 border border-amber-200 rounded"></div>
            <span class="text-sm text-gray-600">Break</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-4 h-4 bg-orange-100 border border-orange-200 rounded"></div>
            <span class="text-sm text-gray-600">Lunch</span>
        </div>
    </div>

    <!-- Print Button -->
    <div class="mt-6 flex justify-end">
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Timetable
        </button>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
    }
</style>
@endsection
