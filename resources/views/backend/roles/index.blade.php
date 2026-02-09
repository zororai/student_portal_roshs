@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Roles & Permissions</h1>
                <p class="mt-2 text-sm text-gray-600">Manage user roles and their associated permissions</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('role.create') }}" class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Role
                </a>
            </div>
        </div>
    </div>

    <!-- Roles List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Table Header -->
        <div class="hidden md:flex items-center bg-gray-50 border-b border-gray-200 px-6 py-4">
            <div class="w-3/12 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role Name</div>
            <div class="w-7/12 text-xs font-semibold text-gray-500 uppercase tracking-wider">Permissions</div>
            <div class="w-2/12 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Actions</div>
        </div>
        
        <!-- Role Rows -->
        @forelse ($roles as $role)
            <div class="flex flex-col md:flex-row md:items-center px-6 py-5 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <div class="w-full md:w-3/12 mb-3 md:mb-0">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $role->name }}</p>
                            <p class="text-xs text-gray-500">{{ $role->permissions->count() }} permissions</p>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-7/12 mb-3 md:mb-0">
                    <div class="flex flex-wrap gap-1.5">
                        @forelse ($role->permissions as $permission)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $permission->name }}
                            </span>
                        @empty
                            <span class="text-sm text-gray-400 italic">No permissions assigned</span>
                        @endforelse
                    </div>
                </div>
                <div class="w-full md:w-2/12 flex justify-start md:justify-end">
                    <a href="{{ route('role.edit', $role->id) }}" class="inline-flex items-center px-3 py-2 bg-green-100 hover:bg-green-200 text-green-700 text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                </div>
            </div>
        @empty
            <div class="px-6 py-12 text-center">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <p class="text-gray-500 text-lg font-medium">No roles found</p>
                <p class="text-gray-400 text-sm mt-1">Get started by creating your first role</p>
                <a href="{{ route('role.create') }}" class="inline-flex items-center mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Role
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection