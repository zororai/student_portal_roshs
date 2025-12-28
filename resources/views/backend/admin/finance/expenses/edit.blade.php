@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Expense</h1>
        <a href="{{ route('admin.finance.expenses.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6 max-w-2xl">
        <form action="{{ route('admin.finance.expenses.update', $expense->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Date *</label><input type="date" name="expense_date" value="{{ $expense->expense_date->format('Y-m-d') }}" class="w-full border rounded-lg px-3 py-2" required></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                        <select name="category_id" class="w-full border rounded-lg px-3 py-2" required>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $expense->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Vendor Name</label><input type="text" name="vendor_name" value="{{ $expense->vendor_name }}" class="w-full border rounded-lg px-3 py-2"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Description *</label><textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2" required>{{ $expense->description }}</textarea></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label><input type="number" name="amount" step="0.01" value="{{ $expense->amount }}" class="w-full border rounded-lg px-3 py-2" required></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Payment Status *</label>
                        <select name="payment_status" class="w-full border rounded-lg px-3 py-2" required>
                            <option value="pending" {{ $expense->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ $expense->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="partial" {{ $expense->payment_status == 'partial' ? 'selected' : '' }}>Partial</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Update Expense</button>
            </div>
        </form>
    </div>
</div>
@endsection
