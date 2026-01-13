@extends('layouts.app')

@section('content')
<div x-data="schemeEditForm()" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Scheme of Work</h1>
        <p class="mt-2 text-sm text-gray-600">{{ $scheme->subject->name }} - {{ $scheme->class->class_name }}</p>
    </div>

    <form action="{{ route('teacher.schemes.update', $scheme->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                            <input type="text" name="title" value="{{ old('title', $scheme->title) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $scheme->description) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                            <input type="date" name="start_date" value="{{ old('start_date', $scheme->start_date ? $scheme->start_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                            <input type="date" name="end_date" value="{{ old('end_date', $scheme->end_date ? $scheme->end_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expected Performance (%)</label>
                            <input type="number" name="expected_performance" value="{{ old('expected_performance', $scheme->expected_performance) }}" min="0" max="100" step="0.1" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="draft" {{ $scheme->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ $scheme->status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="completed" {{ $scheme->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="archived" {{ $scheme->status == 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Scheme Topics</h2>
                    <div class="space-y-4">
                        @foreach($scheme->schemeTopics as $index => $topic)
                            <div class="border rounded-lg p-4 {{ $topic->remedial_required ? 'border-red-300 bg-red-50' : 'border-gray-200' }}">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-medium text-gray-900">{{ $topic->syllabusTopic->name ?? 'Unknown Topic' }}</h4>
                                    @if($topic->actual_performance !== null)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $topic->actual_performance >= 75 ? 'bg-green-100 text-green-800' : ($topic->actual_performance >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                            {{ number_format($topic->actual_performance, 1) }}% Actual
                                        </span>
                                    @endif
                                </div>
                                <input type="hidden" name="topics[{{ $index }}][id]" value="{{ $topic->id }}">
                                <input type="hidden" name="topics[{{ $index }}][syllabus_topic_id]" value="{{ $topic->syllabus_topic_id }}">
                                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Week</label>
                                        <input type="number" name="topics[{{ $index }}][week_number]" value="{{ $topic->week_number }}" min="1" class="w-full text-sm rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Planned</label>
                                        <input type="number" name="topics[{{ $index }}][planned_periods]" value="{{ $topic->planned_periods }}" min="1" required class="w-full text-sm rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Actual</label>
                                        <input type="number" name="topics[{{ $index }}][actual_periods]" value="{{ $topic->actual_periods }}" min="0" class="w-full text-sm rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Expected %</label>
                                        <input type="number" name="topics[{{ $index }}][expected_performance]" value="{{ $topic->expected_performance }}" min="0" max="100" step="0.1" class="w-full text-sm rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                                        <select name="topics[{{ $index }}][status]" class="w-full text-sm rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="pending" {{ $topic->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="in_progress" {{ $topic->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                            <option value="completed" {{ $topic->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="needs_remedial" {{ $topic->status == 'needs_remedial' ? 'selected' : '' }}>Needs Remedial</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Remarks</label>
                                    <textarea name="topics[{{ $index }}][remarks]" rows="1" class="w-full text-sm rounded border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $topic->remarks }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Scheme Summary</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Total Topics</span>
                            <span class="font-semibold">{{ $scheme->schemeTopics->count() }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Planned Periods</span>
                            <span class="font-semibold">{{ $scheme->total_planned_periods }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-gray-100">
                            <span class="text-gray-600">Actual Periods</span>
                            <span class="font-semibold">{{ $scheme->total_actual_periods }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-gray-600">Performance</span>
                            <span class="font-semibold {{ ($scheme->actual_performance ?? 0) >= 50 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $scheme->actual_performance ? number_format($scheme->actual_performance, 1) . '%' : '--' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col space-y-3">
                    <button type="submit" class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg">
                        Save Changes
                    </button>
                    <a href="{{ route('teacher.schemes.show', $scheme->id) }}" class="w-full px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg text-center">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
