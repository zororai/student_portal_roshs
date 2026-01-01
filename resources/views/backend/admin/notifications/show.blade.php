@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 -m-6 p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center space-x-4 mb-8">
            <a href="{{ route('admin.notifications.index') }}" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border border-gray-100 hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Notification Details</h1>
                <p class="text-gray-500 mt-1">View sent notification</p>
            </div>
        </div>

        <!-- Notification Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ $notification->title }}</h2>
                    {!! $notification->priority_badge !!}
                </div>

                <div class="prose max-w-none text-gray-600 mb-6">
                    {!! nl2br(e($notification->message)) !!}
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 bg-gray-50 rounded-xl">
                    <div>
                        <p class="text-sm text-gray-500">Sent To</p>
                        <p class="font-medium text-gray-800">{{ $notification->recipient_label }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Sent By</p>
                        <p class="font-medium text-gray-800">{{ $notification->sender->name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Date Sent</p>
                        <p class="font-medium text-gray-800">{{ $notification->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Priority</p>
                        <p class="font-medium text-gray-800">{{ ucfirst($notification->priority) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Read Status -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-800">Read Status</h3>
            </div>
            @if($notification->reads->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($notification->reads as $read)
                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-emerald-100 to-teal-200 rounded-full flex items-center justify-center">
                            <span class="text-sm font-bold text-teal-600">{{ substr($read->user->name ?? 'U', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $read->user->name ?? 'Unknown' }}</p>
                            <p class="text-sm text-gray-500">{{ $read->user->email ?? '' }}</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500">
                        Read {{ $read->read_at ? $read->read_at->diffForHumans() : 'recently' }}
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </div>
                <p class="text-gray-500">No one has read this notification yet.</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
