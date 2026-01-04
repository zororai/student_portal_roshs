@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Manage Sidebar Permissions</h2>
                        <p class="text-gray-600">Control which sidebar items are visible for each admin user. Check the items you want each user to see in their sidebar.</p>
                    </div>
                    <a href="{{ route('roles-permissions') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Roles & Permissions
                    </a>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="space-y-8">
                    @foreach($users->where('roles.*.name', 'Admin') as $user)
                    <div class="border border-gray-200 rounded-lg p-6 bg-gray-50">
                        <div class="flex items-center mb-4">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">{{ $user->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($user->roles as $role)
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">{{ $role->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('sidebar.permissions.update') }}" method="POST" class="mt-4">
                            @csrf
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @php
                                    $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
                                @endphp

                                @foreach($sidebarItems as $sectionName => $sectionItems)
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 pb-2 border-b border-gray-100">{{ $sectionName }}</h4>
                                    <div class="space-y-2">
                                        @foreach($sectionItems as $permissionKey => $permissionLabel)
                                        <label class="flex items-center space-x-3 cursor-pointer hover:bg-gray-50 p-2 rounded transition-colors">
                                            <input 
                                                type="checkbox" 
                                                name="permissions[]" 
                                                value="{{ $permissionKey }}"
                                                {{ in_array($permissionKey, $userPermissions) ? 'checked' : '' }}
                                                class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                            >
                                            <span class="text-sm text-gray-700 flex-1">{{ $permissionLabel }}</span>
                                            @if(in_array($permissionKey, $userPermissions))
                                                <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
                                <div class="flex space-x-2">
                                    <button type="button" onclick="selectAllPermissions(this)" class="px-3 py-2 text-sm bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                        Select All
                                    </button>
                                    <button type="button" onclick="deselectAllPermissions(this)" class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                        Deselect All
                                    </button>
                                </div>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Update Permissions for {{ $user->name }}
                                </button>
                            </div>
                        </form>
                    </div>
                    @endforeach
                </div>

                @if($users->where('roles.*.name', 'Admin')->count() == 0)
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Admin Users Found</h3>
                    <p class="text-gray-500 mb-4">There are currently no users with Admin roles to manage sidebar permissions for.</p>
                    <a href="{{ route('assignrole.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Manage User Roles
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function selectAllPermissions(button) {
    const form = button.closest('form');
    const checkboxes = form.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllPermissions(button) {
    const form = button.closest('form');
    const checkboxes = form.querySelectorAll('input[type="checkbox"][name="permissions[]"]');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Add loading state to forms
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Updating...
                `;
            }
        });
    });
});
</script>

<style>
.container-fluid {
    max-width: 100%;
    padding: 0 1rem;
}

@media (min-width: 1024px) {
    .container-fluid {
        padding: 0 2rem;
    }
}
</style>
@endsection
