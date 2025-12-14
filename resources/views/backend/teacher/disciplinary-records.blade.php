@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Student Disciplinary Records</h1>
        @role('Teacher')
        <button onclick="showAddRecordModal()" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow">
            <svg class="inline h-5 w-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Record
        </button>
        @endrole
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Disciplinary Records Table -->
    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recorded On</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Offense Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Offense Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judgement</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($records as $record)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $record->created_at->format('Y-m-d') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $record->offense_date instanceof \Carbon\Carbon ? $record->offense_date->format('Y-m-d') : $record->offense_date }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $record->offense_type }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $record->student->user->name ?? 'Unknown' }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ Str::limit($record->description, 50) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            @if($record->offense_status == 'Resolved') bg-green-100 text-green-800
                            @elseif($record->offense_status == 'Pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $record->offense_status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">
                        {{ Str::limit($record->judgement ?? 'Pending', 30) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button onclick="showEditModal({{ $record->id }}, '{{ $record->offense_type }}', '{{ $record->offense_status }}', '{{ $record->offense_date instanceof \Carbon\Carbon ? $record->offense_date->format('Y-m-d') : $record->offense_date }}', '{{ addslashes($record->description) }}', '{{ addslashes($record->judgement ?? '') }}')"
                            class="text-blue-600 hover:text-blue-900 mr-3">
                            Edit
                        </button>
                        <button onclick="showDeleteModal({{ $record->id }})" class="text-red-600 hover:text-red-900">
                            Delete
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        No disciplinary records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Record Modal -->
<div id="addRecordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Student Disciplinary Details</h3>
            <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form action="{{ Auth::user()->hasRole('Admin') ? route('admin.disciplinary.store') : route('teacher.disciplinary.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <!-- Select Class -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Class</label>
                    <select name="class_id" id="class_select" onchange="loadStudents()" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select Student -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Student</label>
                    <select name="student_id" id="student_select" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Student --</option>
                    </select>
                </div>

                <!-- Offense Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Offense Status</label>
                    <select name="offense_status" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Status --</option>
                        <option value="Pending">Pending</option>
                        <option value="Under Investigation">Under Investigation</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Escalated">Escalated</option>
                    </select>
                </div>

                <!-- Student Offense Types -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student Offense Types</label>
                    <select name="offense_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Select Offense Type --</option>
                        <option value="Tardiness">Tardiness</option>
                        <option value="Absenteeism">Absenteeism</option>
                        <option value="Disruptive Behavior">Disruptive Behavior</option>
                        <option value="Cheating">Cheating</option>
                        <option value="Bullying">Bullying</option>
                        <option value="Fighting">Fighting</option>
                        <option value="Vandalism">Vandalism</option>
                        <option value="Theft">Theft</option>
                        <option value="Insubordination">Insubordination</option>
                        <option value="Dress Code Violation">Dress Code Violation</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="offense_date" required value="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Offense Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Offense Description</label>
                    <textarea name="description" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Provide detailed description of the offense..."></textarea>
                </div>

                <!-- Judgement -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judgement/Action Taken</label>
                    <textarea name="judgement" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter the judgement or action taken (optional)..."></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeAddModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Submit
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Record Modal -->
<div id="editRecordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Edit Disciplinary Record</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <!-- Offense Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Offense Status</label>
                    <select name="offense_status" id="edit_offense_status" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Pending">Pending</option>
                        <option value="Under Investigation">Under Investigation</option>
                        <option value="Resolved">Resolved</option>
                        <option value="Escalated">Escalated</option>
                    </select>
                </div>

                <!-- Student Offense Types -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student Offense Types</label>
                    <select name="offense_type" id="edit_offense_type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Tardiness">Tardiness</option>
                        <option value="Absenteeism">Absenteeism</option>
                        <option value="Disruptive Behavior">Disruptive Behavior</option>
                        <option value="Cheating">Cheating</option>
                        <option value="Bullying">Bullying</option>
                        <option value="Fighting">Fighting</option>
                        <option value="Vandalism">Vandalism</option>
                        <option value="Theft">Theft</option>
                        <option value="Insubordination">Insubordination</option>
                        <option value="Dress Code Violation">Dress Code Violation</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="offense_date" id="edit_offense_date" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Offense Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Offense Description</label>
                    <textarea name="description" id="edit_description" rows="4" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <!-- Judgement -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judgement/Action Taken</label>
                    <textarea name="judgement" id="edit_judgement" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Enter the judgement or action taken..."></textarea>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-1/3 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-2">Delete Record</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete this disciplinary record? This action cannot be undone.
                </p>
            </div>
            <div class="flex justify-center space-x-3 px-4 py-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Determine base URL based on user role
const isAdmin = {{ Auth::user()->hasRole('Admin') ? 'true' : 'false' }};
const baseUrl = isAdmin ? '/admin/disciplinary-records' : '/teacher/disciplinary-records';

// Show Add Record Modal
function showAddRecordModal() {
    document.getElementById('addRecordModal').classList.remove('hidden');
}

// Close Add Modal
function closeAddModal() {
    document.getElementById('addRecordModal').classList.add('hidden');
    document.getElementById('class_select').value = '';
    document.getElementById('student_select').innerHTML = '<option value="">-- Select Student --</option>';
}

// Load Students by Class
function loadStudents() {
    const classId = document.getElementById('class_select').value;
    const studentSelect = document.getElementById('student_select');

    studentSelect.innerHTML = '<option value="">Loading...</option>';

    if (!classId) {
        studentSelect.innerHTML = '<option value="">-- Select Student --</option>';
        return;
    }

    fetch(`${baseUrl}/class/${classId}/students`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(students => {
            studentSelect.innerHTML = '<option value="">-- Select Student --</option>';
            if (students.length === 0) {
                studentSelect.innerHTML = '<option value="">No students found in this class</option>';
                return;
            }
            students.forEach(student => {
                const option = document.createElement('option');
                option.value = student.id;
                option.textContent = student.name;
                studentSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading students:', error);
            studentSelect.innerHTML = '<option value="">Error loading students. Check console.</option>';
        });
}

// Show Edit Modal
function showEditModal(id, offenseType, offenseStatus, offenseDate, description, judgement) {
    document.getElementById('editForm').action = `${baseUrl}/${id}`;
    document.getElementById('edit_offense_type').value = offenseType;
    document.getElementById('edit_offense_status').value = offenseStatus;
    document.getElementById('edit_offense_date').value = offenseDate;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_judgement').value = judgement || '';
    document.getElementById('editRecordModal').classList.remove('hidden');
}

// Close Edit Modal
function closeEditModal() {
    document.getElementById('editRecordModal').classList.add('hidden');
}

// Show Delete Modal
function showDeleteModal(id) {
    document.getElementById('deleteForm').action = `${baseUrl}/${id}`;
    document.getElementById('deleteModal').classList.remove('hidden');
}

// Close Delete Modal
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const addModal = document.getElementById('addRecordModal');
    const editModal = document.getElementById('editRecordModal');
    const deleteModal = document.getElementById('deleteModal');

    if (event.target == addModal) {
        closeAddModal();
    }
    if (event.target == editModal) {
        closeEditModal();
    }
    if (event.target == deleteModal) {
        closeDeleteModal();
    }
}
</script>

@endsection
