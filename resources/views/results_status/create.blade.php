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

                <!-- Fee Structure by Level Groups Section -->
                @if(isset($feeLevelGroups) && $feeLevelGroups->count() > 0)
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-700 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                            Fee Structure by Student Category
                        </h3>
                        <a href="{{ route('fee-level-groups.index') }}" class="text-xs text-rose-600 hover:text-rose-800 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Manage Level Groups
                        </a>
                    </div>

                    <div class="bg-rose-50 p-4 rounded-lg border border-rose-200">
                        <p class="text-sm text-gray-600 mb-4">
                            <svg class="w-4 h-4 inline mr-1 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Define different fee structures for student categories (e.g., Junior Forms 1-4, Senior Forms 5-6). Each category can have separate fees for existing and new students.
                        </p>

                        <!-- Level Group Tabs -->
                        <div class="border-b border-rose-200 mb-4">
                            <nav class="-mb-px flex flex-wrap gap-2">
                                @foreach($feeLevelGroups as $index => $group)
                                <button type="button" onclick="switchLevelGroupTab({{ $group->id }})" id="level-group-tab-{{ $group->id }}" 
                                    class="level-group-tab {{ $index === 0 ? 'border-rose-500 text-rose-600 bg-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} border-b-2 py-2 px-3 text-sm font-medium rounded-t-lg">
                                    {{ $group->name }}
                                    <span class="text-xs text-gray-400 ml-1">({{ $group->class_range }})</span>
                                </button>
                                @endforeach
                            </nav>
                        </div>

                        <!-- Level Group Panels -->
                        @foreach($feeLevelGroups as $index => $group)
                        <div id="level-group-panel-{{ $group->id }}" class="level-group-panel {{ $index !== 0 ? 'hidden' : '' }}">
                            <div class="bg-white rounded-lg border border-gray-200 p-4">
                                <h4 class="font-semibold text-gray-700 mb-3 flex items-center">
                                    <span class="w-8 h-8 rounded-full bg-rose-100 flex items-center justify-center mr-2">
                                        <span class="text-rose-600 font-bold text-sm">{{ $group->min_class_numeric }}-{{ $group->max_class_numeric }}</span>
                                    </span>
                                    {{ $group->name }} - {{ $group->class_range }}
                                </h4>

                                <!-- ZIMSEC Section -->
                                <div class="mb-4">
                                    <h5 class="text-sm font-bold text-indigo-600 mb-2 flex items-center">
                                        <span class="w-2 h-2 bg-indigo-500 rounded-full mr-2"></span>
                                        ZIMSEC Fees
                                    </h5>
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <!-- Existing Day Students -->
                                        <div class="bg-blue-50 p-3 rounded border border-blue-200">
                                            <div class="flex justify-between items-center mb-2">
                                                <h5 class="text-sm font-semibold text-blue-700">Day Scholar (Existing)</h5>
                                                <button type="button" onclick="addFeeStructureRow({{ $group->id }}, 'zimsec_day_existing')" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">+ Add</button>
                                            </div>
                                            <div class="space-y-2" id="group-{{ $group->id }}-zimsec-day-existing-container">
                                                <div class="fee-structure-row flex gap-2">
                                                    <select name="fee_structures[{{ $group->id }}][zimsec_day_existing][0][fee_type_id]" class="flex-1 border rounded px-2 py-1 text-xs">
                                                        <option value="">Select Fee</option>
                                                        @foreach($feeTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="number" name="fee_structures[{{ $group->id }}][zimsec_day_existing][0][amount]" step="0.01" min="0" placeholder="0.00" class="w-24 border rounded px-2 py-1 text-xs">
                                                    <button type="button" onclick="removeFeeStructureRow(this)" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">×</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- New Day Students -->
                                        <div class="bg-emerald-50 p-3 rounded border border-emerald-200">
                                            <div class="flex justify-between items-center mb-2">
                                                <h5 class="text-sm font-semibold text-emerald-700">Day Scholar (New Student)</h5>
                                                <button type="button" onclick="addFeeStructureRow({{ $group->id }}, 'zimsec_day_new')" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">+ Add</button>
                                            </div>
                                            <div class="space-y-2" id="group-{{ $group->id }}-zimsec-day-new-container">
                                                <div class="fee-structure-row flex gap-2">
                                                    <select name="fee_structures[{{ $group->id }}][zimsec_day_new][0][fee_type_id]" class="flex-1 border rounded px-2 py-1 text-xs">
                                                        <option value="">Select Fee</option>
                                                        @foreach($feeTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="number" name="fee_structures[{{ $group->id }}][zimsec_day_new][0][amount]" step="0.01" min="0" placeholder="0.00" class="w-24 border rounded px-2 py-1 text-xs">
                                                    <button type="button" onclick="removeFeeStructureRow(this)" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">×</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Existing Boarding Students -->
                                        <div class="bg-green-50 p-3 rounded border border-green-200">
                                            <div class="flex justify-between items-center mb-2">
                                                <h5 class="text-sm font-semibold text-green-700">Boarding (Existing)</h5>
                                                <button type="button" onclick="addFeeStructureRow({{ $group->id }}, 'zimsec_boarding_existing')" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">+ Add</button>
                                            </div>
                                            <div class="space-y-2" id="group-{{ $group->id }}-zimsec-boarding-existing-container">
                                                <div class="fee-structure-row flex gap-2">
                                                    <select name="fee_structures[{{ $group->id }}][zimsec_boarding_existing][0][fee_type_id]" class="flex-1 border rounded px-2 py-1 text-xs">
                                                        <option value="">Select Fee</option>
                                                        @foreach($feeTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="number" name="fee_structures[{{ $group->id }}][zimsec_boarding_existing][0][amount]" step="0.01" min="0" placeholder="0.00" class="w-24 border rounded px-2 py-1 text-xs">
                                                    <button type="button" onclick="removeFeeStructureRow(this)" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">×</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- New Boarding Students -->
                                        <div class="bg-amber-50 p-3 rounded border border-amber-200">
                                            <div class="flex justify-between items-center mb-2">
                                                <h5 class="text-sm font-semibold text-amber-700">Boarding (New Student)</h5>
                                                <button type="button" onclick="addFeeStructureRow({{ $group->id }}, 'zimsec_boarding_new')" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">+ Add</button>
                                            </div>
                                            <div class="space-y-2" id="group-{{ $group->id }}-zimsec-boarding-new-container">
                                                <div class="fee-structure-row flex gap-2">
                                                    <select name="fee_structures[{{ $group->id }}][zimsec_boarding_new][0][fee_type_id]" class="flex-1 border rounded px-2 py-1 text-xs">
                                                        <option value="">Select Fee</option>
                                                        @foreach($feeTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="number" name="fee_structures[{{ $group->id }}][zimsec_boarding_new][0][amount]" step="0.01" min="0" placeholder="0.00" class="w-24 border rounded px-2 py-1 text-xs">
                                                    <button type="button" onclick="removeFeeStructureRow(this)" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">×</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cambridge Section -->
                                <div class="mb-4">
                                    <h5 class="text-sm font-bold text-purple-600 mb-2 flex items-center">
                                        <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                                        Cambridge Fees
                                    </h5>
                                    <div id="group-{{ $group->id }}-cambridge-panel">
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <!-- Existing Day Students -->
                                        <div class="bg-purple-50 p-3 rounded border border-purple-200">
                                            <div class="flex justify-between items-center mb-2">
                                                <h5 class="text-sm font-semibold text-purple-700">Day Scholar (Existing)</h5>
                                                <button type="button" onclick="addFeeStructureRow({{ $group->id }}, 'cambridge_day_existing')" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">+ Add</button>
                                            </div>
                                            <div class="space-y-2" id="group-{{ $group->id }}-cambridge-day-existing-container">
                                                <div class="fee-structure-row flex gap-2">
                                                    <select name="fee_structures[{{ $group->id }}][cambridge_day_existing][0][fee_type_id]" class="flex-1 border rounded px-2 py-1 text-xs">
                                                        <option value="">Select Fee</option>
                                                        @foreach($feeTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="number" name="fee_structures[{{ $group->id }}][cambridge_day_existing][0][amount]" step="0.01" min="0" placeholder="0.00" class="w-24 border rounded px-2 py-1 text-xs">
                                                    <button type="button" onclick="removeFeeStructureRow(this)" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">×</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- New Day Students -->
                                        <div class="bg-pink-50 p-3 rounded border border-pink-200">
                                            <div class="flex justify-between items-center mb-2">
                                                <h5 class="text-sm font-semibold text-pink-700">Day Scholar (New Student)</h5>
                                                <button type="button" onclick="addFeeStructureRow({{ $group->id }}, 'cambridge_day_new')" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">+ Add</button>
                                            </div>
                                            <div class="space-y-2" id="group-{{ $group->id }}-cambridge-day-new-container">
                                                <div class="fee-structure-row flex gap-2">
                                                    <select name="fee_structures[{{ $group->id }}][cambridge_day_new][0][fee_type_id]" class="flex-1 border rounded px-2 py-1 text-xs">
                                                        <option value="">Select Fee</option>
                                                        @foreach($feeTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="number" name="fee_structures[{{ $group->id }}][cambridge_day_new][0][amount]" step="0.01" min="0" placeholder="0.00" class="w-24 border rounded px-2 py-1 text-xs">
                                                    <button type="button" onclick="removeFeeStructureRow(this)" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">×</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Existing Boarding Students -->
                                        <div class="bg-orange-50 p-3 rounded border border-orange-200">
                                            <div class="flex justify-between items-center mb-2">
                                                <h5 class="text-sm font-semibold text-orange-700">Boarding (Existing)</h5>
                                                <button type="button" onclick="addFeeStructureRow({{ $group->id }}, 'cambridge_boarding_existing')" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">+ Add</button>
                                            </div>
                                            <div class="space-y-2" id="group-{{ $group->id }}-cambridge-boarding-existing-container">
                                                <div class="fee-structure-row flex gap-2">
                                                    <select name="fee_structures[{{ $group->id }}][cambridge_boarding_existing][0][fee_type_id]" class="flex-1 border rounded px-2 py-1 text-xs">
                                                        <option value="">Select Fee</option>
                                                        @foreach($feeTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="number" name="fee_structures[{{ $group->id }}][cambridge_boarding_existing][0][amount]" step="0.01" min="0" placeholder="0.00" class="w-24 border rounded px-2 py-1 text-xs">
                                                    <button type="button" onclick="removeFeeStructureRow(this)" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">×</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- New Boarding Students -->
                                        <div class="bg-red-50 p-3 rounded border border-red-200">
                                            <div class="flex justify-between items-center mb-2">
                                                <h5 class="text-sm font-semibold text-red-700">Boarding (New Student)</h5>
                                                <button type="button" onclick="addFeeStructureRow({{ $group->id }}, 'cambridge_boarding_new')" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">+ Add</button>
                                            </div>
                                            <div class="space-y-2" id="group-{{ $group->id }}-cambridge-boarding-new-container">
                                                <div class="fee-structure-row flex gap-2">
                                                    <select name="fee_structures[{{ $group->id }}][cambridge_boarding_new][0][fee_type_id]" class="flex-1 border rounded px-2 py-1 text-xs">
                                                        <option value="">Select Fee</option>
                                                        @foreach($feeTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="number" name="fee_structures[{{ $group->id }}][cambridge_boarding_new][0][amount]" step="0.01" min="0" placeholder="0.00" class="w-24 border rounded px-2 py-1 text-xs">
                                                    <button type="button" onclick="removeFeeStructureRow(this)" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">×</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        @endforeach

                        <p class="text-xs text-rose-600 mt-3">
                            <strong>Note:</strong> Both ZIMSEC and Cambridge fees are shown. Fill in the fees for each curriculum as needed.
                        </p>
                    </div>
                </div>
                @else
                <div class="mb-6">
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-amber-500 mt-0.5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.724-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <h4 class="text-sm font-semibold text-amber-800">No Fee Level Groups Configured</h4>
                                <p class="text-xs text-amber-700 mt-1">To set different fees for different class levels (e.g., Form 1-4 vs Form 5-6), please create Fee Level Groups first.</p>
                                <a href="{{ route('fee-level-groups.create') }}" class="inline-flex items-center mt-2 text-xs font-medium text-amber-700 hover:text-amber-900">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Create Fee Level Groups
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

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

// Switch level group tab
function switchLevelGroupTab(groupId) {
    document.querySelectorAll('.level-group-panel').forEach(panel => panel.classList.add('hidden'));
    document.querySelectorAll('.level-group-tab').forEach(tab => {
        tab.classList.remove('border-rose-500', 'text-rose-600', 'bg-white');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    const panel = document.getElementById('level-group-panel-' + groupId);
    const tab = document.getElementById('level-group-tab-' + groupId);
    if (panel) panel.classList.remove('hidden');
    if (tab) {
        tab.classList.add('border-rose-500', 'text-rose-600', 'bg-white');
        tab.classList.remove('border-transparent', 'text-gray-500');
    }
}

// Switch curriculum tab within a level group
function switchGroupCurriculumTab(groupId, curriculum) {
    document.querySelectorAll('.group-curriculum-panel-' + groupId).forEach(panel => panel.classList.add('hidden'));
    document.querySelectorAll('.group-curriculum-tab-' + groupId).forEach(tab => {
        tab.classList.remove('border-indigo-500', 'text-indigo-600');
        tab.classList.add('border-transparent', 'text-gray-500');
    });
    const panel = document.getElementById('group-' + groupId + '-' + curriculum + '-panel');
    const tab = document.getElementById('group-' + groupId + '-' + curriculum + '-tab');
    if (panel) panel.classList.remove('hidden');
    if (tab) {
        tab.classList.add('border-indigo-500', 'text-indigo-600');
        tab.classList.remove('border-transparent', 'text-gray-500');
    }
}

// Fee Structure row counters for each group/category
const feeStructureIndexes = {};

// Add fee structure row for level groups
function addFeeStructureRow(groupId, category) {
    const containerId = 'group-' + groupId + '-' + category.replace(/_/g, '-') + '-container';
    const container = document.getElementById(containerId);
    if (!container) return;
    
    const key = groupId + '_' + category;
    if (!feeStructureIndexes[key]) {
        feeStructureIndexes[key] = container.querySelectorAll('.fee-structure-row').length;
    }
    const idx = feeStructureIndexes[key]++;
    
    const feeOptions = `<option value="">Select Fee</option>@foreach($feeTypes as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach`;
    
    const html = `
        <div class="fee-structure-row flex gap-2">
            <select name="fee_structures[${groupId}][${category}][${idx}][fee_type_id]" class="flex-1 border rounded px-2 py-1 text-xs">
                ${feeOptions}
            </select>
            <input type="number" name="fee_structures[${groupId}][${category}][${idx}][amount]" step="0.01" min="0" placeholder="0.00" class="w-24 border rounded px-2 py-1 text-xs">
            <button type="button" onclick="removeFeeStructureRow(this)" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">×</button>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
}

// Remove fee structure row
function removeFeeStructureRow(btn) {
    const row = btn.closest('.fee-structure-row');
    const container = row.parentElement;
    const rows = container.querySelectorAll('.fee-structure-row');
    
    if (rows.length > 1) {
        row.remove();
    } else {
        alert('You must have at least one fee row.');
    }
}
</script>
@endpush
