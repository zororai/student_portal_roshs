@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">General Journal</h1>
        <p class="text-gray-500 text-sm">Manage journal entries and post to ledger</p>
    </div>
    <a href="{{ route('finance.journals.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
        + Create Journal Entry
    </a>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">Draft</p>
        <h2 class="text-2xl font-bold text-yellow-600">{{ $batches->where('status', 'draft')->count() }}</h2>
    </div>

    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">Approved</p>
        <h2 class="text-2xl font-bold text-blue-600">{{ $batches->where('status', 'approved')->count() }}</h2>
    </div>

    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">Posted</p>
        <h2 class="text-2xl font-bold text-green-600">{{ $batches->where('status', 'posted')->count() }}</h2>
    </div>

    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">Total Entries</p>
        <h2 class="text-2xl font-bold text-indigo-600">{{ $batches->total() }}</h2>
    </div>
</div>

@if(session('success'))
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm text-green-700">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <p class="text-sm text-red-700">{{ session('error') }}</p>
        </div>
    </div>
@endif

<!-- Filters Card -->
<div class="bg-white shadow-md rounded-xl p-6 mb-6">
    <h3 class="font-semibold text-gray-800 mb-4">Filter Journal Entries</h3>
    <form method="GET" action="{{ route('finance.journals.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-3">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
            </select>
        </div>
        <div class="md:col-span-6">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                   placeholder="Search by reference or description...">
        </div>
        <div class="md:col-span-3 flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md transition duration-200">
                Search
            </button>
        </div>
    </form>
</div>

<!-- Journal Entries Table -->
<div class="bg-white shadow-md rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="font-semibold text-gray-800">Journal Entries</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($batches as $batch)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('finance.journals.show', $batch->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                {{ $batch->reference }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-900">{{ Str::limit($batch->description, 60) }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $batch->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-green-600">
                            ${{ number_format($batch->total_debit, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-red-600">
                            ${{ number_format($batch->total_credit, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($batch->status == 'draft')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                            @elseif($batch->status == 'approved')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Approved</span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Posted</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $batch->creator->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('finance.journals.show', $batch->id) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition duration-150">
                                    View
                                </a>
                                @if($batch->status == 'draft')
                                    <a href="{{ route('finance.journals.edit', $batch->id) }}" 
                                       class="inline-flex items-center px-3 py-1 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition duration-150">
                                        Edit
                                    </a>
                                    <form action="{{ route('finance.journals.approve', $batch->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition duration-150"
                                                onclick="return confirm('Approve this journal batch?')">
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('finance.journals.destroy', $batch->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition duration-150"
                                                onclick="return confirm('Delete this journal batch?')">
                                            Delete
                                        </button>
                                    </form>
                                @elseif($batch->status == 'approved')
                                    <form action="{{ route('finance.journals.post', $batch->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition duration-150"
                                                onclick="return confirm('Post this journal to the ledger? This action cannot be undone.')">
                                            Post
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-medium mb-2">No journal entries found</p>
                                <a href="{{ route('finance.journals.create') }}" class="mt-3 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200">
                                    Create Your First Entry
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($batches->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ $batches->firstItem() }}</span> to <span class="font-medium">{{ $batches->lastItem() }}</span> of <span class="font-medium">{{ $batches->total() }}</span> entries
                </div>
                <div>
                    {{ $batches->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

