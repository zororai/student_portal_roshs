@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Subjects</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage academic subjects and teacher assignments</p>
                </div>
            </div>
        </div>

        <!-- Subject List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <h3 class="text-gray-700 uppercase font-bold mb-2 px-6 pt-6">Subject List</h3>
            
            <!-- Header Row -->
            <div class="flex items-center bg-gray-600 mx-6 rounded-tl rounded-tr">
                <div class="w-1/4 text-left text-white py-2 px-4 font-semibold">Code</div>
                <div class="w-1/4 text-left text-white py-2 px-4 font-semibold">Subject</div>
                <div class="w-1/4 text-left text-white py-2 px-4 font-semibold">Teacher</div>
                <div class="w-1/4 text-center text-white py-2 px-4 font-semibold">Materials</div>
            </div>
            
            <!-- Subject Rows -->
            <div class="px-6 pb-6">
                @forelse ($subjects as $subject)
                    <div class="flex items-center justify-between border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors" onclick="window.location.href='{{ route('subject.Reading', $subject->id) }}'">
                        <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium">{{ $subject->subject_code }}</div>
                        <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium">{{ $subject->name }}</div>
                        <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium">{{ $subject->teacher->user->name ?? 'Not Assigned' }}</div>
                        <div class="w-1/4 text-center text-gray-600 py-2 px-4 font-medium">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                {{ $subject->readings_count ?? 0 }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="border border-gray-200 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <p class="text-gray-500 text-lg font-medium">No subjects found</p>
                            <p class="text-gray-400 text-sm mt-1">Get started by adding your first subject</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $subjects->links() }}
        </div>
    </div>
@endsection
