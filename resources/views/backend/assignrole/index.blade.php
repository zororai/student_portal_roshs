@extends('layouts.app')

@section('content')
<div class="container-fluid py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">User Role Management</h1>
                    <p class="text-gray-600">Assign and manage user roles across the system</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('assignrole.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Create User
                    </a>
                </div>
            </div>
        </div>
        <!-- Users Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <!-- Table Header -->
            <div class="bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-12 gap-4 px-6 py-4">
                    <div class="col-span-12 sm:col-span-3 text-sm font-semibold text-gray-700">User Details</div>
                    <div class="col-span-12 sm:col-span-4 text-sm font-semibold text-gray-700">Email Address</div>
                    <div class="col-span-12 sm:col-span-3 text-sm font-semibold text-gray-700">Assigned Roles</div>
                    <div class="col-span-12 sm:col-span-2 text-sm font-semibold text-gray-700 text-right">Actions</div>
                </div>
            </div>
            
            <!-- Table Body -->
            <div class="divide-y divide-gray-200">
                @forelse ($users as $user)
                <div class="grid grid-cols-12 gap-4 px-6 py-4 hover:bg-gray-50 transition-colors">
                    <!-- User Details -->
                    <div class="col-span-12 sm:col-span-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-sm">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">ID: #{{ $user->id }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="col-span-12 sm:col-span-4">
                        <p class="text-sm text-gray-900">{{ $user->email }}</p>
                        <p class="text-xs text-gray-500">{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    
                    <!-- Roles -->
                    <div class="col-span-12 sm:col-span-3">
                        <div class="flex flex-wrap gap-1">
                            @forelse ($user->roles as $role)
                                @php
                                    $roleColors = [
                                        'Admin' => 'bg-red-100 text-red-800 border-red-200',
                                        'Teacher' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'Parent' => 'bg-green-100 text-green-800 border-green-200',
                                        'Student' => 'bg-yellow-100 text-yellow-800 border-yellow-200'
                                    ];
                                    $colorClass = $roleColors[$role->name] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $colorClass }}">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                    No Role
                                </span>
                            @endforelse
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="col-span-12 sm:col-span-2">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('assignrole.edit', $user->id) }}" class="inline-flex items-center px-3 py-2 text-sm text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition-colors" title="Edit User Roles">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new user.</p>
                    <div class="mt-6">
                        <a href="{{ route('assignrole.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create User
                        </a>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="mt-6">
            <div class="bg-white rounded-lg border border-gray-200 px-4 py-3">
                {{ $users->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

<style>
.container-fluid {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    min-height: 100vh;
}