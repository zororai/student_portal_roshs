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
                <div class="flex items-center space-x-3">
                    <a href="{{ route('subject.create') }}" class="inline-flex items-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 448 512">
                            <path d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/>
                        </svg>
                        Add New Subject
                    </a>
                </div>
            </div>
        </div>

        <!-- Subject List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <h3 class="text-gray-700 uppercase font-bold mb-2 px-6 pt-6">Subject List</h3>
            
            <!-- Header Row -->
            <div class="flex items-center bg-gray-600 mx-6 rounded-tl rounded-tr">
                <div class="w-1/3 text-left text-white py-2 px-4 font-semibold">Code</div>
                <div class="w-1/3 text-left text-white py-2 px-4 font-semibold">Subject</div>
                <div class="w-1/3 text-right text-white py-2 px-4 font-semibold">Teacher</div>
            </div>
            
            <!-- Subject Rows -->
            <div class="px-6 pb-6">
                @forelse ($subjects as $subject)
                    <div class="flex items-center justify-between border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors" onclick="window.location='{{ route('subject.Reading', $subject->id) }}'">
                        <div class="w-1/3 text-left text-gray-600 py-2 px-4 font-medium">{{ $subject->subject_code }}</div>
                        <div class="w-1/3 text-left text-gray-600 py-2 px-4 font-medium">{{ $subject->name }}</div>
                        <div class="w-1/3 text-right text-gray-600 py-2 px-4 font-medium">{{ $subject->teacher->user->name ?? 'Not Assigned' }}</div>
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
