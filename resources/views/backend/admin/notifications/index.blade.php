@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 -m-6 p-6">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Notifications</h1>
                    <p class="text-gray-500 mt-1">Send announcements to teachers, students, and parents</p>
                </div>
            </div>
            <a href="{{ route('admin.notifications.create') }}" class="mt-4 sm:mt-0 inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Send Notification
            </a>
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

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        @if(session('sms_results'))
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-blue-700">
                    <p class="font-semibold">SMS Results:</p>
                    <p>Sent: {{ session('sms_results')['sent'] }} | Failed: {{ session('sms_results')['failed'] }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- SMS Push Notifications Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-green-500 to-emerald-600">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <h2 class="text-lg font-semibold text-white">Push SMS Notifications</h2>
                </div>
            </div>

            <form action="{{ route('admin.notifications.send-sms') }}" method="POST" class="p-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Recipient Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Send To</label>
                        <select name="recipient_type" id="sms_recipient_type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500" required>
                            <option value="">-- Select Recipients --</option>
                            <option value="all_parents">All Parents</option>
                            <option value="all_teachers">All Teachers</option>
                            <option value="class_parents">Parents of Specific Class</option>
                        </select>
                    </div>

                    <!-- Class Selection (conditional) -->
                    <div id="sms_class_select" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Class</label>
                        <select name="class_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">
                            <option value="">-- Select Class --</option>
                            @foreach(\App\Grade::orderBy('class_name')->get() as $class)
                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Message -->
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea name="message" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none" placeholder="Enter your SMS message..." required maxlength="160"></textarea>
                    <p class="text-xs text-gray-500 mt-1">Maximum 160 characters for a single SMS</p>
                </div>

                <!-- Submit Button -->
                <div class="mt-6 flex items-center justify-between">
                    <div class="flex items-center text-sm text-gray-500">
                        <svg class="w-4 h-4 mr-1.5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        SMS charges may apply
                    </div>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-lg font-semibold">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Send SMS
                    </button>
                </div>
            </form>
        </div>

        <script>
            document.getElementById('sms_recipient_type').addEventListener('change', function() {
                const classSelect = document.getElementById('sms_class_select');
                if (this.value === 'class_parents') {
                    classSelect.classList.remove('hidden');
                } else {
                    classSelect.classList.add('hidden');
                }
            });
        </script>

        <!-- Notifications List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Sent Notifications</h2>
            </div>

            @if($notifications->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($notifications as $notification)
                <div class="p-5 hover:bg-gray-50 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="font-semibold text-gray-800">{{ $notification->title }}</h3>
                                {!! $notification->priority_badge !!}
                            </div>
                            <p class="text-gray-600 text-sm mb-3">{{ Str::limit($notification->message, 150) }}</p>
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $notification->recipient_label }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $notification->created_at->format('d M Y, H:i') }}
                                </div>
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    By: {{ $notification->sender->name ?? 'Unknown' }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <a href="{{ route('admin.notifications.show', $notification->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" onsubmit="return confirm('Delete this notification?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $notifications->links() }}
            </div>
            @else
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No Notifications Sent</h3>
                <p class="text-gray-500 max-w-sm mx-auto mb-6">Start by sending your first notification to teachers, students, or parents.</p>
                <a href="{{ route('admin.notifications.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Send First Notification
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
