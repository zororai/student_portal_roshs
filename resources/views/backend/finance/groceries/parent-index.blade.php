@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Groceries List</h1>
        <p class="text-gray-500 mt-1">View and submit grocery lists for your children</p>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    @if(count($groceryData) > 0)
    <div class="space-y-6">
        @foreach($groceryData as $data)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-orange-500 to-amber-500 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $data['child']->user->name ?? 'Child' }}</h3>
                        <p class="text-orange-100 text-sm">{{ $data['child']->class->class_name ?? 'Class' }} - {{ ucfirst($data['list']->term) }} {{ $data['list']->year }}</p>
                    </div>
                    @if($data['response'] && $data['response']->acknowledged)
                    <span class="px-3 py-1 bg-white/20 text-white rounded-full text-sm">Acknowledged</span>
                    @elseif($data['response'] && $data['response']->submitted)
                    <span class="px-3 py-1 bg-white/20 text-white rounded-full text-sm">Submitted</span>
                    @else
                    <span class="px-3 py-1 bg-white/20 text-white rounded-full text-sm">Pending</span>
                    @endif
                </div>
            </div>

            <form action="{{ route('parent.groceries.submit') }}" method="POST" class="p-6">
                @csrf
                <input type="hidden" name="grocery_list_id" value="{{ $data['list']->id }}">
                <input type="hidden" name="student_id" value="{{ $data['child']->id }}">

                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-3">Please tick the items you have purchased for your child:</p>
                </div>

                <div class="space-y-2 mb-6">
                    @foreach($data['list']->items as $item)
                    @php 
                        $isChecked = $data['response'] && in_array($item->id, $data['response']->items_bought ?? []); 
                        $isDisabled = $data['response'] && $data['response']->acknowledged;
                    @endphp
                    <label class="flex items-center p-3 rounded-lg border {{ $isChecked ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }} {{ $isDisabled ? 'cursor-not-allowed opacity-75' : 'cursor-pointer hover:bg-gray-100' }}">
                        <input type="checkbox" name="items_bought[]" value="{{ $item->id }}" 
                            class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 mr-3"
                            {{ $isChecked ? 'checked' : '' }}
                            {{ $isDisabled ? 'disabled' : '' }}>
                        <div class="flex-1">
                            <span class="text-gray-800">{{ $item->name }}</span>
                        </div>
                        @if($item->quantity)
                        <span class="text-sm text-gray-500">Qty: {{ $item->quantity }}</span>
                        @endif
                    </label>
                    @endforeach
                </div>

                @if(!($data['response'] && $data['response']->acknowledged))
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Any additional notes...">{{ $data['response']->notes ?? '' }}</textarea>
                </div>

                <button type="submit" class="w-full px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 font-medium">
                    {{ $data['response'] && $data['response']->submitted ? 'Update Response' : 'Submit Response' }}
                </button>
                @else
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg text-center">
                    <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-green-800 font-medium">Goods Received</p>
                    <p class="text-green-600 text-sm">The school has acknowledged receipt of the goods.</p>
                </div>
                @endif
            </form>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-gray-50 border border-gray-200 rounded-xl p-8 text-center">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
        </svg>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Grocery Lists</h3>
        <p class="text-gray-600">There are no active grocery lists for your children at this time.</p>
    </div>
    @endif
</div>
@endsection
