@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Grocery Arrears</h1>
        <a href="{{ route('finance.grocery-arrears.export', request()->query()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Export Excel
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Grocery Items Summary</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="text-center p-3 bg-orange-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">Items B/F</p>
                <p class="text-xl font-bold text-orange-600">{{ $totalBalanceBf ?? 0 }} <span class="text-xs font-normal">items</span></p>
            </div>
            <div class="text-center p-3 bg-blue-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">Current Term</p>
                <p class="text-xl font-bold text-blue-600">{{ $totalCurrentTermOwed ?? 0 }} <span class="text-xs font-normal">items</span></p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">Total Required</p>
                <p class="text-xl font-bold text-gray-800">{{ $totalGroceryItems ?? 0 }} <span class="text-xs font-normal">items</span></p>
            </div>
            <div class="text-center p-3 bg-green-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">Total Provided</p>
                <p class="text-xl font-bold text-green-600">{{ $totalProvided ?? 0 }} <span class="text-xs font-normal">items</span></p>
            </div>
            <div class="text-center p-3 bg-red-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">Outstanding</p>
                <p class="text-xl font-bold text-red-600">{{ $totalOutstanding ?? 0 }} <span class="text-xs font-normal">items</span></p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('finance.grocery-arrears') }}" id="filterForm">
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Roll No." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                    <select name="class_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->class_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Student Type</label>
                    <select name="student_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Types</option>
                        <option value="day" {{ request('student_type') == 'day' ? 'selected' : '' }}>Day</option>
                        <option value="boarding" {{ request('student_type') == 'boarding' ? 'selected' : '' }}>Boarding</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Show</label>
                    <select name="show_arrears_only" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Students</option>
                        <option value="1" {{ request('show_arrears_only') == '1' ? 'selected' : '' }}>With Arrears Only</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        Filter
                    </button>
                    <a href="{{ route('finance.grocery-arrears') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
                        Clear
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- Students Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No.</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance B/F</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Term</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Owed</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $student)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $student->roll_number ?? '-' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $student->user->name ?? $student->name }}</div>
                            <div class="text-xs text-gray-500">{{ $student->parent->user->name ?? 'No Parent' }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $student->class->class_name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full {{ ($student->student_type ?? 'day') == 'boarding' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($student->student_type ?? 'day') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold {{ ($student->balance_bf ?? 0) > 0 ? 'text-orange-600' : 'text-gray-500' }}">
                            {{ $student->balance_bf ?? 0 }} items
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-blue-600">
                            {{ $student->current_term_owed ?? 0 }} items
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-bold {{ ($student->total_owed ?? 0) > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $student->total_owed ?? 0 }} items
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if(($student->total_owed ?? 0) > 0)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Arrears
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Cleared
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.groceries.student-history', $student->id) }}" class="text-blue-600 hover:text-blue-900" title="View History">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if(count($student->arrears_breakdown ?? []) > 0)
                                <button type="button" onclick="showBreakdown({{ $student->id }}, '{{ addslashes($student->user->name ?? $student->name) }}', {{ json_encode($student->arrears_breakdown) }})" class="text-orange-600 hover:text-orange-900" title="View Breakdown">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-16 h-16 text-green-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-lg font-medium text-gray-600">No Students with Grocery Arrears</p>
                                <p class="text-sm text-gray-400">All students have cleared their grocery obligations.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Breakdown Modal -->
<div id="breakdownModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white mb-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">
                <svg class="w-5 h-5 inline-block mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Grocery Arrears Breakdown
            </h3>
            <button onclick="closeBreakdownModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <div class="mb-4 p-3 bg-orange-50 rounded-lg">
            <p class="text-sm text-gray-600">Student: <span id="modalStudentName" class="font-semibold text-gray-900"></span></p>
        </div>
        
        <div id="breakdownContent" class="space-y-3">
        </div>
        
        <div class="mt-4 flex justify-end">
            <button onclick="closeBreakdownModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function showBreakdown(studentId, studentName, breakdown) {
    document.getElementById('modalStudentName').textContent = studentName;
    
    let content = '';
    if (breakdown && breakdown.length > 0) {
        content = '<table class="min-w-full divide-y divide-gray-200">';
        content += '<thead class="bg-gray-50"><tr>';
        content += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Term</th>';
        content += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Items</th>';
        content += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Provided</th>';
        content += '<th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Missing</th>';
        content += '</tr></thead><tbody class="divide-y divide-gray-200">';
        
        breakdown.forEach(function(item) {
            content += '<tr>';
            content += '<td class="px-4 py-2 text-sm text-gray-900">' + item.term + '</td>';
            content += '<td class="px-4 py-2 text-sm text-gray-600">' + item.total_items + ' items</td>';
            content += '<td class="px-4 py-2 text-sm text-green-600">' + item.provided + ' items</td>';
            content += '<td class="px-4 py-2 text-sm font-semibold text-red-600">' + item.owed + ' items</td>';
            content += '</tr>';
            if (item.missing_items && item.missing_items.length > 0) {
                content += '<tr><td colspan="4" class="px-4 py-2 bg-red-50">';
                content += '<p class="text-xs text-gray-600 font-medium">Missing: <span class="text-red-600">' + item.missing_items.join(', ') + '</span></p>';
                content += '</td></tr>';
            }
        });
        
        content += '</tbody></table>';
    } else {
        content = '<p class="text-center text-gray-500 py-4">No arrears breakdown available.</p>';
    }
    
    document.getElementById('breakdownContent').innerHTML = content;
    document.getElementById('breakdownModal').classList.remove('hidden');
}

function closeBreakdownModal() {
    document.getElementById('breakdownModal').classList.add('hidden');
}

document.getElementById('breakdownModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBreakdownModal();
    }
});
</script>
@endsection
