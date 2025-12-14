<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@php
    $totalArrears = 0;
    $allTerms = \App\ResultsStatus::all();
    foreach ($parents->children as $child) {
        $childFees = 0;
        foreach ($allTerms as $term) {
            $childFees += floatval($term->total_fees);
        }
        $childPaid = floatval(\App\StudentPayment::where('student_id', $child->id)->sum('amount_paid'));
        $totalArrears += ($childFees - $childPaid);
    }
@endphp

<!-- Summary Cards -->
<div class="w-full block mt-8">
    <div class="flex flex-wrap sm:flex-no-wrap justify-between gap-4">
        <div class="w-full sm:max-w-sm bg-blue-100 text-center border border-blue-300 rounded px-8 py-6 my-4 sm:my-0">
            <h3 class="text-blue-700 uppercase font-bold">
                <span class="text-4xl">{{ sprintf("%02d", $parents->children_count) }}</span>
                <span class="leading-tight block mt-1">Children</span>
            </h3>
        </div>
        
        @if($totalArrears > 0)
        <div class="w-full sm:max-w-sm bg-red-100 text-center border border-red-300 rounded px-8 py-6 my-4 sm:my-0">
            <h3 class="text-red-700 uppercase font-bold">
                <span class="text-4xl">${{ number_format($totalArrears, 2) }}</span>
                <span class="leading-tight block mt-1">Outstanding Arrears</span>
            </h3>
        </div>
        @else
        <div class="w-full sm:max-w-sm bg-green-100 text-center border border-green-300 rounded px-8 py-6 my-4 sm:my-0">
            <h3 class="text-green-700 uppercase font-bold">
                <span class="text-4xl">✓</span>
                <span class="leading-tight block mt-1">Fees Paid Up</span>
            </h3>
        </div>
        @endif
    </div>
</div>

