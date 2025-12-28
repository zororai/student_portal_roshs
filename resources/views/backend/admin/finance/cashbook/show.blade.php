@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Cash Book Entry Details</h1>
        <a href="{{ route('admin.finance.cashbook.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6 max-w-2xl">
        <div class="grid grid-cols-2 gap-4">
            <div><span class="text-gray-500">Reference:</span> <strong>{{ $entry->reference_number }}</strong></div>
            <div><span class="text-gray-500">Date:</span> <strong>{{ $entry->entry_date->format('d M Y') }}</strong></div>
            <div><span class="text-gray-500">Type:</span> 
                <span class="px-2 py-1 text-xs rounded {{ $entry->isReceipt() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($entry->transaction_type) }}
                </span>
            </div>
            <div><span class="text-gray-500">Category:</span> <strong>{{ ucfirst(str_replace('_', ' ', $entry->category)) }}</strong></div>
            <div class="col-span-2"><span class="text-gray-500">Description:</span> <strong>{{ $entry->description }}</strong></div>
            <div><span class="text-gray-500">Amount:</span> <strong class="{{ $entry->isReceipt() ? 'text-green-600' : 'text-red-600' }}">${{ number_format($entry->amount, 2) }}</strong></div>
            <div><span class="text-gray-500">Balance:</span> <strong>${{ number_format($entry->balance, 2) }}</strong></div>
            <div><span class="text-gray-500">Payment Method:</span> <strong>{{ ucfirst(str_replace('_', ' ', $entry->payment_method ?? 'N/A')) }}</strong></div>
            <div><span class="text-gray-500">Payer/Payee:</span> <strong>{{ $entry->payer_payee ?? 'N/A' }}</strong></div>
            <div><span class="text-gray-500">Created By:</span> <strong>{{ $entry->creator->name }}</strong></div>
            <div><span class="text-gray-500">Created At:</span> <strong>{{ $entry->created_at->format('d M Y H:i') }}</strong></div>
            @if($entry->notes)
            <div class="col-span-2"><span class="text-gray-500">Notes:</span><br>{{ $entry->notes }}</div>
            @endif
        </div>
    </div>
</div>
@endsection
