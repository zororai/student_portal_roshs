@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 -m-6 p-6">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <a href="{{ route('parent.medical-reports.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Reports
            </a>
            {!! $report->status_badge !!}
        </div>

        <!-- Report Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-red-500 to-pink-600">
                <h1 class="text-xl font-bold text-white">{{ $report->condition_name }}</h1>
                <p class="text-red-100 mt-1">{{ ucfirst(str_replace('_', ' ', $report->condition_type)) }}</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Student Info -->
                <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-indigo-200 rounded-xl flex items-center justify-center mr-4">
                        <span class="text-lg font-bold text-indigo-600">{{ substr($report->student->user->name ?? 'S', 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $report->student->user->name ?? 'Unknown' }}</p>
                        <p class="text-sm text-gray-500">{{ $report->student->class->class_name ?? 'No Class' }}</p>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Description</h3>
                    <p class="text-gray-700">{{ $report->description }}</p>
                </div>

                @if($report->medications)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Medications</h3>
                    <p class="text-gray-700">{{ $report->medications }}</p>
                </div>
                @endif

                @if($report->emergency_instructions)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Emergency Instructions</h3>
                    <div class="p-4 bg-red-50 border border-red-100 rounded-xl">
                        <p class="text-red-700">{{ $report->emergency_instructions }}</p>
                    </div>
                </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($report->diagnosis_date)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Diagnosis Date</h3>
                        <p class="text-gray-700">{{ $report->diagnosis_date->format('d M Y') }}</p>
                    </div>
                    @endif

                    @if($report->doctor_name)
                    <div>
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Doctor</h3>
                        <p class="text-gray-700">{{ $report->doctor_name }}</p>
                        @if($report->doctor_contact)
                        <p class="text-sm text-gray-500">{{ $report->doctor_contact }}</p>
                        @endif
                    </div>
                    @endif
                </div>

                @if($report->attachment_path)
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Attachment</h3>
                    <a href="{{ Storage::url($report->attachment_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        View Attachment
                    </a>
                </div>
                @endif

                <!-- Admin Response -->
                @if($report->status !== 'pending')
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Admin Response</h3>
                    <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl">
                        @if($report->admin_response)
                        <p class="text-blue-700">{{ $report->admin_response }}</p>
                        @else
                        <p class="text-blue-600 italic">Report acknowledged - no additional comments.</p>
                        @endif
                        <div class="mt-3 pt-3 border-t border-blue-200 text-sm text-blue-600">
                            <p>Acknowledged by {{ $report->acknowledgedBy->name ?? 'Admin' }}</p>
                            <p>{{ $report->acknowledged_at->format('d M Y \a\t H:i') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Submission Info -->
                <div class="border-t border-gray-100 pt-6">
                    <p class="text-sm text-gray-500">
                        Submitted on {{ $report->created_at->format('d M Y \a\t H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
