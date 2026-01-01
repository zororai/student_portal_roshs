@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 -m-6 p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Payment Verifications</h1>
                    <p class="text-gray-500 mt-1">Review and verify parent payment submissions</p>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-600 text-sm font-medium">Pending</p>
                        <p class="text-2xl font-bold text-yellow-800">{{ $pendingCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-600 text-sm font-medium">Verified</p>
                        <p class="text-2xl font-bold text-green-800">{{ $verifiedCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-600 text-sm font-medium">Rejected</p>
                        <p class="text-2xl font-bold text-red-800">{{ $rejectedCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Verifications List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">All Submissions</h2>
            </div>

            @if($verifications->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($verifications as $verification)
                <div class="p-5 {{ $verification->status === 'pending' ? 'bg-yellow-50/50' : '' }} hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <span class="font-semibold text-gray-800">{{ $verification->parent->user->name ?? 'Unknown Parent' }}</span>
                                {!! $verification->status_badge !!}
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm text-gray-600 mb-2">
                                <div>
                                    <span class="text-gray-500">Student:</span> {{ $verification->student->user->name ?? 'Unknown' }}
                                    ({{ $verification->student->class->class_name ?? 'No Class' }})
                                </div>
                                <div>
                                    <span class="text-gray-500">Receipt #:</span> {{ $verification->receipt_number }}
                                </div>
                                <div>
                                    <span class="text-gray-500">Amount:</span> ${{ number_format($verification->amount_paid, 2) }}
                                </div>
                                <div>
                                    <span class="text-gray-500">Payment Date:</span> {{ $verification->payment_date->format('d M Y') }}
                                </div>
                            </div>
                            <p class="text-xs text-gray-500">Submitted {{ $verification->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <a href="{{ route('admin.payment-verification.show', $verification->id) }}" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                Review
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $verifications->links() }}
            </div>
            @else
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Submissions Yet</h3>
                <p class="text-gray-500 max-w-sm mx-auto">Parents have not submitted any payment verification requests yet.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
