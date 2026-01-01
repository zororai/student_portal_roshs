@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 -m-6 p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <a href="{{ route('admin.medical-reports.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-800 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Reports
            </a>
            {!! $report->status_badge !!}
        </div>

        <!-- Report Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-red-500 to-pink-600">
                <h1 class="text-xl font-bold text-white">{{ $report->condition_name }}</h1>
                <p class="text-red-100 mt-1">{{ ucfirst(str_replace('_', ' ', $report->condition_type)) }}</p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Student & Parent Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-indigo-200 rounded-xl flex items-center justify-center mr-4">
                            <span class="text-lg font-bold text-indigo-600">{{ substr($report->student->user->name ?? 'S', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Student</p>
                            <p class="font-semibold text-gray-800">{{ $report->student->user->name ?? 'Unknown' }}</p>
                            <p class="text-sm text-gray-500">{{ $report->student->class->class_name ?? 'No Class' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center p-4 bg-gray-50 rounded-xl">
                        <div class="w-12 h-12 bg-gradient-to-br from-emerald-100 to-teal-200 rounded-xl flex items-center justify-center mr-4">
                            <span class="text-lg font-bold text-teal-600">{{ substr($report->parent->user->name ?? 'P', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase">Parent</p>
                            <p class="font-semibold text-gray-800">{{ $report->parent->user->name ?? 'Unknown' }}</p>
                            <p class="text-sm text-gray-500">{{ $report->parent->phone ?? 'No phone' }}</p>
                        </div>
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

                <!-- Submission Info -->
                <div class="border-t border-gray-100 pt-6">
                    <p class="text-sm text-gray-500">Submitted on {{ $report->created_at->format('d M Y \a\t H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Admin Response Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Admin Response</h2>
            </div>
            
            <div class="p-6">
                @if($report->status === 'pending')
                <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <p class="text-amber-800 text-sm">
                        <strong>Action Required:</strong> Please review this medical report and acknowledge that you have received it. 
                        The parent will be notified that their report has been seen.
                    </p>
                </div>
                <form action="{{ route('admin.medical-reports.acknowledge', $report->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="admin_response" class="block text-sm font-medium text-gray-700 mb-2">Response Message (Optional)</label>
                        <textarea name="admin_response" id="admin_response" rows="4" 
                            placeholder="Add any notes or response for the parent..."
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all resize-none">{{ old('admin_response') }}</textarea>
                        <p class="text-sm text-gray-500 mt-1">This message will be visible to the parent.</p>
                    </div>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Acknowledge Report
                    </button>
                </form>
                @else
                <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-xl">
                    @if($report->admin_response)
                    <p class="text-emerald-700">{{ $report->admin_response }}</p>
                    @else
                    <p class="text-emerald-600 italic">Report acknowledged - no additional comments.</p>
                    @endif
                    <div class="mt-3 pt-3 border-t border-emerald-200 text-sm text-emerald-600">
                        <p>Acknowledged by {{ $report->acknowledgedBy->name ?? 'Admin' }}</p>
                        <p>{{ $report->acknowledged_at->format('d M Y \a\t H:i') }}</p>
                    </div>
                </div>

                @if($report->status === 'acknowledged')
                <form action="{{ route('admin.medical-reports.review', $report->id) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Mark as Reviewed
                    </button>
                </form>
                @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
