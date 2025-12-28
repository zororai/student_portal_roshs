@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Cash Book Entry</h1>
        <a href="{{ route('admin.finance.cashbook.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form action="{{ route('admin.finance.cashbook.update', $entry->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Entry Date *</label><input type="date" name="entry_date" value="{{ $entry->entry_date->format('Y-m-d') }}" class="w-full border rounded-lg px-3 py-2" required></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Category *</label><input type="text" name="category" value="{{ $entry->category }}" class="w-full border rounded-lg px-3 py-2" required></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Description *</label><input type="text" name="description" value="{{ $entry->description }}" class="w-full border rounded-lg px-3 py-2" required></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label><input type="number" step="0.01" name="amount" value="{{ $entry->amount }}" class="w-full border rounded-lg px-3 py-2" required></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label><select name="payment_method" class="w-full border rounded-lg px-3 py-2"><option value="cash" {{ $entry->payment_method == 'cash' ? 'selected' : '' }}>Cash</option><option value="bank" {{ $entry->payment_method == 'bank' ? 'selected' : '' }}>Bank</option><option value="mobile_money" {{ $entry->payment_method == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Payer/Payee</label><input type="text" name="payer_payee" value="{{ $entry->payer_payee }}" class="w-full border rounded-lg px-3 py-2"></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Notes</label><textarea name="notes" rows="3" class="w-full border rounded-lg px-3 py-2">{{ $entry->notes }}</textarea></div>
            </div>
            <div class="mt-6 flex justify-end"><button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Update</button></div>
        </form>
    </div>
</div>
@endsection
