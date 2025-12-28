@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('admin.leave.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center mb-4">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Leave Management
        </a>
        <h1 class="text-3xl font-bold text-gray-900">Leave Application Details</h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Status Banner -->
                <div class="p-4 {{ $leave->status === 'approved' ? 'bg-green-50 border-b border-green-200' : ($leave->status === 'rejected' ? 'bg-red-50 border-b border-red-200' : 'bg-yellow-50 border-b border-yellow-200') }}">
                    <div class="flex items-center justify-between">
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
                        @if($leave->approved_at)
                            <span class="text-sm text-gray-600">{{ $leave->approved_at->format('M d, Y H:i') }}</span>
                        @endif
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Teacher Info -->
                    <div class="flex items-center pb-4 border-b border-gray-200">
                        @if($leave->teacher->user->profile_picture)
                            <img class="h-14 w-14 rounded-full object-cover" src="{{ asset('storage/' . $leave->teacher->user->profile_picture) }}" alt="">
                        @else
                            <div class="h-14 w-14 rounded-full bg-blue-500 flex items-center justify-center">
                                <span class="text-white font-bold text-xl">{{ substr($leave->teacher->user->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">{{ $leave->teacher->user->name }}</h3>
                            <p class="text-gray-500">{{ $leave->teacher->user->email }}</p>
                        </div>
                    </div>

                    <!-- Leave Details -->
                    <div class="grid grid-cols-2 gap-6">
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
                    </div>

                    <!-- Reason -->
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Reason for Leave</p>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-900">{{ $leave->reason }}</p>
                        </div>
                    </div>

                    <!-- Attachment -->
                    @if($leave->attachment)
                    <div>
                        <p class="text-sm text-gray-500 mb-2">Supporting Document</p>
                        <a href="{{ asset('storage/' . $leave->attachment) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
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

                    <!-- Action Buttons -->
                    @if($leave->isPending())
                    <div class="pt-4 border-t border-gray-200">
                        <div class="space-y-4">
                            <form action="{{ route('admin.leave.approve', $leave->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks (Optional)</label>
                                    <textarea name="admin_remarks" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Add any remarks..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                    Approve Leave Request
                                </button>
                            </form>
                            <form action="{{ route('admin.leave.reject', $leave->id) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Rejection *</label>
                                    <textarea name="admin_remarks" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Please provide a reason..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                    Reject Leave Request
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-900">Recent Leave History</h3>
                </div>
                <div class="p-4">
                    @forelse($leaveHistory as $history)
                        <div class="mb-4 pb-4 border-b border-gray-100 last:border-0 last:mb-0 last:pb-0">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-sm font-medium text-gray-900">{{ ucfirst($history->leave_type) }}</span>
                                <span class="text-xs text-gray-500">{{ $history->total_days }}d</span>
                            </div>
                            <p class="text-xs text-gray-500">{{ $history->start_date->format('M d') }} - {{ $history->end_date->format('M d, Y') }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No previous leave history</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
