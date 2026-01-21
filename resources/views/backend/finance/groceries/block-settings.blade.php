@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Grocery Block Settings</h1>
            <p class="text-gray-600 mt-2">Control whether grocery arrears block parents and students from viewing results.</p>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        <!-- Settings Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-4">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Results Blocking Control
                </h2>
            </div>

            <form action="{{ route('admin.grocery-block-settings.update') }}" method="POST" class="p-6">
                @csrf

                <!-- Current Status -->
                <div class="mb-6 p-4 rounded-xl {{ $groceryBlockEnabled ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200' }}">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            @if($groceryBlockEnabled)
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            @else
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/>
                            </svg>
                            @endif
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium {{ $groceryBlockEnabled ? 'text-red-800' : 'text-green-800' }}">
                                Current Status: <span class="font-bold">{{ $groceryBlockEnabled ? 'BLOCKING ENABLED' : 'BLOCKING DISABLED' }}</span>
                            </p>
                            <p class="text-sm {{ $groceryBlockEnabled ? 'text-red-600' : 'text-green-600' }}">
                                @if($groceryBlockEnabled)
                                    Parents and students with grocery arrears cannot view results.
                                @else
                                    Grocery arrears do not prevent viewing results.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Toggle Setting -->
                <div class="mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="grocery_block_enabled" value="1" id="grocery_block_toggle" {{ $groceryBlockEnabled ? 'checked' : '' }}
                               style="width: 50px; height: 26px; cursor: pointer;">
                        <span class="ml-4 text-gray-700 font-medium">Enable Grocery Arrears Blocking</span>
                    </label>
                    <p class="text-sm text-gray-500 mt-2">
                        When enabled, parents and students with outstanding grocery items will be blocked from viewing results.
                    </p>
                </div>

                <!-- Which student types to apply blocking to (card-style toggles) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Apply blocking to student types</label>
                    <div id="block-type-cards" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="checkbox" name="block_types[]" value="day" class="hidden block-type-checkbox" {{ in_array('day', $blockedTypes ?? []) ? 'checked' : '' }}>
                            <div class="block-type-card p-4 border rounded-lg transition-shadow flex items-start space-x-4 {{ in_array('day', $blockedTypes ?? []) ? 'border-indigo-500 bg-indigo-50 shadow-sm' : 'border-gray-200 bg-white' }}">
                                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <div class="font-semibold text-gray-800">Day Scholar</div>
                                        <svg class="ml-3 check-icon w-5 h-5 text-indigo-600 {{ in_array('day', $blockedTypes ?? []) ? '' : 'hidden' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Student goes home daily</p>
                                </div>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="checkbox" name="block_types[]" value="boarder" class="hidden block-type-checkbox" {{ in_array('boarder', $blockedTypes ?? []) ? 'checked' : '' }}>
                            <div class="block-type-card p-4 border rounded-lg transition-shadow flex items-start space-x-4 {{ in_array('boarder', $blockedTypes ?? []) ? 'border-amber-500 bg-amber-50 shadow-sm' : 'border-gray-200 bg-white' }}">
                                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3-.895 3-2-1.343-2-3-2zM12 14c-4.418 0-8 1.79-8 4v2h16v-2c0-2.21-3.582-4-8-4z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <div class="font-semibold text-gray-800">Boarding</div>
                                        <svg class="ml-3 check-icon w-5 h-5 text-amber-600 {{ in_array('boarder', $blockedTypes ?? []) ? '' : 'hidden' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Student stays at school</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Select which student types should be affected by grocery arrears blocking.</p>
                </div>

                <script>
                    (function(){
                        const labels = document.querySelectorAll('#block-type-cards label');
                        labels.forEach(label => {
                            const checkbox = label.querySelector('.block-type-checkbox');
                            const card = label.querySelector('.block-type-card');
                            const checkIcon = label.querySelector('.check-icon');

                            function update() {
                                if (checkbox.checked) {
                                    card.classList.add('shadow-sm');
                                    checkIcon.classList.remove('hidden');
                                } else {
                                    card.classList.remove('shadow-sm');
                                    checkIcon.classList.add('hidden');
                                }
                            }

                            // initialize
                            update();

                            // toggle on click
                            label.addEventListener('click', function(e){
                                // prevent double toggling when clicking the checkbox itself
                                if (e.target.tagName.toLowerCase() === 'input') return;
                                checkbox.checked = !checkbox.checked;
                                update();
                            });
                        });
                    })();
                </script>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                    <div class="flex">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-blue-800">How it works</h4>
                            <ul class="text-sm text-blue-700 mt-1 space-y-1">
                                <li>• When <strong>enabled</strong>: Students/parents with unpaid grocery items cannot view results</li>
                                <li>• When <strong>disabled</strong>: Grocery arrears are ignored when viewing results</li>
                                <li>• School fees blocking still applies separately regardless of this setting</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between">
                    <a href="{{ route('admin.groceries.index') }}" class="text-gray-600 hover:text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Groceries
                    </a>
                    <button type="submit" style="background: linear-gradient(to right, #10b981, #14b8a6); color: white; padding: 12px 24px; border-radius: 12px; font-weight: 600; display: inline-flex; align-items: center; border: none; cursor: pointer; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Controls: Search and Apply to Type -->
        <div class="mt-6 mb-4 flex items-center justify-between space-x-4">
            <form method="GET" action="{{ route('admin.grocery-block-settings') }}" class="flex-1">
                <label class="text-sm font-medium text-gray-700">Search student</label>
                <div class="mt-2 flex">
                    <input name="q" value="{{ $q ?? '' }}" type="text" placeholder="Search by name or roll number" class="w-full px-3 py-2 border border-gray-300 rounded-l-lg focus:outline-none">
                    <button type="submit" class="px-4 bg-blue-600 text-white rounded-r-lg">Search</button>
                </div>
            </form>

            <form method="POST" action="{{ route('admin.grocery-block-settings.update') }}" class="w-96 bg-white p-4 rounded-lg shadow-sm">
                @csrf
                <label class="text-sm font-medium text-gray-700">Apply to student type</label>
                <div class="mt-2 flex items-center space-x-2">
                    <select name="apply_type" class="flex-1 px-3 py-2 border border-gray-300 rounded">
                        <option value="">Select type</option>
                        <option value="day">Day</option>
                        <option value="boarder">Boarder</option>
                    </select>
                    <select name="apply_action" class="w-40 px-3 py-2 border border-gray-300 rounded">
                        <option value="block">Block</option>
                        <option value="exempt">Exempt</option>
                    </select>
                    <button type="submit" class="px-3 py-2 bg-emerald-600 text-white rounded">Apply</button>
                </div>
            </form>
        </div>

        <!-- Students with Arrears Section -->
        @if(count($studentsWithArrears) > 0)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-8">
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-4">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Students with Grocery Arrears ({{ count($studentsWithArrears) }})
                </h2>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4">These students have outstanding grocery items. You can exempt individual students from grocery blocking.</p>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Arrears</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($studentsWithArrears as $item)
                            <tr>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">{{ $item['student']->user->name ?? 'N/A' }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-gray-600">
                                    {{ $item['student']->class->class_name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="text-red-600 font-semibold">${{ number_format($item['arrears'], 2) }}</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if($item['student']->grocery_exempt)
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Exempted</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Blocked</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <form action="{{ route('admin.grocery-exempt', $item['student']->id) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @if($item['student']->grocery_exempt)
                                            <button type="submit" class="px-3 py-1 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600">
                                                Block
                                            </button>
                                        @else
                                            <button type="submit" class="px-3 py-1 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600">
                                                Exempt
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mt-8 p-6 text-center">
            <svg class="w-16 h-16 mx-auto text-green-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900">No Students with Grocery Arrears</h3>
            <p class="text-gray-500 mt-2">All students have cleared their grocery obligations.</p>
        </div>
        @endif
    </div>
</div>
@endsection
