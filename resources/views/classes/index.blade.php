@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Manage Class Results</h1>
                    <p class="mt-1 text-sm text-gray-500">View and manage academic results by class</p>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-rose-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-rose-100 text-rose-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Classes</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $classes->total() }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Students</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $classes->sum('students_count') }}</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-emerald-500">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Results Ready</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $classes->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classes Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($classes as $class)
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <div class="bg-gradient-to-r from-rose-500 to-pink-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-xl font-bold text-white">{{ $class->class_name }}</h3>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-sm">
                                    {{ $class->students_count }} Students
                                </span>
                            </div>
                        </div>
                        <div class="px-6 py-5">
                            <!-- Teacher -->
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-400 to-pink-500 flex items-center justify-center text-white font-bold text-sm">
                                    {{ $class->teacher ? strtoupper(substr($class->teacher->user->name, 0, 1)) : '?' }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs text-gray-500 uppercase tracking-wider">Class Teacher</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $class->teacher->user->name ?? 'Not Assigned' }}</p>
                                </div>
                            </div>
                            
                            <!-- Subjects -->
                            <div class="mb-4">
                                <p class="text-xs text-gray-500 uppercase tracking-wider mb-2">Subjects</p>
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($class->subjects as $subject)
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700">
                                            {{ $subject->subject_code }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-400 italic">No subjects assigned</span>
                                    @endforelse
                                </div>
                            </div>
                            
                            <!-- Action -->
                            <a href="{{ route('adminresults.classname', $class->id) }}" class="block w-full text-center px-4 py-3 rounded-xl bg-gradient-to-r from-rose-500 to-pink-600 text-white font-semibold hover:from-rose-600 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                View Results
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full">
                        <div class="bg-white rounded-2xl shadow-lg px-6 py-16 text-center">
                            <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <p class="text-gray-900 text-lg font-semibold">No classes found</p>
                            <p class="text-gray-500 text-sm mt-1">Classes will appear here once created</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $classes->links() }}
            </div>
        </div>
    </div>
@endsection