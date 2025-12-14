@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <a href="{{ route('admin.audit-trail.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-2">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Audit Trail
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Audit Detail</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Activity Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-500">Action</label>
                        <p class="mt-1">
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $audit->action_color }}">
                                {{ ucfirst($audit->action) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Date & Time</label>
                        <p class="mt-1 text-gray-900">{{ $audit->created_at->format('F d, Y \a\t h:i:s A') }}</p>
                    </div>
                    <div class="col-span-2">
                        <label class="text-sm font-medium text-gray-500">Description</label>
                        <p class="mt-1 text-gray-900">{{ $audit->description }}</p>
                    </div>
                </div>
            </div>

            <!-- Changes -->
            @if($audit->old_values || $audit->new_values)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Changes Made</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($audit->old_values)
                    <div>
                        <h4 class="text-sm font-medium text-red-600 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                            Old Values
                        </h4>
                        <div class="bg-red-50 rounded-lg p-4 border border-red-200">
                            <pre class="text-xs text-red-800 overflow-x-auto whitespace-pre-wrap">{{ json_encode(json_decode($audit->old_values), JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                    @endif
                    @if($audit->new_values)
                    <div>
                        <h4 class="text-sm font-medium text-green-600 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            New Values
                        </h4>
                        <div class="bg-green-50 rounded-lg p-4 border border-green-200">
                            <pre class="text-xs text-green-800 overflow-x-auto whitespace-pre-wrap">{{ json_encode(json_decode($audit->new_values), JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- User Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">User Information</h3>
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-lg font-bold">
                        {{ strtoupper(substr($audit->user_name ?? 'S', 0, 1)) }}
                    </div>
                    <div class="ml-3">
                        <p class="font-medium text-gray-900">{{ $audit->user_name ?? 'System' }}</p>
                        <p class="text-sm text-gray-500">{{ $audit->user_role }}</p>
                    </div>
                </div>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">User ID</span>
                        <span class="text-gray-900">{{ $audit->user_id ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Technical Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Technical Details</h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <span class="text-gray-500 block">IP Address</span>
                        <span class="text-gray-900 font-mono">{{ $audit->ip_address ?? 'N/A' }}</span>
                    </div>
                    @if($audit->model_type)
                    <div>
                        <span class="text-gray-500 block">Model Type</span>
                        <span class="text-gray-900">{{ class_basename($audit->model_type) }}</span>
                    </div>
                    @endif
                    @if($audit->model_id)
                    <div>
                        <span class="text-gray-500 block">Model ID</span>
                        <span class="text-gray-900">#{{ $audit->model_id }}</span>
                    </div>
                    @endif
                    @if($audit->user_agent)
                    <div>
                        <span class="text-gray-500 block">User Agent</span>
                        <span class="text-gray-900 text-xs break-all">{{ $audit->user_agent }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Record Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Record Info</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Audit ID</span>
                        <span class="text-gray-900">#{{ $audit->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="text-gray-900">{{ $audit->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
