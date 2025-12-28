@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('teacher.leave.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-4">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Leave Applications
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Leave Application Details</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Status Banner -->
        <div class="p-4 {{ $leave->status === 'approved' ? 'bg-green-50 border-b border-green-200' : ($leave->status === 'rejected' ? 'bg-red-50 border-b border-red-200' : 'bg-yellow-50 border-b border-yellow-200') }}">
            <div class="flex items-center">
                @if($leave->status === 'approved')
                    <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold text-green-800">Approved</span>
                @elseif($leave->status === 'rejected')
                    <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold text-red-800">Rejected</span>
                @else
                    <svg class="w-6 h-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold text-yellow-800">Pending Approval</span>
                @endif
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Leave Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Leave Type</p>
                    <p class="font-semibold text-gray-900">{{ \App\LeaveApplication::getLeaveTypes()[$leave->leave_type] ?? ucfirst($leave->leave_type) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Days</p>
                    <p class="font-semibold text-gray-900">{{ $leave->total_days }} day{{ $leave->total_days > 1 ? 's' : '' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Start Date</p>
                    <p class="font-semibold text-gray-900">{{ $leave->start_date->format('l, F d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">End Date</p>
                    <p class="font-semibold text-gray-900">{{ $leave->end_date->format('l, F d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Applied On</p>
                    <p class="font-semibold text-gray-900">{{ $leave->created_at->format('F d, Y \a\t h:i A') }}</p>
                </div>
                @if($leave->approved_at)
                <div>
                    <p class="text-sm text-gray-500">{{ $leave->status === 'approved' ? 'Approved' : 'Rejected' }} On</p>
                    <p class="font-semibold text-gray-900">{{ $leave->approved_at->format('F d, Y \a\t h:i A') }}</p>
                </div>
                @endif
            </div>

            <!-- Reason -->
            <div>
                <p class="text-sm text-gray-500 mb-2">Reason</p>
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-900">{{ $leave->reason }}</p>
                </div>
            </div>

            <!-- Attachment -->
            @if($leave->attachment)
            <div>
                <p class="text-sm text-gray-500 mb-2">Attached Document</p>
                <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    View Attachment
                </a>
            </div>
            @endif

            <!-- Admin Remarks -->
            @if($leave->admin_remarks)
            <div>
                <p class="text-sm text-gray-500 mb-2">Admin Remarks</p>
                <div class="p-4 {{ $leave->status === 'approved' ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }} rounded-lg">
                    <p class="{{ $leave->status === 'approved' ? 'text-green-800' : 'text-red-800' }}">{{ $leave->admin_remarks }}</p>
                    @if($leave->approver)
                        <p class="text-sm text-gray-500 mt-2">- {{ $leave->approver->name }}</p>
                    @endif
                </div>
            </div>
            @endif

            <!-- Actions -->
            @if($leave->isPending())
            <div class="pt-4 border-t border-gray-200">
                <form action="{{ route('teacher.leave.destroy', $leave->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this leave request?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Cancel Application
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
