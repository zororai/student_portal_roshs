@extends('layouts.app')
@php use Illuminate\Support\Facades\Storage; @endphp

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">My Attendance</h1>
            <p class="text-gray-600 mt-1">View your attendance history</p>
        </div>

        <!-- Today's Attendance Status -->
        @php
            $todayAttendance = $attendance->where('date', today()->format('Y-m-d'))->first();
            $isCheckedIn = $todayAttendance && $todayAttendance->check_in_time && !$todayAttendance->check_out_time;
            $isCheckedOut = $todayAttendance && $todayAttendance->check_out_time;
        @endphp
        @if($isCheckedIn)
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg mr-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-green-800">You are currently checked in</p>
                        <p class="text-sm text-green-600">Check-in time: {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }}</p>
                    </div>
                </div>
                <button onclick="openCheckoutModal()" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Check Out
                </button>
            </div>
        </div>
        @elseif($isCheckedOut)
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-blue-800">Attendance complete for today</p>
                    <p class="text-sm text-blue-600">
                        Check-in: {{ \Carbon\Carbon::parse($todayAttendance->check_in_time)->format('H:i') }} | 
                        Check-out: {{ \Carbon\Carbon::parse($todayAttendance->check_out_time)->format('H:i') }}
                        @if($todayAttendance->checkout_reason)
                            <span class="ml-2 text-xs">(Reason: {{ Str::limit($todayAttendance->checkout_reason, 30) }})</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        @endif

        <!-- QR Code and Statistics -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
            <!-- QR Code Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 text-center">My QR Code</h3>
                <div class="flex flex-col items-center">
                    @if($teacher->qr_code_token)
                        <div class="bg-white p-3 rounded-lg border-2 border-gray-200">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data={{ urlencode($teacher->qr_code_token) }}" alt="QR Code" class="w-40 h-40">
                        </div>
                        <p class="text-xs text-gray-500 mt-3 text-center">Scan this code for attendance</p>
                        
                        <!-- QR Token for manual copy -->
                        <div class="mt-3 w-full">
                            <label class="block text-xs text-gray-500 mb-1">QR Token (for manual input):</label>
                            <div class="flex items-center space-x-2">
                                <input type="text" readonly value="{{ $teacher->qr_code_token }}" 
                                       class="flex-1 text-xs p-2 bg-gray-50 border border-gray-200 rounded-lg font-mono" 
                                       id="qrToken">
                                <button onclick="copyToken()" class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors" title="Copy Token">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <button onclick="window.print()" class="mt-3 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Print QR
                        </button>
                    @else
                        <div class="bg-gray-100 p-6 rounded-lg text-center">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            <p class="text-sm text-gray-500">QR code not generated</p>
                            <p class="text-xs text-gray-400 mt-1">Contact admin to generate</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Days</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $totalDays }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Present Days</p>
                            <p class="text-2xl font-bold text-green-600">{{ $presentDays }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-100 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Late Days</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ $lateDays }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('teacher.my-attendance') }}" class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                    <select name="month" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select name="year" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @for($y = now()->year; $y >= now()->year - 2; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="pt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Attendance Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Attendance Records - {{ date('F', mktime(0, 0, 0, $month, 1)) }} {{ $year }}
                </h3>
            </div>
            
            @if($attendance->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attendance as $record)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($record->date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($record->date)->format('l') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($record->check_in_time)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $record->check_in_time > '08:00:00' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ \Carbon\Carbon::parse($record->check_in_time)->format('H:i') }}
                                        @if($record->check_in_time > '08:00:00')
                                            (Late)
                                        @endif
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($record->check_out_time)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ \Carbon\Carbon::parse($record->check_out_time)->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($record->status === 'IN')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Checked In
                                    </span>
                                @elseif($record->status === 'OUT')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Checked Out
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Absent
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No attendance records</h3>
                <p class="mt-1 text-sm text-gray-500">No attendance records found for the selected period.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Checkout Modal -->
<div id="checkoutModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeCheckoutModal()"></div>
        <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6">
            <div id="checkoutFormSection">
                <div class="text-center mb-4">
                    <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full">
                        <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    <h3 class="mt-4 text-xl font-semibold text-gray-900">Check Out</h3>
                    <p class="mt-2 text-sm text-gray-500">Are you sure you want to check out now?</p>
                </div>
                
                <!-- Reason Section (shown when early checkout) -->
                <div id="reasonSection" class="hidden mb-4">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-3">
                        <p class="text-sm text-yellow-800">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span id="earlyCheckoutMessage">You are checking out early. Please provide a reason.</span>
                        </p>
                    </div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Early Checkout <span class="text-red-500">*</span></label>
                    <textarea id="checkoutReason" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500" placeholder="Enter your reason for checking out early..."></textarea>
                </div>

                <div class="mt-6 flex justify-center space-x-3">
                    <button onclick="closeCheckoutModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button onclick="submitCheckout()" id="checkoutBtn" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2 hidden" id="checkoutSpinner" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Confirm Check Out
                    </button>
                </div>
            </div>
            
            <!-- Success Section -->
            <div id="checkoutSuccessSection" class="hidden text-center">
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-green-100 rounded-full">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-semibold text-gray-900">Checked Out Successfully!</h3>
                <p id="checkoutSuccessMessage" class="mt-2 text-sm text-gray-500"></p>
                <button onclick="window.location.reload()" class="mt-4 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                    Done
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    let requiresReason = false;

    function copyToken() {
        const tokenInput = document.getElementById('qrToken');
        tokenInput.select();
        tokenInput.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(tokenInput.value).then(() => {
            alert('QR Token copied! Paste it in the scanner page.');
        }).catch(err => {
            document.execCommand('copy');
            alert('QR Token copied! Paste it in the scanner page.');
        });
    }

    function openCheckoutModal() {
        document.getElementById('checkoutModal').classList.remove('hidden');
        document.getElementById('checkoutFormSection').classList.remove('hidden');
        document.getElementById('checkoutSuccessSection').classList.add('hidden');
        document.getElementById('reasonSection').classList.add('hidden');
        document.getElementById('checkoutReason').value = '';
        requiresReason = false;
    }

    function closeCheckoutModal() {
        document.getElementById('checkoutModal').classList.add('hidden');
    }

    function submitCheckout() {
        const btn = document.getElementById('checkoutBtn');
        const spinner = document.getElementById('checkoutSpinner');
        const reason = document.getElementById('checkoutReason').value.trim();
        
        // Validate reason if required
        if (requiresReason && !reason) {
            alert('Please provide a reason for early checkout.');
            return;
        }

        // Disable button and show spinner
        btn.disabled = true;
        spinner.classList.remove('hidden');
        spinner.classList.add('animate-spin');

        fetch('{{ route("teacher.self-checkout") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ checkout_reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                document.getElementById('checkoutFormSection').classList.add('hidden');
                document.getElementById('checkoutSuccessSection').classList.remove('hidden');
                document.getElementById('checkoutSuccessMessage').textContent = data.message;
            } else if (data.requires_reason) {
                // Show reason section
                requiresReason = true;
                document.getElementById('reasonSection').classList.remove('hidden');
                document.getElementById('earlyCheckoutMessage').textContent = data.message;
                btn.disabled = false;
                spinner.classList.add('hidden');
                spinner.classList.remove('animate-spin');
            } else {
                alert(data.message || 'Failed to check out. Please try again.');
                btn.disabled = false;
                spinner.classList.add('hidden');
                spinner.classList.remove('animate-spin');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            btn.disabled = false;
            spinner.classList.add('hidden');
            spinner.classList.remove('animate-spin');
        });
    }
</script>
@endpush
@endsection
