@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Expense Categories</h1>
        <a href="{{ route('admin.finance.expenses.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50"><h3 class="font-semibold">Categories</h3></div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Expenses</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($categories as $cat)
                    <tr>
                        <td class="px-6 py-4 text-sm font-mono">{{ $cat->code }}</td>
                        <td class="px-6 py-4 text-sm">{{ $cat->name }}</td>
                        <td class="px-6 py-4 text-sm text-right">{{ $cat->expenses_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="font-semibold mb-4">Add Category</h3>
            <form action="{{ route('admin.finance.expenses.store-category') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Code *</label><input type="text" name="code" class="w-full border rounded-lg px-3 py-2" required></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input type="text" name="name" class="w-full border rounded-lg px-3 py-2" required></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea name="description" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea></div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
