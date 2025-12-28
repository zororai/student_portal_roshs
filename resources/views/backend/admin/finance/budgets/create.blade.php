@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Budget Period</h1>
        <a href="{{ route('admin.finance.budgets.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6 max-w-xl">
        <form action="{{ route('admin.finance.budgets.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Budget Name *</label><input type="text" name="name" class="w-full border rounded-lg px-3 py-2" required placeholder="e.g. 2025 Annual Budget"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Period Type *</label>
                    <select name="period_type" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="annual">Annual</option>
                        <option value="term">Term</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Start Date *</label><input type="date" name="start_date" class="w-full border rounded-lg px-3 py-2" required></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">End Date *</label><input type="date" name="end_date" class="w-full border rounded-lg px-3 py-2" required></div>
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Create Budget</button>
            </div>
        </form>
    </div>
</div>
@endsection
