@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Assign Subject to Teacher</h1>
                <p class="mt-2 text-sm text-gray-600">Link subjects to teachers for class assignments</p>
            </div>
            <a href="{{ route('admin.subjects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Subjects
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-green-700">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Assignment Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-green-500 to-emerald-600">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Assignment
                </h3>
            </div>
            
            <form action="{{ route('admin.subjects.assign.store') }}" method="POST" class="p-6 space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Teacher <span class="text-red-500">*</span></label>
                    <select name="teacher_id" required class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('teacher_id') border-red-500 @enderror">
                        <option value="">-- Choose a Teacher --</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Subjects <span class="text-red-500">*</span></label>
                    <div class="border border-gray-300 rounded-lg max-h-48 overflow-y-auto p-2 @error('subject_ids') border-red-500 @enderror">
                        @forelse ($subjects as $subject)
                            <label class="flex items-center p-2 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                <span class="ml-3 text-sm text-gray-700">{{ $subject->name }} <span class="text-gray-400">({{ $subject->subject_code }})</span></span>
                            </label>
                        @empty
                            <p class="p-2 text-sm text-gray-500 text-center">No unassigned subjects available</p>
                        @endforelse
                    </div>
                    @error('subject_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if($subjects->isEmpty())
                        <p class="mt-2 text-sm text-amber-600">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            All subjects are already assigned. Create new subjects or unassign existing ones.
                        </p>
                    @endif
                    <p class="mt-1 text-xs text-gray-500">Select one or more subjects to assign</p>
                </div>

                <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all flex items-center justify-center" {{ $subjects->isEmpty() ? 'disabled' : '' }}>
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    Assign Subject
                </button>
            </form>
        </div>

        <!-- Current Assignments -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-500 to-indigo-600">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Current Assignments ({{ $assignedSubjects->count() }})
                </h3>
            </div>
            
            <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                @forelse ($assignedSubjects as $subject)
                    <div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-sm font-bold">
                                {{ strtoupper(substr($subject->teacher->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $subject->name }}</p>
                                <p class="text-xs text-gray-500">{{ $subject->teacher->user->name }} • Code: {{ $subject->subject_code }}</p>
                            </div>
                        </div>
                        <form action="{{ route('admin.subjects.unassign', $subject->id) }}" method="POST" onsubmit="return confirm('Remove this assignment?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Remove Assignment">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-sm">No subjects assigned yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
