@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Budget Management</h1>
        <a href="{{ route('admin.finance.budgets.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ New Budget</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($budgets as $budget)
                <tr>
                    <td class="px-6 py-4 text-sm font-medium">{{ $budget->name }}</td>
                    <td class="px-6 py-4 text-sm">{{ ucfirst($budget->period_type) }}</td>
                    <td class="px-6 py-4 text-sm">{{ $budget->start_date->format('d M Y') }} - {{ $budget->end_date->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($budget->status == 'active')<span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Active</span>
                        @elseif($budget->status == 'closed')<span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Closed</span>
                        @else<span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Draft</span>@endif
                    </td>
                    <td class="px-6 py-4 text-right text-sm">
                        <a href="{{ route('admin.finance.budgets.show', $budget->id) }}" class="text-blue-600 hover:underline">View</a>
                        <a href="{{ route('admin.finance.budgets.comparison', $budget->id) }}" class="text-green-600 hover:underline ml-2">Compare</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No budgets found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $budgets->links() }}</div>
</div>
@endsection
