@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Seat Assignment</h1>
                <p class="mt-2 text-sm text-gray-600">Manage student chair and desk assignments</p>
            </div>
        </div>
    </div>

    <!-- Filter by Class -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Class</label>
                <select id="classFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Student</label>
                <input type="text" id="searchInput" placeholder="Search by name or roll number..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Chair</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Desk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="studentsTableBody">
                    @foreach($students as $student)
                    <tr class="student-row hover:bg-gray-50" data-class-id="{{ $student->class_id }}" data-name="{{ strtolower($student->user->name ?? '') }}" data-roll="{{ strtolower($student->roll_number ?? '') }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                                    {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $student->user->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $student->user->email ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->class->class_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->roll_number ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span id="chair-{{ $student->id }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student->chair ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-500' }}">
                                {{ $student->chair ?? 'Not assigned' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span id="desk-{{ $student->id }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $student->desk ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-500' }}">
                                {{ $student->desk ?? 'Not assigned' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button onclick="openEditModal({{ $student->id }}, '{{ $student->user->name ?? 'Student' }}', '{{ $student->chair ?? '' }}', '{{ $student->desk ?? '' }}')" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeEditModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">Edit Seat Assignment</h3>
                <p id="modalStudentName" class="text-blue-100 text-sm">Student Name</p>
            </div>
            <div class="bg-white px-6 py-4">
                <input type="hidden" id="editStudentId">
                <div class="space-y-4">
                    <div>
                        <label for="editChair" class="block text-sm font-medium text-gray-700 mb-1">Chair Number</label>
                        <input type="text" id="editChair" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., A1, B5, 12">
                    </div>
                    <div>
                        <label for="editDesk" class="block text-sm font-medium text-gray-700 mb-1">Desk/Table Number</label>
                        <input type="text" id="editDesk" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., T1, D3, 7">
                    </div>
                </div>
                <div id="editMessage" class="mt-4 hidden"></div>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="saveSeatAssignment()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Filter functionality
document.getElementById('classFilter').addEventListener('change', filterStudents);
document.getElementById('searchInput').addEventListener('input', filterStudents);

function filterStudents() {
    const classId = document.getElementById('classFilter').value;
    const search = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.student-row');

    rows.forEach(row => {
        const rowClassId = row.dataset.classId;
        const name = row.dataset.name;
        const roll = row.dataset.roll;

        const matchesClass = !classId || rowClassId === classId;
        const matchesSearch = !search || name.includes(search) || roll.includes(search);

        row.style.display = matchesClass && matchesSearch ? '' : 'none';
    });
}

function openEditModal(studentId, studentName, chair, desk) {
    document.getElementById('editStudentId').value = studentId;
    document.getElementById('modalStudentName').textContent = studentName;
    document.getElementById('editChair').value = chair;
    document.getElementById('editDesk').value = desk;
    document.getElementById('editMessage').classList.add('hidden');
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function saveSeatAssignment() {
    const studentId = document.getElementById('editStudentId').value;
    const chair = document.getElementById('editChair').value;
    const desk = document.getElementById('editDesk').value;
    const messageDiv = document.getElementById('editMessage');

    fetch(`/admin/seat-assignment/${studentId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ chair: chair, desk: desk })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update table cells
            const chairCell = document.getElementById(`chair-${studentId}`);
            const deskCell = document.getElementById(`desk-${studentId}`);
            
            chairCell.textContent = data.chair || 'Not assigned';
            chairCell.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data.chair ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-500'}`;
            
            deskCell.textContent = data.desk || 'Not assigned';
            deskCell.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${data.desk ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-500'}`;

            messageDiv.innerHTML = '<div class="p-3 bg-green-100 text-green-700 rounded-lg">' + data.message + '</div>';
            messageDiv.classList.remove('hidden');

            setTimeout(() => {
                closeEditModal();
            }, 1500);
        } else {
            messageDiv.innerHTML = '<div class="p-3 bg-red-100 text-red-700 rounded-lg">' + data.message + '</div>';
            messageDiv.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.innerHTML = '<div class="p-3 bg-red-100 text-red-700 rounded-lg">An error occurred. Please try again.</div>';
        messageDiv.classList.remove('hidden');
    });
}
</script>
@endsection
