@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.groceries.index') }}" class="mr-4 p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $class->class_name }} - Grocery List</h1>
            <p class="text-gray-500 mt-1">View student grocery list responses</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if($activeList)
    <!-- Active List Info -->
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-orange-800">Active Grocery List: {{ ucfirst($activeList->term) }} {{ $activeList->year }}</h3>
                <p class="text-sm text-orange-600 mt-1">{{ $activeList->items->count() }} items in list</p>
            </div>
            <button onclick="document.getElementById('viewItemsModal').classList.remove('hidden')" class="px-3 py-1 text-sm bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                View Items
            </button>
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Parent</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Items Bought</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($students as $index => $student)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                <span class="text-xs font-medium text-gray-600">{{ substr($student->user->name ?? 'S', 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $student->user->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">Roll: {{ $student->roll_number }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $student->parent->user->name ?? 'No Parent' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($student->grocery_response)
                            @if($student->grocery_response->acknowledged)
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Acknowledged</span>
                            @elseif($student->grocery_response->submitted)
                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                            @else
                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                            @endif
                        @else
                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">Not Started</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-sm">
                        @if($student->grocery_response && $student->grocery_response->items_bought)
                        <span class="font-medium text-blue-600">{{ count($student->grocery_response->items_bought) }}/{{ $activeList->items->count() }}</span>
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($student->grocery_response && $student->grocery_response->submitted)
                        <a href="{{ route('admin.groceries.response', $student->grocery_response->id) }}" class="inline-flex items-center px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 mr-1">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View
                        </a>
                        @if(!$student->grocery_response->acknowledged)
                        <form action="{{ route('admin.groceries.acknowledge', $student->grocery_response->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="inline-flex items-center px-3 py-1 text-xs bg-green-100 text-green-700 rounded-lg hover:bg-green-200">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Acknowledge
                            </button>
                        </form>
                        @endif
                        @else
                        <span class="text-gray-400 text-xs">No response yet</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">No students in this class</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
        <svg class="w-12 h-12 text-yellow-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <h3 class="text-lg font-semibold text-yellow-800 mb-2">No Active Grocery List</h3>
        <p class="text-yellow-600">There is no active grocery list for this class. Create one from the main groceries page.</p>
        <a href="{{ route('admin.groceries.index') }}" class="inline-block mt-4 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
            Go to Groceries
        </a>
    </div>
    @endif
</div>

@if($activeList)
<!-- View Items Modal -->
<div id="viewItemsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Grocery Items</h3>
            <button onclick="document.getElementById('viewItemsModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 max-h-96 overflow-y-auto">
            <ul class="space-y-2">
                @foreach($activeList->items as $item)
                <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-800">{{ $item->name }}</span>
                    @if($item->quantity)
                    <span class="text-sm text-gray-500">Qty: {{ $item->quantity }}</span>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
@endsection
