@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Events</h1>
                <p class="mt-2 text-sm text-gray-600">Manage and publish school events</p>
            </div>
            <a href="{{ route('events.create') }}" class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Event
            </a>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Event List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Table Header -->
        <div class="hidden md:flex items-center bg-gray-50 border-b border-gray-200 px-6 py-4">
            <div class="w-3/12 text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</div>
            <div class="w-2/12 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</div>
            <div class="w-2/12 text-xs font-semibold text-gray-500 uppercase tracking-wider">Location</div>
            <div class="w-2/12 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</div>
            <div class="w-3/12 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</div>
        </div>
        
        <!-- Event Rows -->
        @forelse($events as $event)
            <div class="flex flex-col md:flex-row md:items-center px-6 py-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <div class="w-full md:w-3/12 mb-2 md:mb-0">
                    <p class="text-sm font-semibold text-gray-900">{{ $event->title }}</p>
                    <p class="text-xs text-gray-500 md:hidden mt-1">
                        {{ $event->event_date->format('M d, Y') }}
                        @if($event->event_time)
                            at {{ \Carbon\Carbon::parse($event->event_time)->format('g:i A') }}
                        @endif
                    </p>
                </div>
                <div class="hidden md:block w-2/12">
                    <p class="text-sm text-gray-600">{{ $event->event_date->format('M d, Y') }}</p>
                    @if($event->event_time)
                        <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($event->event_time)->format('g:i A') }}</p>
                    @endif
                </div>
                <div class="hidden md:block w-2/12">
                    <p class="text-sm text-gray-600">{{ $event->location ?? 'TBD' }}</p>
                </div>
                <div class="hidden md:block w-2/12">
                    @if($event->is_published)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Published
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                            Draft
                        </span>
                    @endif
                </div>
                <div class="w-full md:w-3/12 flex items-center justify-start md:justify-end space-x-2 mt-3 md:mt-0">
                    <a href="{{ route('events.edit', $event->id) }}" class="inline-flex items-center px-3 py-2 bg-amber-100 hover:bg-amber-200 text-amber-700 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('events.destroy', $event->id) }}" method="POST" class="inline-flex" onsubmit="return confirm('Are you sure you want to delete this event?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <p class="text-gray-500 text-lg font-medium">No events found</p>
                <p class="text-gray-400 text-sm mt-1">Get started by creating your first event</p>
                <a href="{{ route('events.create') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Event
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $events->links() }}
    </div>
</div>
@endsection
