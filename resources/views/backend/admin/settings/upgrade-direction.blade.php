@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Upgrade Direction</h1>
            <p class="text-gray-600 mt-1">Configure how students progress through class levels</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Current Setting Display -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Current Direction Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Current Setting</h3>
                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $upgradeDirection === 'ascending' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ ucfirst($upgradeDirection) }}
                </span>
            </div>
            
            <div class="flex items-center justify-center py-8">
                @if($upgradeDirection === 'ascending')
                <div class="flex items-center space-x-4">
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto">
                            <span class="text-2xl font-bold text-green-600">1</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Lower</p>
                    </div>
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto">
                            <span class="text-2xl font-bold text-green-600">2</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Middle</p>
                    </div>
                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto">
                            <span class="text-2xl font-bold text-green-600">3</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Higher</p>
                    </div>
                </div>
                @else
                <div class="flex items-center space-x-4">
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mx-auto">
                            <span class="text-2xl font-bold text-blue-600">3</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Higher</p>
                    </div>
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mx-auto">
                            <span class="text-2xl font-bold text-blue-600">2</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Middle</p>
                    </div>
                    <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                    <div class="text-center">
                        <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center mx-auto">
                            <span class="text-2xl font-bold text-blue-600">1</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Lower</p>
                    </div>
                </div>
                @endif
            </div>
            
            <p class="text-sm text-gray-500 text-center">
                @if($upgradeDirection === 'ascending')
                    Students progress from lower numeric values to higher (e.g., Grade 1 → Grade 2 → Grade 3)
                @else
                    Students progress from higher numeric values to lower (e.g., Form 6 → Form 5 → Form 4)
                @endif
            </p>
        </div>

        <!-- Update Direction Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Update Direction</h3>
            
            <form action="{{ route('admin.settings.upgrade-direction.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="space-y-4">
                    <label class="block p-4 border-2 rounded-lg cursor-pointer transition-all {{ $upgradeDirection === 'ascending' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300' }}">
                        <div class="flex items-center">
                            <input type="radio" name="upgrade_direction" value="ascending" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300" {{ $upgradeDirection === 'ascending' ? 'checked' : '' }}>
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">Ascending</span>
                                <span class="block text-sm text-gray-500">Students move from lower to higher class numbers (1 → 2 → 3)</span>
                            </div>
                        </div>
                        <div class="mt-2 ml-7">
                            <span class="text-xs text-gray-400">Example: Grade 1 → Grade 2 → Grade 3 → ... → Graduation</span>
                        </div>
                    </label>
                    
                    <label class="block p-4 border-2 rounded-lg cursor-pointer transition-all {{ $upgradeDirection === 'descending' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                        <div class="flex items-center">
                            <input type="radio" name="upgrade_direction" value="descending" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300" {{ $upgradeDirection === 'descending' ? 'checked' : '' }}>
                            <div class="ml-3">
                                <span class="block text-sm font-medium text-gray-900">Descending</span>
                                <span class="block text-sm text-gray-500">Students move from higher to lower class numbers (6 → 5 → 4)</span>
                            </div>
                        </div>
                        <div class="mt-2 ml-7">
                            <span class="text-xs text-gray-400">Example: Form 6 → Form 5 → Form 4 → ... → Graduation</span>
                        </div>
                    </label>
                </div>
                
                <div class="mt-6">
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg shadow-md hover:from-indigo-700 hover:to-purple-700 transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Direction Setting
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Upgrade Preview -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Upgrade Preview</h3>
                <p class="text-sm text-gray-500 mt-1">How classes will upgrade based on current direction setting</p>
            </div>
            <button onclick="refreshPreview()" class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Numeric Value</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Direction</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Class</th>
                    </tr>
                </thead>
                <tbody id="previewBody" class="bg-white divide-y divide-gray-200">
                    @foreach($classes as $class)
                    @php
                        if ($upgradeDirection === 'ascending') {
                            $nextClass = $classes->where('class_numeric', $class->class_numeric + 1)->first();
                        } else {
                            $nextClass = $classes->where('class_numeric', $class->class_numeric - 1)->first();
                        }
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-600 font-bold">{{ $class->class_numeric }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $class->class_name }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $class->class_numeric }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <svg class="w-6 h-6 text-indigo-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($nextClass)
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                                        <span class="text-green-600 font-bold">{{ $nextClass->class_numeric }}</span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-600">{{ $nextClass->class_name }}</p>
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-orange-100 text-orange-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                                    </svg>
                                    Graduation (Final)
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if($classes->isEmpty())
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-sm text-gray-500">
                            No classes defined in the system yet.
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function refreshPreview() {
    location.reload();
}
</script>
@endsection
