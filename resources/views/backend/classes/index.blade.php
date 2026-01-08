@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Classes</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage classes, students, and subject assignments</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="openCleanResultsModal()" class="inline-flex items-center px-5 py-3 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 448 512">
                            <path d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.72 23.72 0 0 0-21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0-16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"/>
                        </svg>
                        Clean Results
                    </button>
                    <a href="{{ route('classes.create') }}" class="inline-flex items-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 448 512">
                            <path d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/>
                        </svg>
                        Add New Class
                    </a>
                </div>
            </div>
        </div>

        <!-- Classes Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                #
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Class Name
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Students
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Subjects
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Class Teacher
                            </th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($classes as $class)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900">{{ $class->class_numeric }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $class->class_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                        {{ $class->students_count }} {{ Str::plural('Student', $class->students_count) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-2">
                                        @forelse ($class->subjects as $subject)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                {{ $subject->subject_code }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-gray-400 italic">No subjects assigned</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600">{{ $class->teacher->user->name ?? 'Not Assigned' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('classes.edit',$class->id) }}" class="inline-flex items-center p-2 bg-green-100 hover:bg-green-200 text-green-600 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512">
                                                <path d="M400 480H48c-26.5 0-48-21.5-48-48V80c0-26.5 21.5-48 48-48h352c26.5 0 48 21.5 48 48v352c0 26.5-21.5 48-48 48zM238.1 177.9L102.4 313.6l-6.3 57.1c-.8 7.6 5.6 14.1 13.3 13.3l57.1-6.3L302.2 242c2.3-2.3 2.3-6.1 0-8.5L246.7 178c-2.5-2.4-6.3-2.4-8.6-.1zM345 165.1L314.9 135c-9.4-9.4-24.6-9.4-33.9 0l-23.1 23.1c-2.3 2.3-2.3 6.1 0 8.5l55.5 55.5c2.3 2.3 6.1 2.3 8.5 0L345 199c9.3-9.3 9.3-24.5 0-33.9z"/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('classes.destroy',$class->id) }}" method="POST" class="inline-flex">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center p-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors" title="Delete">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512">
                                                    <path d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.72 23.72 0 0 0-21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0-16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"/>
                                                </svg>
                                            </button>
                                        </form>
                                        <a href="{{ route('class.assign.subject',$class->id) }}" class="inline-flex items-center p-2 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg transition-colors" title="Assign Subject">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512">
                                                <path d="M160 84V44c0-8.837 7.163-16 16-16h256c8.837 0 16 7.163 16 16v40c0 8.837-7.163 16-16 16H176c-8.837 0-16-7.163-16-16zM16 228h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 256h416c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H16c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm160-128h256c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H176c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                        <p class="text-gray-500 text-lg font-medium">No classes found</p>
                                        <p class="text-gray-400 text-sm mt-1">Get started by adding your first class</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $classes->links() }}
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
