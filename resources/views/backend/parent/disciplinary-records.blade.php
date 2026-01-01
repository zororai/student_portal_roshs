@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 -m-6 p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Disciplinary Records</h1>
                    <p class="text-gray-500 mt-1">View your child's disciplinary history</p>
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
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-red-700">{{ $error }}</p>
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

        @if($records->count() > 0)
        <!-- Disciplinary Records -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-800">Disciplinary History</h2>
                    <span class="text-sm text-gray-500">{{ $records->count() }} record(s)</span>
                </div>
            </div>

            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Offense</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="text-left py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Recorded By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($records as $record)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-6">
                                <p class="font-medium text-gray-800">{{ $record->student->user->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-500">{{ $record->class->class_name ?? 'N/A' }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <div>
                                    <p class="font-medium text-gray-800">{{ $record->offense_type }}</p>
                                    <p class="text-sm text-gray-500 line-clamp-2">{{ Str::limit($record->description, 50) }}</p>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    switch($record->offense_status) {
                                        case 'pending':
                                            $statusClass = 'bg-amber-100 text-amber-700';
                                            break;
                                        case 'resolved':
                                            $statusClass = 'bg-emerald-100 text-emerald-700';
                                            break;
                                        case 'escalated':
                                            $statusClass = 'bg-red-100 text-red-700';
                                            break;
                                        default:
                                            $statusClass = 'bg-gray-100 text-gray-700';
                                    }
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                                    {{ ucfirst($record->offense_status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-gray-600">{{ $record->offense_date ? $record->offense_date->format('d M Y') : 'N/A' }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <p class="text-gray-600">{{ $record->teacher->user->name ?? 'N/A' }}</p>
                            </td>
                        </tr>
                        @if($record->judgement)
                        <tr class="bg-blue-50">
                            <td colspan="5" class="py-3 px-6">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-blue-500 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-blue-800">Judgement/Resolution:</p>
                                        <p class="text-sm text-blue-700">{{ $record->judgement }}</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="md:hidden divide-y divide-gray-100">
                @foreach ($records as $record)
                <div class="p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="font-medium text-gray-800">{{ $record->offense_type }}</p>
                            <p class="text-sm text-gray-500">{{ $record->student->user->name ?? 'Unknown' }}</p>
                        </div>
                        @php
                            switch($record->offense_status) {
                                case 'pending':
                                    $statusClass = 'bg-amber-100 text-amber-700';
                                    break;
                                case 'resolved':
                                    $statusClass = 'bg-emerald-100 text-emerald-700';
                                    break;
                                case 'escalated':
                                    $statusClass = 'bg-red-100 text-red-700';
                                    break;
                                default:
                                    $statusClass = 'bg-gray-100 text-gray-700';
                            }
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusClass }}">
                            {{ ucfirst($record->offense_status) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600 mb-2">{{ $record->description }}</p>
                    <div class="flex items-center text-sm text-gray-500 space-x-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $record->offense_date ? $record->offense_date->format('d M Y') : 'N/A' }}
                        </div>
                    </div>
                    @if($record->judgement)
                    <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                        <p class="text-sm font-medium text-blue-800">Judgement:</p>
                        <p class="text-sm text-blue-700">{{ $record->judgement }}</p>
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
                <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Disciplinary Records</h3>
                <p class="text-gray-500 max-w-sm mx-auto">Great news! Your child has no disciplinary records on file.</p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