<!-- Student Assessment & Attendance Section -->
@foreach ($parents->children as $child)
<div class="w-full block mt-6">
    <div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-4">
            <h3 class="text-lg font-bold flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                {{ $child->user->name ?? 'Student' }} - Academic Performance
                <span class="ml-2 px-2 py-1 text-xs bg-white/20 rounded-full">{{ $child->class->class_name ?? 'N/A' }}</span>
            </h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Assessment Grade Chart -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Subject Performance
                    </h4>
                    @if(isset($assessmentData[$child->id]) && !empty($assessmentData[$child->id]['subject_percentages']))
                    <div style="height: 300px;">
                        <canvas id="assessmentChart{{ $child->id }}"></canvas>
                    </div>
                    @else
                    <div class="flex items-center justify-center h-48 text-gray-500">
                        <div class="text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="mt-2">No assessment data available</p>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Attendance Summary -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Attendance Summary
                    </h4>
                    @if(isset($attendanceData[$child->id]) && $attendanceData[$child->id]['total'] > 0)
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-green-100 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-green-600">{{ $attendanceData[$child->id]['present'] }}</div>
                            <div class="text-sm text-green-700">Present</div>
                        </div>
                        <div class="bg-red-100 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-red-600">{{ $attendanceData[$child->id]['absent'] }}</div>
                            <div class="text-sm text-red-700">Absent</div>
                        </div>
                        <div class="bg-yellow-100 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-yellow-600">{{ $attendanceData[$child->id]['late'] }}</div>
                            <div class="text-sm text-yellow-700">Late</div>
                        </div>
                        <div class="bg-blue-100 rounded-lg p-4 text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ $attendanceData[$child->id]['percentage'] }}%</div>
                            <div class="text-sm text-blue-700">Attendance Rate</div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                            <span>Attendance Rate</span>
                            <span>{{ $attendanceData[$child->id]['percentage'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="h-3 rounded-full {{ $attendanceData[$child->id]['percentage'] >= 80 ? 'bg-green-500' : ($attendanceData[$child->id]['percentage'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $attendanceData[$child->id]['percentage'] }}%"></div>
                        </div>
                    </div>
                    @else
                    <div class="flex items-center justify-center h-48 text-gray-500">
                        <div class="text-center">
                            <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="mt-2">No attendance data available</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Assessment Details Table -->
            @if(isset($assessmentData[$child->id]) && !empty($assessmentData[$child->id]['subject_marks']))
            <div class="mt-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Assessment Details by Subject
                </h4>
                <div class="space-y-4">
                    @foreach($assessmentData[$child->id]['subject_marks'] as $subject => $data)
                    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 px-4 py-3 border-b flex justify-between items-center">
                            <span class="font-semibold text-gray-800">{{ $subject }}</span>
                            <span class="px-3 py-1 text-sm font-bold rounded-full {{ $assessmentData[$child->id]['subject_percentages'][$subject] >= 75 ? 'bg-green-100 text-green-800' : ($assessmentData[$child->id]['subject_percentages'][$subject] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $assessmentData[$child->id]['subject_percentages'][$subject] }}%
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Topic</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Mark</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($data['assessments'] as $assessment)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $assessment['topic'] ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">
                                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">{{ $assessment['type'] ?? 'Assessment' }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center">
                                            <span class="font-semibold {{ ($assessment['total'] > 0 && ($assessment['mark'] / $assessment['total']) >= 0.5) ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $assessment['mark'] ?? 0 }}/{{ $assessment['total'] ?? 0 }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $assessment['comment'] ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if(isset($assessmentData[$child->id]) && !empty($assessmentData[$child->id]['subject_percentages']))
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('assessmentChart{{ $child->id }}');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($assessmentData[$child->id]['subject_percentages'])) !!},
                datasets: [{
                    label: 'Performance (%)',
                    data: {!! json_encode(array_values($assessmentData[$child->id]['subject_percentages'])) !!},
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(6, 182, 212, 0.8)',
                        'rgba(132, 204, 22, 0.8)'
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(16, 185, 129, 1)',
                        'rgba(245, 158, 11, 1)',
                        'rgba(239, 68, 68, 1)',
                        'rgba(139, 92, 246, 1)',
                        'rgba(236, 72, 153, 1)',
                        'rgba(6, 182, 212, 1)',
                        'rgba(132, 204, 22, 1)'
                    ],
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { callback: function(value) { return value + '%'; } }
                    }
                }
            }
        });
    }
});
</script>
@endif
@endforeach

