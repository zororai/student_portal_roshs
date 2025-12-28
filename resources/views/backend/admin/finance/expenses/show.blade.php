@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Expense Details</h1>
        <a href="{{ route('admin.finance.expenses.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6 max-w-2xl">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div><span class="text-gray-500">Expense #:</span> <strong>{{ $expense->expense_number }}</strong></div>
            <div><span class="text-gray-500">Date:</span> <strong>{{ $expense->expense_date->format('d M Y') }}</strong></div>
            <div><span class="text-gray-500">Category:</span> <strong>{{ $expense->category->name ?? 'N/A' }}</strong></div>
            <div><span class="text-gray-500">Vendor:</span> <strong>{{ $expense->vendor_name ?? 'N/A' }}</strong></div>
            <div><span class="text-gray-500">Amount:</span> <strong class="text-xl">${{ number_format($expense->amount, 2) }}</strong></div>
            <div><span class="text-gray-500">Status:</span> 
                @if($expense->payment_status == 'paid')<span class="px-2 py-1 bg-green-100 text-green-800 rounded">Paid</span>
                @elseif($expense->payment_status == 'partial')<span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Partial</span>
                @else<span class="px-2 py-1 bg-red-100 text-red-800 rounded">Pending</span>@endif
            </div>
        </div>
        <div class="border-t pt-4 mb-4">
            <div class="text-gray-500 text-sm">Description</div>
            <p>{{ $expense->description }}</p>
        </div>
        @if($expense->notes)
        <div class="border-t pt-4 mb-4">
            <div class="text-gray-500 text-sm">Notes</div>
            <p>{{ $expense->notes }}</p>
        </div>
        @endif
        <div class="border-t pt-4 flex gap-2">
            <a href="{{ route('admin.finance.expenses.edit', $expense->id) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Edit</a>
            @if(!$expense->approved_by)
            <form action="{{ route('admin.finance.expenses.approve', $expense->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Approve</button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
