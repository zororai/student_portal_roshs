@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Grocery History</h1>
            <p class="text-gray-500 mt-1">{{ $student->user->name }} - {{ $student->class->class_name ?? 'No Class' }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.groceries.student-history.print', $student->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print PDF
            </a>
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-600 font-medium">Total Terms</p>
                <p class="text-3xl font-bold text-blue-700">{{ count($historyData) }}</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-sm text-green-600 font-medium">Items Provided</p>
                <p class="text-3xl font-bold text-green-700">
                    @php
                        $totalProvided = 0;
                        $totalExtra = 0;
                        foreach($historyData as $data) {
                            $totalProvided += $data['provided_count'] ?? 0;
                            $totalExtra += count($data['response']->extra_items ?? []);
                        }
                    @endphp
                    {{ $totalProvided }} <span class="text-base font-normal">items</span>
                    @if($totalExtra > 0)
                    <span class="ml-1 px-2 py-0.5 text-sm bg-green-200 text-green-800 rounded">+{{ $totalExtra }} extra</span>
                    @endif
                </p>
            </div>
            <div class="text-center p-4 {{ ($totalOwedItems ?? 0) > 0 ? 'bg-red-50' : 'bg-green-50' }} rounded-lg">
                <p class="text-sm {{ ($totalOwedItems ?? 0) > 0 ? 'text-red-600' : 'text-green-600' }} font-medium">Outstanding Items</p>
                <p class="text-3xl font-bold {{ ($totalOwedItems ?? 0) > 0 ? 'text-red-700' : 'text-green-700' }}">{{ $totalOwedItems ?? 0 }} <span class="text-base font-normal">items</span></p>
            </div>
        </div>
    </div>

    <!-- History Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-800">Grocery Responses History</h2>
        </div>
        
        @if(count($historyData) > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Term/Year</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Submitted</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total Items</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Provided</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Extra Items</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Missing</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($historyData as $data)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ ucfirst($data['term']) }} {{ $data['year'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($data['response']->submitted_at)
                                {{ $data['response']->submitted_at->format('M d, Y') }}
                            @else
                                <span class="text-gray-400">Not submitted</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $data['total_items'] }} items</td>
                        <td class="px-6 py-4 text-sm text-green-600 font-medium">{{ $data['provided_count'] }} items</td>
                        <td class="px-6 py-4 text-sm">
                            @if($data['response']->extra_items && count($data['response']->extra_items) > 0)
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">+{{ count($data['response']->extra_items) }} extra</span>
                            @else
                            <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm {{ ($data['owed_count'] ?? 0) > 0 ? 'text-red-600' : 'text-green-600' }} font-medium">
                            {{ $data['owed_count'] }} items
                            @if(count($data['missing_items'] ?? []) > 0)
                                <button type="button" onclick="showMissing('{{ addslashes(implode(', ', $data['missing_items'])) }}')" class="ml-1 text-orange-500 hover:text-orange-700" title="View missing items">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($data['response']->acknowledged)
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Acknowledged</span>
                            @elseif($data['response']->submitted)
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.groceries.response', $data['response']->id) }}" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 text-lg font-medium">No grocery history found</p>
            <p class="text-gray-400 text-sm mt-1">This student has no grocery responses yet</p>
        </div>
        @endif
    </div>
</div>

<script>
function showMissing(items) {
    alert('Missing Items:\n\n' + items);
}
</script>
@endsection
