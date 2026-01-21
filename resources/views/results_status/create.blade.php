@extends('layouts.app')

@section('content')
    <div class="create-results-status">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-gray-700 uppercase font-bold">Create New Term</h2>
            </div>
        </div>

        <!-- Teacher Session Reminder Alert -->
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-r-lg shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-semibold text-amber-800">Remember to Update Teacher Sessions</h3>
                    <p class="text-sm text-amber-700 mt-1">
                        Before or after creating a new term, please ensure teacher work sessions (Morning, Afternoon, or Both) are correctly assigned.
                    </p>
                    <div class="mt-3">
                        <a href="{{ route('teacher.sessions') }}" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Change Teacher Sessions
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <form action="{{ route('results_status.store') }}" method="POST" id="termForm">
            @csrf
            <div class="mt-4 bg-white rounded border border-gray-300 p-6">
                <div class="form-group mb-4">
                    <label for="year" class="block text-sm font-semibold text-gray-700 mb-2">Year</label>
                    <select name="year" id="year" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select year</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                        <option value="2028">2028</option>
                        <option value="2029">2029</option>
                        <option value="2030">2030</option>
                    </select>
                    @error('year')
                        <div class="text-danger text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="result_period" class="block text-gray-700 font-bold mb-2">Select Term:</label>
                    <select name="result_period" id="result_period" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select a Term</option>
                        <option value="first">First Term</option>
                        <option value="second">Second Term</option>
                        <option value="third">Third Term</option>
                    </select>
                    @error('result_period')
                        <div class="text-danger text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Curriculum Fee Structure Tabs -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-700 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Fee Structure by Curriculum
                        </h3>
                    </div>

                    <!-- Curriculum Tabs -->
                    <div class="border-b border-gray-200 mb-4">
                        <nav class="-mb-px flex space-x-8">
                            <button type="button" onclick="switchCurriculumTab('zimsec')" id="zimsec-tab" class="curriculum-tab border-b-2 border-indigo-500 py-2 px-1 text-sm font-medium text-indigo-600">
                                ZIMSEC Curriculum
                            </button>
                            <button type="button" onclick="switchCurriculumTab('cambridge')" id="cambridge-tab" class="curriculum-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Cambridge Curriculum
                            </button>
                        </nav>
                    </div>

                    <!-- ZIMSEC Fees Panel -->
                    <div id="zimsec-panel" class="curriculum-panel">
                        <!-- ZIMSEC Day Scholar Fees -->
                        <div class="mb-4">
                            <h4 class="font-semibold text-gray-700 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                ZIMSEC Day Scholar Fees
                            </h4>
                            <div id="zimsecDayFeesContainer" class="space-y-3 bg-blue-50 p-4 rounded-lg border border-blue-200">
                                @if($feeTypes->count() > 0)
                                    <div class="fee-type-row bg-white p-3 rounded border border-gray-200">
                                        <div class="grid grid-cols-12 gap-3">
                                            <div class="col-span-5">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Fee Type</label>
                                                <select name="zimsec_day_fees[0][fee_type_id]" class="w-full border rounded px-3 py-2 text-sm" required>
                                                    <option value="">Select Fee Type</option>
                                                    @foreach($feeTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-5">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount ($)</label>
                                                <input type="number" name="zimsec_day_fees[0][amount]" step="0.01" min="0" class="w-full border rounded px-3 py-2 text-sm zimsec-day-fee-amount" placeholder="0.00" required>
                                            </div>
                                            <div class="col-span-2 flex items-end gap-1">
                                                <button type="button" class="add-zimsec-day-fee bg-green-500 hover:bg-green-600 text-white px-2 py-2 rounded text-xs" title="Add">+</button>
                                                <button type="button" class="remove-zimsec-day-fee bg-red-500 hover:bg-red-600 text-white px-2 py-2 rounded text-xs" title="Remove">-</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="text-right font-semibold text-blue-700 text-sm">
                                    Total: <span id="zimsecDayTotalDisplay">$0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- ZIMSEC Boarding Fees -->
                        <div class="mb-4">
                            <h4 class="font-semibold text-gray-700 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                ZIMSEC Boarding Fees
                            </h4>
                            <div id="zimsecBoardingFeesContainer" class="space-y-3 bg-green-50 p-4 rounded-lg border border-green-200">
                                @if($feeTypes->count() > 0)
                                    <div class="fee-type-row bg-white p-3 rounded border border-gray-200">
                                        <div class="grid grid-cols-12 gap-3">
                                            <div class="col-span-5">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Fee Type</label>
                                                <select name="zimsec_boarding_fees[0][fee_type_id]" class="w-full border rounded px-3 py-2 text-sm" required>
                                                    <option value="">Select Fee Type</option>
                                                    @foreach($feeTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-5">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount ($)</label>
                                                <input type="number" name="zimsec_boarding_fees[0][amount]" step="0.01" min="0" class="w-full border rounded px-3 py-2 text-sm zimsec-boarding-fee-amount" placeholder="0.00" required>
                                            </div>
                                            <div class="col-span-2 flex items-end gap-1">
                                                <button type="button" class="add-zimsec-boarding-fee bg-green-500 hover:bg-green-600 text-white px-2 py-2 rounded text-xs" title="Add">+</button>
                                                <button type="button" class="remove-zimsec-boarding-fee bg-red-500 hover:bg-red-600 text-white px-2 py-2 rounded text-xs" title="Remove">-</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="text-right font-semibold text-green-700 text-sm">
                                    Total: <span id="zimsecBoardingTotalDisplay">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cambridge Fees Panel (hidden by default) -->
                    <div id="cambridge-panel" class="curriculum-panel hidden">
                        <!-- Cambridge Day Scholar Fees -->
                        <div class="mb-4">
                            <h4 class="font-semibold text-gray-700 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                Cambridge Day Scholar Fees
                            </h4>
                            <div id="cambridgeDayFeesContainer" class="space-y-3 bg-purple-50 p-4 rounded-lg border border-purple-200">
                                @if($feeTypes->count() > 0)
                                    <div class="fee-type-row bg-white p-3 rounded border border-gray-200">
                                        <div class="grid grid-cols-12 gap-3">
                                            <div class="col-span-5">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Fee Type</label>
                                                <select name="cambridge_day_fees[0][fee_type_id]" class="w-full border rounded px-3 py-2 text-sm" required>
                                                    <option value="">Select Fee Type</option>
                                                    @foreach($feeTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-5">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount ($)</label>
                                                <input type="number" name="cambridge_day_fees[0][amount]" step="0.01" min="0" class="w-full border rounded px-3 py-2 text-sm cambridge-day-fee-amount" placeholder="0.00" required>
                                            </div>
                                            <div class="col-span-2 flex items-end gap-1">
                                                <button type="button" class="add-cambridge-day-fee bg-green-500 hover:bg-green-600 text-white px-2 py-2 rounded text-xs" title="Add">+</button>
                                                <button type="button" class="remove-cambridge-day-fee bg-red-500 hover:bg-red-600 text-white px-2 py-2 rounded text-xs" title="Remove">-</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="text-right font-semibold text-purple-700 text-sm">
                                    Total: <span id="cambridgeDayTotalDisplay">$0.00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Cambridge Boarding Fees -->
                        <div class="mb-4">
                            <h4 class="font-semibold text-gray-700 mb-2 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                                Cambridge Boarding Fees
                            </h4>
                            <div id="cambridgeBoardingFeesContainer" class="space-y-3 bg-orange-50 p-4 rounded-lg border border-orange-200">
                                @if($feeTypes->count() > 0)
                                    <div class="fee-type-row bg-white p-3 rounded border border-gray-200">
                                        <div class="grid grid-cols-12 gap-3">
                                            <div class="col-span-5">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Fee Type</label>
                                                <select name="cambridge_boarding_fees[0][fee_type_id]" class="w-full border rounded px-3 py-2 text-sm" required>
                                                    <option value="">Select Fee Type</option>
                                                    @foreach($feeTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-span-5">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount ($)</label>
                                                <input type="number" name="cambridge_boarding_fees[0][amount]" step="0.01" min="0" class="w-full border rounded px-3 py-2 text-sm cambridge-boarding-fee-amount" placeholder="0.00" required>
                                            </div>
                                            <div class="col-span-2 flex items-end gap-1">
                                                <button type="button" class="add-cambridge-boarding-fee bg-green-500 hover:bg-green-600 text-white px-2 py-2 rounded text-xs" title="Add">+</button>
                                                <button type="button" class="remove-cambridge-boarding-fee bg-red-500 hover:bg-red-600 text-white px-2 py-2 rounded text-xs" title="Remove">-</button>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="text-right font-semibold text-orange-700 text-sm">
                                    Total: <span id="cambridgeBoardingTotalDisplay">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    @error('zimsec_day_fees')
                        <div class="text-danger text-red-500 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Total Fees Summary -->
                <div class="mb-6">
                    <h4 class="text-md font-bold text-gray-700 mb-3">Fee Summary</h4>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-3 rounded">
                            <p class="text-xs text-gray-600">ZIMSEC Day</p>
                            <div id="totalZimsecDayDisplay" class="text-lg font-bold text-blue-600">$0.00</div>
                        </div>
                        <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded">
                            <p class="text-xs text-gray-600">ZIMSEC Boarding</p>
                            <div id="totalZimsecBoardingDisplay" class="text-lg font-bold text-green-600">$0.00</div>
                        </div>
                        <div class="bg-purple-50 border-l-4 border-purple-500 p-3 rounded">
                            <p class="text-xs text-gray-600">Cambridge Day</p>
                            <div id="totalCambridgeDayDisplay" class="text-lg font-bold text-purple-600">$0.00</div>
                        </div>
                        <div class="bg-orange-50 border-l-4 border-orange-500 p-3 rounded">
                            <p class="text-xs text-gray-600">Cambridge Boarding</p>
                            <div id="totalCambridgeBoardingDisplay" class="text-lg font-bold text-orange-600">$0.00</div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Scholarship students get percentage discounts applied to these rates.</p>
                </div>

                <!-- Level-Based Fee Adjustments Section -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-700 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Level-Based Fee Adjustments
                        </h3>
                        <span class="text-xs text-gray-500">Optional: Add extra fees for specific class levels</span>
                    </div>

                    <div class="bg-teal-50 p-4 rounded-lg border border-teal-200">
                        <p class="text-sm text-gray-600 mb-4">
                            <svg class="w-4 h-4 inline mr-1 text-teal-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Set additional fees for specific class levels. New students or students upgrading to a new level may pay more.
                        </p>

                        @if(isset($classes) && $classes->count() > 0)
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                                <h4 class="text-sm font-semibold text-gray-700">Class Levels ({{ ucfirst($upgradeDirection ?? 'ascending') }} Order)</h4>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ZIMSEC Day +</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">ZIMSEC Board +</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Camb Day +</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Camb Board +</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach($classes as $class)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-2">
                                                <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center">
                                                    <span class="text-teal-600 font-bold text-sm">{{ $class->class_numeric }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ $class->class_name }}</td>
                                            <td class="px-4 py-2">
                                                <input type="number" name="level_adjustments[{{ $class->class_numeric }}][zimsec_day]" step="0.01" min="0" value="0" class="w-20 border border-gray-300 rounded px-2 py-1 text-sm">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" name="level_adjustments[{{ $class->class_numeric }}][zimsec_boarding]" step="0.01" min="0" value="0" class="w-20 border border-gray-300 rounded px-2 py-1 text-sm">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" name="level_adjustments[{{ $class->class_numeric }}][cambridge_day]" step="0.01" min="0" value="0" class="w-20 border border-gray-300 rounded px-2 py-1 text-sm">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" name="level_adjustments[{{ $class->class_numeric }}][cambridge_boarding]" step="0.01" min="0" value="0" class="w-20 border border-gray-300 rounded px-2 py-1 text-sm">
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <p class="text-xs text-amber-600 mt-2">Level adjustments are ADDED to base fees. Enter 0 for no adjustment.</p>
                        @else
                        <p class="text-center text-gray-500 py-4">No classes defined yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Teacher Attendance Settings Section -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-700 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Teacher Attendance Settings
                        </h3>
                    </div>

                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <!-- Session Mode Toggle -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">School Session Mode</label>
                            <div class="flex flex-wrap gap-3">
                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all border-amber-500 bg-amber-50" id="morning_mode_label">
                                    <input type="radio" name="session_mode" value="morning" checked
                                           class="h-4 w-4 text-amber-600 focus:ring-amber-500"
                                           onchange="toggleAttendanceSessionMode()">
                                    <div class="ml-3">
                                        <span class="block font-medium text-gray-900">Morning Session</span>
                                        <span class="block text-xs text-gray-500">Morning only (e.g., 07:30 - 12:30)</span>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all border-gray-200 hover:border-gray-300" id="afternoon_mode_label">
                                    <input type="radio" name="session_mode" value="afternoon"
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500"
                                           onchange="toggleAttendanceSessionMode()">
                                    <div class="ml-3">
                                        <span class="block font-medium text-gray-900">Afternoon Session</span>
                                        <span class="block text-xs text-gray-500">Afternoon only (e.g., 12:30 - 17:30)</span>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all border-gray-200 hover:border-gray-300" id="dual_mode_label">
                                    <input type="radio" name="session_mode" value="dual"
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500"
                                           onchange="toggleAttendanceSessionMode()">
                                    <div class="ml-3">
                                        <span class="block font-medium text-gray-900">Dual Session</span>
                                        <span class="block text-xs text-gray-500">Morning & Afternoon sessions</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Morning/Single Session Times -->
                        <div class="bg-white p-4 rounded border border-gray-200 mb-4">
                            <h4 class="font-medium text-amber-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span id="morning_session_label">Work Hours</span>
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-In Time</label>
                                    <input type="time" name="check_in_time" id="check_in_time" value="07:30"
                                           class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-Out Time</label>
                                    <input type="time" name="check_out_time" id="check_out_time" value="16:30"
                                           class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-purple-500">
                                </div>
                            </div>
                        </div>

                        <!-- Afternoon Session Times (hidden by default) -->
                        <div id="afternoon_session_section" class="bg-white p-4 rounded border border-gray-200 mb-4 hidden">
                            <h4 class="font-medium text-indigo-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                Afternoon Session
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-In Time</label>
                                    <input type="time" name="afternoon_check_in_time" id="afternoon_check_in_time" value="12:30"
                                           class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-Out Time</label>
                                    <input type="time" name="afternoon_check_out_time" id="afternoon_check_out_time" value="17:30"
                                           class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-purple-500">
                                </div>
                            </div>
                        </div>

                        <!-- Grace Period -->
                        <div class="bg-white p-4 rounded border border-gray-200">
                            <div class="flex items-center">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Late Grace Period (minutes)</label>
                                    <input type="number" name="late_grace_minutes" id="late_grace_minutes" value="0" min="0" max="60"
                                           class="w-24 border rounded px-4 py-2 focus:ring-2 focus:ring-purple-500">
                                </div>
                                <span class="ml-4 text-sm text-gray-500">Minutes after check-in time before marking as late</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('results_status.index') }}" class="btn btn-secondary bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-6 rounded">Cancel</a>
                    <button type="submit" class="btn btn-primary bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded">Create Term</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
let zimsecDayFeeIndex = 1;
let zimsecBoardingFeeIndex = 1;
let cambridgeDayFeeIndex = 1;
let cambridgeBoardingFeeIndex = 1;

// Switch curriculum tab
function switchCurriculumTab(curriculum) {
    document.querySelectorAll('.curriculum-panel').forEach(panel => panel.classList.add('hidden'));
    document.querySelectorAll('.curriculum-tab').forEach(tab => {
        tab.classList.remove('border-indigo-500', 'text-indigo-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    document.getElementById(curriculum + '-panel').classList.remove('hidden');
    const activeTab = document.getElementById(curriculum + '-tab');
    activeTab.classList.add('border-indigo-500', 'text-indigo-600');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
}

// Function to calculate totals
function updateTotals() {
    let zimsecDayTotal = 0;
    document.querySelectorAll('.zimsec-day-fee-amount').forEach(input => { zimsecDayTotal += parseFloat(input.value) || 0; });
    document.getElementById('zimsecDayTotalDisplay').textContent = '$' + zimsecDayTotal.toFixed(2);
    document.getElementById('totalZimsecDayDisplay').textContent = '$' + zimsecDayTotal.toFixed(2);
    
    let zimsecBoardingTotal = 0;
    document.querySelectorAll('.zimsec-boarding-fee-amount').forEach(input => { zimsecBoardingTotal += parseFloat(input.value) || 0; });
    document.getElementById('zimsecBoardingTotalDisplay').textContent = '$' + zimsecBoardingTotal.toFixed(2);
    document.getElementById('totalZimsecBoardingDisplay').textContent = '$' + zimsecBoardingTotal.toFixed(2);
    
    let cambridgeDayTotal = 0;
    document.querySelectorAll('.cambridge-day-fee-amount').forEach(input => { cambridgeDayTotal += parseFloat(input.value) || 0; });
    document.getElementById('cambridgeDayTotalDisplay').textContent = '$' + cambridgeDayTotal.toFixed(2);
    document.getElementById('totalCambridgeDayDisplay').textContent = '$' + cambridgeDayTotal.toFixed(2);
    
    let cambridgeBoardingTotal = 0;
    document.querySelectorAll('.cambridge-boarding-fee-amount').forEach(input => { cambridgeBoardingTotal += parseFloat(input.value) || 0; });
    document.getElementById('cambridgeBoardingTotalDisplay').textContent = '$' + cambridgeBoardingTotal.toFixed(2);
    document.getElementById('totalCambridgeBoardingDisplay').textContent = '$' + cambridgeBoardingTotal.toFixed(2);
}

const feeTypeOptions = `<option value="">Select Fee Type</option>@foreach($feeTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach`;

function toggleAttendanceSessionMode() {
    const mode = document.querySelector('input[name="session_mode"]:checked').value;
    const morningSection = document.querySelector('#morning_session_label').closest('.bg-white');
    const afternoonSection = document.getElementById('afternoon_session_section');
    const morningLabel = document.getElementById('morning_mode_label');
    const afternoonLabel = document.getElementById('afternoon_mode_label');
    const dualLabel = document.getElementById('dual_mode_label');
    
    // Reset all labels
    [morningLabel, afternoonLabel, dualLabel].forEach(label => {
        label.classList.remove('border-amber-500', 'bg-amber-50', 'border-indigo-500', 'bg-indigo-50', 'border-purple-500', 'bg-purple-50');
        label.classList.add('border-gray-200');
    });
    
    // Hide both sections initially
    morningSection.classList.add('hidden');
    afternoonSection.classList.add('hidden');
    
    if (mode === 'morning') {
        morningLabel.classList.remove('border-gray-200');
        morningLabel.classList.add('border-amber-500', 'bg-amber-50');
        morningSection.classList.remove('hidden');
        document.getElementById('morning_session_label').textContent = 'Morning Session';
        document.getElementById('check_in_time').value = '07:30';
        document.getElementById('check_out_time').value = '12:30';
    } else if (mode === 'afternoon') {
        afternoonLabel.classList.remove('border-gray-200');
        afternoonLabel.classList.add('border-indigo-500', 'bg-indigo-50');
        morningSection.classList.remove('hidden');
        document.getElementById('morning_session_label').textContent = 'Afternoon Session';
        document.getElementById('check_in_time').value = '12:30';
        document.getElementById('check_out_time').value = '17:30';
    } else if (mode === 'dual') {
        dualLabel.classList.remove('border-gray-200');
        dualLabel.classList.add('border-purple-500', 'bg-purple-50');
        morningSection.classList.remove('hidden');
        afternoonSection.classList.remove('hidden');
        document.getElementById('morning_session_label').textContent = 'Morning Session';
        document.getElementById('check_in_time').value = '07:30';
        document.getElementById('check_out_time').value = '12:30';
        document.getElementById('afternoon_check_in_time').value = '12:30';
        document.getElementById('afternoon_check_out_time').value = '17:30';
    }
}

function createFeeRow(prefix, index, colorClass) {
    return `<div class="fee-type-row bg-white p-3 rounded border border-gray-200">
        <div class="grid grid-cols-12 gap-3">
            <div class="col-span-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Fee Type</label>
                <select name="${prefix}[${index}][fee_type_id]" class="w-full border rounded px-3 py-2 text-sm" required>${feeTypeOptions}</select>
            </div>
            <div class="col-span-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount ($)</label>
                <input type="number" name="${prefix}[${index}][amount]" step="0.01" min="0" class="w-full border rounded px-3 py-2 text-sm ${prefix.replace('_', '-')}-amount" placeholder="0.00" required>
            </div>
            <div class="col-span-2 flex items-end gap-1">
                <button type="button" class="add-${prefix.replace('_', '-')} bg-green-500 hover:bg-green-600 text-white px-2 py-2 rounded text-xs" title="Add">+</button>
                <button type="button" class="remove-${prefix.replace('_', '-')} bg-red-500 hover:bg-red-600 text-white px-2 py-2 rounded text-xs" title="Remove">-</button>
            </div>
        </div>
    </div>`;
}

function setupFeeContainer(containerId, addBtnClass, removeBtnClass, prefix, indexVar) {
    const container = document.getElementById(containerId);
    if (!container) return;
    container.addEventListener('click', function(e) {
        if (e.target.closest('.' + addBtnClass)) {
            const idx = window[indexVar]++;
            const row = createFeeRow(prefix, idx, '');
            e.target.closest('.fee-type-row').insertAdjacentHTML('afterend', row);
            updateTotals();
        }
        if (e.target.closest('.' + removeBtnClass)) {
            const rows = container.querySelectorAll('.fee-type-row');
            if (rows.length > 1) { e.target.closest('.fee-type-row').remove(); updateTotals(); }
            else { alert('You must have at least one fee type.'); }
        }
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('zimsec-day-fee-amount') || 
            e.target.classList.contains('zimsec-boarding-fee-amount') ||
            e.target.classList.contains('cambridge-day-fee-amount') ||
            e.target.classList.contains('cambridge-boarding-fee-amount')) {
            updateTotals();
        }
    });
    updateTotals();
    
    setupFeeContainer('zimsecDayFeesContainer', 'add-zimsec-day-fee', 'remove-zimsec-day-fee', 'zimsec_day_fees', 'zimsecDayFeeIndex');
    setupFeeContainer('zimsecBoardingFeesContainer', 'add-zimsec-boarding-fee', 'remove-zimsec-boarding-fee', 'zimsec_boarding_fees', 'zimsecBoardingFeeIndex');
    setupFeeContainer('cambridgeDayFeesContainer', 'add-cambridge-day-fee', 'remove-cambridge-day-fee', 'cambridge_day_fees', 'cambridgeDayFeeIndex');
    setupFeeContainer('cambridgeBoardingFeesContainer', 'add-cambridge-boarding-fee', 'remove-cambridge-boarding-fee', 'cambridge_boarding_fees', 'cambridgeBoardingFeeIndex');
});
</script>
@endpush