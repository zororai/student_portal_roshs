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
                <div>
                    <button onclick="openCleanResultsModal()" class="inline-flex items-center px-5 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 448 512">
                            <path d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.72 23.72 0 0 0-21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0-16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"/>
                        </svg>
                        Clean Results
                    </button>
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
                                <h3 class="text-xl font-bold text-gray-900">{{ $class->class_name }}</h3>
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray/20 text-gray-900 backdrop-blur-sm">
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
                            <a href="{{ route('adminresults.classname', $class->id) }}" class="block w-full text-center px-4 py-3 rounded-xl bg-gradient-to-r from-rose-500 to-pink-600 text-gray-900 font-semibold hover:from-rose-600 hover:to-pink-700 transition-all duration-200 shadow-md hover:shadow-lg">
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

    <!-- Clean Results Modal -->
    <div id="cleanResultsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-900">
                    Clean Results Records
                </h3>
                <button onclick="closeCleanResultsModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">Warning: This action cannot be undone!</p>
                        <p class="text-xs mt-1">Please carefully select the criteria for deleting results records.</p>
                    </div>
                </div>
            </div>

            <form id="cleanResultsForm" class="space-y-4">
                @csrf
                
                <!-- Clean All Option -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="cleanAllCheckbox" onchange="toggleCleanAllOption()" class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500">
                        <span class="ml-3 text-sm font-semibold text-gray-900">Clean All Published Results</span>
                    </label>
                    <p class="ml-8 text-xs text-gray-500 mt-1">This will delete all published results records from the database</p>
                </div>

                <div id="filterOptions">
                    <!-- Select Class -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Class (Optional)</label>
                        <select id="cleanClassId" name="class_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">-- All Classes --</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Select Year -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Year (Optional)</label>
                        <select id="cleanYear" name="year" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">-- All Years --</option>
                            @php
                                $currentYear = date('Y');
                                for($y = $currentYear; $y >= $currentYear - 10; $y--) {
                                    echo "<option value='$y'>$y</option>";
                                }
                            @endphp
                        </select>
                    </div>

                    <!-- Select Term -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Term (Optional)</label>
                        <select id="cleanTerm" name="term" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">-- All Terms --</option>
                            <option value="first">First Term</option>
                            <option value="second">Second Term</option>
                            <option value="third">Third Term</option>
                        </select>
                    </div>
                </div>

                <!-- Confirmation Input -->
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <label class="block text-sm font-medium text-red-700 mb-2">Type "DELETE" to confirm</label>
                    <input type="text" id="confirmDelete" class="w-full px-4 py-2 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="Type DELETE to confirm">
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeCleanResultsModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="button" onclick="submitCleanResults()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Clean Results
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openCleanResultsModal() {
        document.getElementById('cleanResultsModal').classList.remove('hidden');
        document.getElementById('cleanAllCheckbox').checked = false;
        document.getElementById('cleanClassId').value = '';
        document.getElementById('cleanYear').value = '';
        document.getElementById('cleanTerm').value = '';
        document.getElementById('confirmDelete').value = '';
        document.getElementById('filterOptions').classList.remove('opacity-50', 'pointer-events-none');
    }

    function closeCleanResultsModal() {
        document.getElementById('cleanResultsModal').classList.add('hidden');
    }

    function toggleCleanAllOption() {
        const cleanAll = document.getElementById('cleanAllCheckbox').checked;
        const filterOptions = document.getElementById('filterOptions');
        
        if (cleanAll) {
            filterOptions.classList.add('opacity-50', 'pointer-events-none');
            document.getElementById('cleanClassId').value = '';
            document.getElementById('cleanYear').value = '';
            document.getElementById('cleanTerm').value = '';
        } else {
            filterOptions.classList.remove('opacity-50', 'pointer-events-none');
        }
    }

    function submitCleanResults() {
        const confirmText = document.getElementById('confirmDelete').value;
        
        if (confirmText !== 'DELETE') {
            alert('Please type "DELETE" to confirm this action.');
            return;
        }

        const cleanAll = document.getElementById('cleanAllCheckbox').checked;
        const classId = document.getElementById('cleanClassId').value;
        const year = document.getElementById('cleanYear').value;
        const term = document.getElementById('cleanTerm').value;

        if (!cleanAll && !classId && !year && !term) {
            alert('Please select at least one filter option or check "Clean All Published Results".');
            return;
        }

        if (!confirm('Are you absolutely sure you want to delete these results? This action cannot be undone!')) {
            return;
        }

        // Show loading state
        const submitBtn = event.target;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';

        // Submit the form
        fetch('{{ route("admin.clean-results") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                clean_all: cleanAll,
                class_id: classId,
                year: year,
                term: term
            })
        })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Clean Results';
            
            if (data.success) {
                alert(data.message + '\n' + data.deleted_count + ' record(s) deleted.');
                closeCleanResultsModal();
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to clean results'));
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Clean Results';
            console.error('Error:', error);
            alert('An error occurred while cleaning results. Please try again.');
        });
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('cleanResultsModal');
        if (event.target == modal) {
            closeCleanResultsModal();
        }
    }
    </script>
@endsection