<!-- Arrears Breakdown Section -->
@if($totalArrears > 0)
<div class="w-full block mt-4">
    <div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-hidden">
        <div class="bg-red-600 text-white px-6 py-4">
            <h3 class="text-lg font-bold flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Outstanding Fees Breakdown
            </h3>
        </div>
        <div class="p-4">
            @foreach ($parents->children as $child)
                @php
                    $childArrears = [];
                    $childTotalArrears = 0;
                    foreach ($allTerms as $term) {
                        $termFees = floatval($term->total_fees);
                        $termPaid = floatval(\App\StudentPayment::where('student_id', $child->id)
                            ->where('results_status_id', $term->id)
                            ->sum('amount_paid'));
                        $termArrears = $termFees - $termPaid;
                        if ($termArrears > 0) {
                            $childArrears[] = [
                                'term' => ucfirst($term->result_period) . ' ' . $term->year,
                                'fees' => $termFees,
                                'paid' => $termPaid,
                                'arrears' => $termArrears
                            ];
                            $childTotalArrears += $termArrears;
                        }
                    }
                @endphp
                
                @if($childTotalArrears > 0)
                <div class="mb-4 border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-4 py-3 border-b flex justify-between items-center">
                        <div>
                            <span class="font-semibold text-gray-900">{{ $child->user->name }}</span>
                            <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">{{ $child->class->class_name ?? 'N/A' }}</span>
                        </div>
                        <span class="text-red-600 font-bold">${{ number_format($childTotalArrears, 2) }}</span>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Term</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Fees</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Outstanding</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($childArrears as $arrear)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ $arrear['term'] }}</td>
                                <td class="px-4 py-2 text-sm text-gray-600 text-right">${{ number_format($arrear['fees'], 2) }}</td>
                                <td class="px-4 py-2 text-sm text-green-600 text-right">${{ number_format($arrear['paid'], 2) }}</td>
                                <td class="px-4 py-2 text-sm text-red-600 font-semibold text-right">${{ number_format($arrear['arrears'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            @endforeach
            
            <div class="mt-4 p-4 bg-red-50 rounded-lg border border-red-200">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-700">Total Outstanding:</span>
                    <span class="text-2xl font-bold text-red-600">${{ number_format($totalArrears, 2) }}</span>
                </div>
                <p class="text-sm text-gray-500 mt-2">Please settle outstanding fees at your earliest convenience.</p>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Disciplinary Records Section -->
@php
    $childrenIds = $parents->children->pluck('id')->toArray();
    $disciplinaryRecords = \App\DisciplinaryRecord::with(['student.user', 'class'])
        ->whereIn('student_id', $childrenIds)
        ->orderBy('offense_date', 'desc')
        ->get();
@endphp

@if($disciplinaryRecords->count() > 0)
<div class="w-full block mt-4">
    <div class="bg-white border border-gray-300 rounded-lg shadow-sm overflow-hidden">
        <div class="bg-orange-600 text-white px-6 py-4">
            <h3 class="text-lg font-bold flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Disciplinary Records
            </h3>
        </div>
        <div class="p-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Offense Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judgement</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($disciplinaryRecords as $record)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">
                                {{ $record->offense_date instanceof \Carbon\Carbon ? $record->offense_date->format('Y-m-d') : $record->offense_date }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $record->student->user->name ?? 'Unknown' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $record->offense_type }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ Str::limit($record->description, 50) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @if($record->offense_status == 'Resolved') bg-green-100 text-green-800
                                    @elseif($record->offense_status == 'Pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $record->offense_status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $record->judgement ?? 'Pending' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Children Info Cards -->
<div class="w-full block mt-4 sm:mt-8">
    <div class="flex flex-wrap sm:flex-no-wrap justify-between">
        @foreach ($parents->children as $key => $children)
            <div class="w-full bg-gray-200 text-center border border-gray-300 rounded px-8 py-6 mb-4 {{ ($key>=1) ? 'ml-0 sm:ml-2' : '' }} {{ ($parents->children_count===1) ? 'sm:max-w-sm' : '' }}">
                <div class="text-gray-700 font-bold">
                    <div class="mb-6">
                        <div class="text-lg uppercase">{{ $children->user->name }}</div>
                        <div class="text-sm lowercase leading-tight">{{ $children->user->email }}</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="w-1/2 text-sm text-right">Class :</div>
                        <div class="w-1/2 text-sm text-left ml-2">{{ $children->class->class_name ?? 'N/A' }}</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="w-1/2 text-sm text-right">Roll No :</div>
                        <div class="w-1/2 text-sm text-left ml-2">{{ $children->roll_number }}</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="w-1/2 text-sm text-right">Phone :</div>
                        <div class="w-1/2 text-sm text-left ml-2">{{ $children->phone }}</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="w-1/2 text-sm text-right">Gender :</div>
                        <div class="w-1/2 text-sm text-left ml-2">{{ $children->gender }}</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="w-1/2 text-sm text-right">Date of Birth :</div>
                        <div class="w-1/2 text-sm text-left ml-2">{{ $children->dateofbirth }}</div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="w-1/2 text-sm text-right">Address :</div>
                        <div class="w-1/2 text-sm text-left ml-2">{{ $children->current_address }}</div>
                    </div>
                    <div class="mt-6">
                        <a href="{{ route('attendance.show',$children->id) }}" class="bg-blue-600 inline-block mb-4 text-sm text-white uppercase font-semibold px-4 py-2 border border-gray-400 rounded">Attendance Report</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>