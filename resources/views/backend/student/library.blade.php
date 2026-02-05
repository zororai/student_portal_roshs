@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Library</h1>
        <p class="mt-2 text-gray-600">View your borrowed books and borrowing history</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Borrowed</p>
                    <p class="text-3xl font-bold mt-1">{{ $totalBorrowed }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
            <p class="text-blue-100 text-sm mt-2">All time</p>
        </div>

        <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm font-medium">Currently Borrowed</p>
                    <p class="text-3xl font-bold mt-1">{{ $currentlyBorrowed }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                </div>
            </div>
            <p class="text-amber-100 text-sm mt-2">Books with you</p>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Returned</p>
                    <p class="text-3xl font-bold mt-1">{{ $returned }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-green-100 text-sm mt-2">Successfully returned</p>
        </div>

        <div class="bg-gradient-to-br {{ $overdue > 0 ? 'from-red-500 to-red-600' : 'from-gray-400 to-gray-500' }} rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="{{ $overdue > 0 ? 'text-red-100' : 'text-gray-100' }} text-sm font-medium">Overdue</p>
                    <p class="text-3xl font-bold mt-1">{{ $overdue }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="{{ $overdue > 0 ? 'text-red-100' : 'text-gray-100' }} text-sm mt-2">{{ $overdue > 0 ? 'Please return soon!' : 'No overdue books' }}</p>
        </div>
    </div>

    <!-- Currently Borrowed Books -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-xl">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                Currently Borrowed Books
            </h2>
            <p class="text-blue-100 text-sm mt-1">Books you currently have checked out</p>
        </div>
        <div class="p-6">
            @if($borrowedBooks->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($borrowedBooks as $record)
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                @if($record->book && $record->book->image)
                                    <img src="{{ asset($record->book->image) }}" alt="{{ $record->book_title }}" class="w-16 h-20 object-cover rounded-lg shadow">
                                @else
                                    <div class="w-16 h-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 truncate">{{ $record->book_title }}</h3>
                                <p class="text-sm text-gray-500">Book #: {{ $record->book_number }}</p>
                                @if($record->book && $record->book->author)
                                    <p class="text-sm text-gray-500">Author: {{ $record->book->author }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-gray-600">Issued: <strong>{{ $record->issue_date ? $record->issue_date->format('M d, Y') : 'N/A' }}</strong></span>
                            </div>
                            @if($record->due_date)
                            <div class="flex items-center text-sm">
                                <svg class="w-4 h-4 {{ $record->due_date < now() ? 'text-red-500' : 'text-gray-400' }} mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="{{ $record->due_date < now() ? 'text-red-600 font-semibold' : 'text-gray-600' }}">
                                    Due: <strong>{{ $record->due_date->format('M d, Y') }}</strong>
                                    @if($record->due_date < now())
                                        <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 rounded-full text-xs">OVERDUE</span>
                                    @endif
                                </span>
                            </div>
                            @endif
                        </div>
                        @if($record->notes)
                        <div class="mt-3 p-2 bg-yellow-50 rounded-lg border border-yellow-100">
                            <p class="text-xs text-yellow-700"><strong>Note:</strong> {{ $record->notes }}</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">No Books Currently Borrowed</h3>
                    <p class="text-gray-500">You don't have any books checked out at the moment.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Borrowing History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-700 to-gray-800 rounded-t-xl">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Borrowing History
            </h2>
            <p class="text-gray-300 text-sm mt-1">Complete history of all books you've borrowed</p>
        </div>
        <div class="p-6">
            @if($borrowingHistory->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Book</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Book #</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Issue Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Due Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Return Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($borrowingHistory as $record)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4">
                                    <div class="flex items-center">
                                        @if($record->book && $record->book->image)
                                            <img src="{{ asset($record->book->image) }}" alt="{{ $record->book_title }}" class="w-10 h-12 object-cover rounded mr-3">
                                        @else
                                            <div class="w-10 h-12 bg-gray-200 rounded mr-3 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $record->book_title }}</p>
                                            @if($record->book && $record->book->author)
                                                <p class="text-xs text-gray-500">{{ $record->book->author }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $record->book_number }}</td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $record->issue_date ? $record->issue_date->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $record->due_date ? $record->due_date->format('M d, Y') : 'N/A' }}</td>
                                <td class="px-4 py-4 text-sm text-gray-600">{{ $record->return_date ? $record->return_date->format('M d, Y') : '-' }}</td>
                                <td class="px-4 py-4">
                                    @if($record->status === 'returned')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Returned</span>
                                    @elseif($record->due_date && $record->due_date < now())
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Overdue</span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Borrowed</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $borrowingHistory->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">No Borrowing History</h3>
                    <p class="text-gray-500">You haven't borrowed any books yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
