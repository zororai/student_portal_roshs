@extends('layouts.app')

@section('content')
    <div class="roles">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-gray-700 uppercase font-bold">Edit Role</h2>
            </div>
            <!-- Log on to codeastro.com for more projects -->
            <div class="flex flex-wrap items-center">
                <a href="{{ route('roles-permissions') }}" class="bg-gray-700 text-white text-sm uppercase py-2 px-4 mr-2 flex items-center rounded">
                    <svg class="w-3 h-3 fill-current" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="long-arrow-alt-left" class="svg-inline--fa fa-long-arrow-alt-left fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z"></path></svg>
                    <span class="ml-2 text-xs font-semibold">Back</span>
                </a>
                <a href="{{ route('role.create') }}" class="bg-green-500 text-white text-sm uppercase py-2 px-4 mr-2 flex items-center rounded">
                    <svg class="w-3 h-3 fill-current" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus" class="svg-inline--fa fa-plus fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"></path></svg>
                    <span class="ml-2 text-xs font-semibold">Role</span>
                </a>
                <a href="{{ route('permission.create') }}" class="bg-gray-200 text-gray-700 text-sm uppercase py-2 px-4 flex items-center rounded">
                    <svg class="w-3 h-3 fill-current" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus" class="svg-inline--fa fa-plus fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"></path></svg>
                    <span class="ml-2 text-xs font-semibold">Permission</span>
                </a>
            </div>
        </div>
        <!-- Log on to codeastro.com for more projects -->
        <div class="table w-full mt-8 bg-white rounded">
            <form action="{{ route('role.update',$role->id) }}" method="POST" class="w-full max-w-lg px-6 py-12">
                @csrf
                @method('PUT')
                <div class="md:flex md:items-center mb-6">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                            Role Name
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <input name="name" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" id="inline-full-name" type="text" value="{{ $role->name }}">
                        @error('name')
                            <p class="text-red-500 text-xs italic">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="md:flex mb-6">
                    <div class="md:w-1/3">
                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                            Permissions
                        </label>
                    </div>
                    <div class="md:w-2/3">
                        <!-- Search Bar -->
                        <div class="mb-4">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text" id="permissionSearch" placeholder="Search permissions..." 
                                    class="block w-full pl-10 pr-4 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="block text-gray-600 font-bold max-h-64 overflow-y-auto" id="permissionsContainer">
                            @foreach ($permissions as $permission)
                                <div class="flex items-center permission-item">
                                    <label>
                                        <input name="selectedpermissions[]" class="mr-2 leading-tight" type="checkbox" value="{{ $permission->name }}"
                                            @foreach ($role->permissions as $item)
                                                {{ ($item->id === $permission->id) ? 'checked' : '' }}
                                            @endforeach
                                        >
                                        <span class="text-sm">
                                            {{ $permission->name }}
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="md:flex md:items-center">
                    <div class="md:w-1/3"></div>
                    <div class="md:w-2/3">
                        <button class="shadow bg-blue-500 hover:bg-blue-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded" type="submit">
                            Update Role
                        </button>
                    </div>
                </div>
            </form>        
        </div>
        <!-- Log on to codeastro.com for more projects -->
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('permissionSearch');
    const permissionItems = document.querySelectorAll('.permission-item');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        permissionItems.forEach(function(item) {
            const permissionText = item.textContent.toLowerCase();
            
            if (permissionText.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
});
</script>
@endpush