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
                Class Timetable
            </h1>
            <p class="text-gray-500 mt-1 ml-13">View your class schedules</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center gap-3">
            @if($classes->count() > 0)
                <form method="GET" action="{{ route('teacher.timetable') }}" class="flex items-center gap-2">
                    <select name="class_id" onchange="this.form.submit()" class="px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ ($class && $class->id == $c->id) ? 'selected' : '' }}>
                                {{ $c->class_name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @endif
            <button id="notificationBtn" onclick="toggleNotifications()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-700 rounded-xl font-semibold hover:bg-emerald-200 transition-colors no-print">
                <svg id="notificationIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span id="notificationText">Enable Reminders</span>
            </button>
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors no-print">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
        </div>
    </div>

    @if(!$class)
        <div class="bg-amber-100 border-l-4 border-amber-500 text-amber-700 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                You are not assigned to any class yet.
            </div>
        </div>
    @elseif(!$settings)
        <div class="bg-amber-100 border-l-4 border-amber-500 text-amber-700 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                No timetable has been created for {{ $class->class_name }} yet.
            </div>
        </div>
    @else
        <!-- Class Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-800">{{ $class->class_name }}</h2>
            <p class="text-sm text-gray-500">Form {{ $class->class_numeric }}</p>
        </div>

        <!-- Settings Summary -->
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
        </div>

        <!-- Timetable Grid -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]">
                    <thead>
                        <tr style="background: linear-gradient(to right, #10b981, #14b8a6) !important;">
                            <th class="px-4 py-4 text-left text-xs font-semibold uppercase tracking-wider w-24" style="color: #616161 !important; background-color: transparent !important;">Time</th>
                            @foreach($days as $day)
                                <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider" style="color: #616161 !important; background-color: transparent !important;">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $maxSlots = 0;
                            foreach($days as $day) {
                                if(isset($timetable[$day]) && count($timetable[$day]) > $maxSlots) {
                                    $maxSlots = count($timetable[$day]);
                                }
                            }
                        @endphp

                        @for($i = 0; $i < $maxSlots; $i++)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-500">
                                    @if(isset($timetable['Monday'][$i]))
                                        {{ \Carbon\Carbon::parse($timetable['Monday'][$i]->start_time)->format('H:i') }}
                                        <br>
                                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($timetable['Monday'][$i]->end_time)->format('H:i') }}</span>
                                    @endif
                                </td>
                                @foreach($days as $day)
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
    @endif
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
    }
</style>

<script>
const VAPID_PUBLIC_KEY = '{{ config("services.webpush.public_key") }}';
let swRegistration = null;
let isSubscribed = false;

// Initialize on page load
document.addEventListener('DOMContentLoaded', async () => {
    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        document.getElementById('notificationBtn').style.display = 'none';
        return;
    }

    try {
        swRegistration = await navigator.serviceWorker.register('/sw.js');
        const subscription = await swRegistration.pushManager.getSubscription();
        isSubscribed = !!subscription;
        updateUI();
    } catch (error) {
        console.error('Service worker registration failed:', error);
    }
});

function updateUI() {
    const btn = document.getElementById('notificationBtn');
    const text = document.getElementById('notificationText');
    
    if (isSubscribed) {
        btn.classList.remove('bg-emerald-100', 'text-emerald-700', 'hover:bg-emerald-200');
        btn.classList.add('bg-red-100', 'text-red-700', 'hover:bg-red-200');
        text.textContent = 'Disable Reminders';
    } else {
        btn.classList.remove('bg-red-100', 'text-red-700', 'hover:bg-red-200');
        btn.classList.add('bg-emerald-100', 'text-emerald-700', 'hover:bg-emerald-200');
        text.textContent = 'Enable Reminders';
    }
}

async function toggleNotifications() {
    if (!swRegistration) {
        alert('Push notifications are not supported in this browser.');
        return;
    }

    if (isSubscribed) {
        await unsubscribeUser();
    } else {
        await subscribeUser();
    }
}

async function subscribeUser() {
    try {
        const permission = await Notification.requestPermission();
        if (permission !== 'granted') {
            alert('Please allow notifications to receive lesson reminders.');
            return;
        }

        const subscription = await swRegistration.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
        });

        const response = await fetch('{{ route("push.subscribe") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(subscription)
        });

        const data = await response.json();
        if (data.success) {
            isSubscribed = true;
            updateUI();
            alert('🔔 Lesson reminders enabled! You will receive notifications 5 minutes before each lesson.');
        }
    } catch (error) {
        console.error('Subscription failed:', error);
        alert('Failed to enable notifications. Please try again.');
    }
}

async function unsubscribeUser() {
    try {
        const subscription = await swRegistration.pushManager.getSubscription();
        if (subscription) {
            await subscription.unsubscribe();
            
            await fetch('{{ route("push.unsubscribe") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ endpoint: subscription.endpoint })
            });
        }
        
        isSubscribed = false;
        updateUI();
        alert('Lesson reminders have been disabled.');
    } catch (error) {
        console.error('Unsubscribe failed:', error);
    }
}

function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}
</script>
@endsection
