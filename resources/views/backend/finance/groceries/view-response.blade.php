@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.groceries.class', $response->student->class_id) }}" class="mr-4 p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Grocery Response</h1>
            <p class="text-gray-500 mt-1">{{ $response->student->user->name ?? 'Student' }}</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Student Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Student Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500 uppercase">Student Name</p>
                    <p class="text-gray-800 font-medium">{{ $response->student->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Parent Name</p>
                    <p class="text-gray-800 font-medium">{{ $response->parent->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Submitted At</p>
                    <p class="text-gray-800">{{ $response->submitted_at ? $response->submitted_at->format('M d, Y h:i A') : 'Not submitted' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 uppercase">Status</p>
                    @if($response->acknowledged)
                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Acknowledged</span>
                    @elseif($response->submitted)
                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                    @else
                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                    @endif
                </div>
                @if($response->notes)
                <div>
                    <p class="text-xs text-gray-500 uppercase">Parent Notes</p>
                    <p class="text-gray-800">{{ $response->notes }}</p>
                </div>
                @endif
            </div>

            @if($response->submitted && !$response->acknowledged)
            <form action="{{ route('admin.groceries.acknowledge', $response->id) }}" method="POST" class="mt-6">
                @csrf
                @method('PUT')
                <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Acknowledge Receipt
                </button>
            </form>
            @endif
        </div>

        <!-- Items List -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Grocery Items</h3>
            <div class="space-y-2">
                @foreach($response->groceryList->items as $item)
                @php 
                    $bought = in_array($item->id, $response->items_bought ?? []);
                    $actualQty = ($response->item_actual_qty[$item->id] ?? null);
                    $extraQty = ($response->item_extra_qty[$item->id] ?? 0);
                    $shortQty = ($response->item_short_qty[$item->id] ?? 0);
                @endphp
                <div class="flex items-center justify-between p-3 rounded-lg {{ $bought ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' }}">
                    <div class="flex items-center">
                        @if($bought)
                        <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        @else
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        @endif
                        <span class="{{ $bought ? 'text-green-800' : 'text-gray-600' }}">{{ $item->name }}</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        @if($item->quantity)
                        <span class="text-sm text-gray-500">Required: {{ $item->quantity }}</span>
                        @endif
                        @if($actualQty !== null)
                        <span class="text-sm text-blue-600 font-medium">Brought: {{ $actualQty }}</span>
                        @endif
                        @if($shortQty > 0)
                        <span class="px-2 py-1 text-xs font-medium bg-red-200 text-red-800 rounded-full">-{{ $shortQty }} short</span>
                        @endif
                        @if($extraQty > 0)
                        <span class="px-2 py-1 text-xs font-medium bg-green-200 text-green-800 rounded-full">+{{ $extraQty }} extra</span>
                        @endif
                        <span class="px-2 py-1 text-xs font-medium rounded-full {{ $bought ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $bought ? 'Bought' : 'Not Bought' }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Extra Items Section -->
            <div class="mt-6">
                <h4 class="text-md font-semibold text-green-800 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Extra Items Brought (Beyond Required List)
                </h4>
                @if($response->extra_items && count($response->extra_items) > 0)
                <div class="space-y-2">
                    @foreach($response->extra_items as $extraItem)
                    <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span class="text-green-800 font-medium">{{ $extraItem['name'] ?? 'Unknown Item' }}</span>
                        </div>
                        @if(!empty($extraItem['quantity']))
                        <span class="text-sm text-green-600">Qty: {{ $extraItem['quantity'] }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg text-center">
                    <p class="text-gray-500 text-sm">No extra items recorded</p>
                </div>
                @endif
            </div>

            <!-- Summary -->
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex justify-between items-center">
                    <span class="text-blue-800 font-medium">Items Bought:</span>
                    <span class="text-blue-600 font-bold">{{ count($response->items_bought ?? []) }} / {{ $response->groceryList->items->count() }}</span>
                </div>
                @if($response->extra_items && count($response->extra_items) > 0)
                <div class="flex justify-between items-center mt-2 pt-2 border-t border-blue-200">
                    <span class="text-green-800 font-medium">Extra Items:</span>
                    <span class="text-green-600 font-bold">{{ count($response->extra_items) }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
