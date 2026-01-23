@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Scholarship Students</h1>
            <p class="text-gray-600 text-sm mt-1">Manage students with scholarships, their student type, and curriculum</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="bg-emerald-100 text-emerald-800 px-3 py-1 rounded-full text-sm font-medium">
                {{ $students->total() }} Students with Scholarships
            </span>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.scholarships.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500"
                       placeholder="Search by name...">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Class</label>
                <select name="class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Student Type</label>
                <select name="student_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <option value="day" {{ request('student_type') == 'day' ? 'selected' : '' }}>Day</option>
                    <option value="boarding" {{ request('student_type') == 'boarding' ? 'selected' : '' }}>Boarding</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Curriculum</label>
                <select name="curriculum_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                    <option value="">All Curricula</option>
                    <option value="zimsec" {{ request('curriculum_type') == 'zimsec' ? 'selected' : '' }}>ZIMSEC</option>
                    <option value="cambridge" {{ request('curriculum_type') == 'cambridge' ? 'selected' : '' }}>Cambridge</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    Filter
                </button>
                <a href="{{ route('admin.scholarships.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6" id="bulk-actions" style="display: none;">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-medium text-gray-700">
                <span id="selected-count">0</span> students selected
            </span>
            <div class="flex items-center gap-2">
                <select id="bulk-student-type" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Change Student Type</option>
                    <option value="day">Day</option>
                    <option value="boarding">Boarding</option>
                </select>
                <button type="button" onclick="bulkUpdateStudentType()" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm font-medium">
                    Apply
                </button>
            </div>
            <div class="flex items-center gap-2">
                <select id="bulk-curriculum" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Change Curriculum</option>
                    <option value="zimsec">ZIMSEC</option>
                    <option value="cambridge">Cambridge</option>
                </select>
                <button type="button" onclick="bulkUpdateCurriculum()" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium">
                    Apply
                </button>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500" onchange="toggleSelectAll()">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scholarship %</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Type</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curriculum</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $student)
                    <tr class="hover:bg-gray-50" id="student-row-{{ $student->id }}">
                        <td class="px-4 py-3">
                            <input type="checkbox" class="student-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   value="{{ $student->id }}" onchange="updateSelectedCount()">
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    @if($student->photo)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $student->photo) }}" alt="">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <span class="text-blue-600 font-medium text-sm">{{ substr($student->user->name ?? 'S', 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-3">
                                    <a href="{{ route('student.show', $student->id) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                        {{ $student->user->name ?? 'N/A' }}
                                    </a>
                                    @if($student->is_new_student)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">New</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-600">
                            {{ $student->class->class_name ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                {{ $student->scholarship_percentage }}%
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <select class="student-type-select px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    data-student-id="{{ $student->id }}"
                                    data-original="{{ $student->student_type }}"
                                    onchange="updateStudent({{ $student->id }}, 'student_type', this.value)">
                                <option value="day" {{ $student->student_type == 'day' ? 'selected' : '' }}>Day</option>
                                <option value="boarding" {{ $student->student_type == 'boarding' ? 'selected' : '' }}>Boarding</option>
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <select class="curriculum-select px-2 py-1 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    data-student-id="{{ $student->id }}"
                                    data-original="{{ $student->curriculum_type }}"
                                    onchange="updateStudent({{ $student->id }}, 'curriculum_type', this.value)">
                                <option value="zimsec" {{ $student->curriculum_type == 'zimsec' ? 'selected' : '' }}>ZIMSEC</option>
                                <option value="cambridge" {{ $student->curriculum_type == 'cambridge' ? 'selected' : '' }}>Cambridge</option>
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <button type="button" onclick="openEditModal({{ $student->id }}, '{{ $student->user->name ?? 'Student' }}', '{{ $student->student_type }}', '{{ $student->curriculum_type }}', {{ $student->scholarship_percentage }})"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Edit
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-lg font-medium">No scholarship students found</p>
                            <p class="text-sm">Students with scholarships will appear here</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($students->hasPages())
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
            {{ $students->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800" id="modal-title">Edit Student</h3>
        </div>
        <form id="editForm" onsubmit="submitEditForm(event)">
            <input type="hidden" id="edit-student-id">
            <div class="px-6 py-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Student Type</label>
                    <select id="edit-student-type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="day">Day</option>
                        <option value="boarding">Boarding</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Curriculum</label>
                    <select id="edit-curriculum" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="zimsec">ZIMSEC</option>
                        <option value="cambridge">Cambridge</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Scholarship Percentage</label>
                    <input type="number" id="edit-scholarship" min="0" max="100" step="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm font-medium">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleSelectAll() {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAll.checked);
        updateSelectedCount();
    }
    
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.student-checkbox:checked');
        const count = checked.length;
        document.getElementById('selected-count').textContent = count;
        document.getElementById('bulk-actions').style.display = count > 0 ? 'block' : 'none';
    }
    
    function getSelectedStudentIds() {
        const checked = document.querySelectorAll('.student-checkbox:checked');
        return Array.from(checked).map(cb => cb.value);
    }
    
    function bulkUpdateStudentType() {
        const value = document.getElementById('bulk-student-type').value;
        if (!value) {
            alert('Please select a student type');
            return;
        }
        bulkUpdate('student_type', value);
    }
    
    function bulkUpdateCurriculum() {
        const value = document.getElementById('bulk-curriculum').value;
        if (!value) {
            alert('Please select a curriculum');
            return;
        }
        bulkUpdate('curriculum_type', value);
    }
    
    function bulkUpdate(field, value) {
        const ids = getSelectedStudentIds();
        if (ids.length === 0) {
            alert('Please select at least one student');
            return;
        }
        
        fetch('{{ route("admin.scholarships.bulk-update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                student_ids: ids,
                field: field,
                value: value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
    
    function updateStudent(studentId, field, value) {
        const data = {
            student_type: document.querySelector(`select.student-type-select[data-student-id="${studentId}"]`).value,
            curriculum_type: document.querySelector(`select.curriculum-select[data-student-id="${studentId}"]`).value,
            scholarship_percentage: document.querySelector(`#student-row-${studentId} .scholarship-input`)?.value || 
                                   parseFloat(document.querySelector(`#student-row-${studentId} .bg-emerald-100`)?.textContent) || 0
        };
        
        data[field] = value;
        
        fetch(`/admin/scholarships/${studentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show brief success indication
                const row = document.getElementById(`student-row-${studentId}`);
                row.classList.add('bg-green-50');
                setTimeout(() => row.classList.remove('bg-green-50'), 1000);
            } else {
                alert('Error updating student');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
    
    function openEditModal(studentId, studentName, studentType, curriculum, scholarship) {
        document.getElementById('edit-student-id').value = studentId;
        document.getElementById('modal-title').textContent = 'Edit ' + studentName;
        document.getElementById('edit-student-type').value = studentType;
        document.getElementById('edit-curriculum').value = curriculum;
        document.getElementById('edit-scholarship').value = scholarship;
        document.getElementById('editModal').classList.remove('hidden');
        document.getElementById('editModal').classList.add('flex');
    }
    
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.getElementById('editModal').classList.remove('flex');
    }
    
    function submitEditForm(event) {
        event.preventDefault();
        
        const studentId = document.getElementById('edit-student-id').value;
        const data = {
            student_type: document.getElementById('edit-student-type').value,
            curriculum_type: document.getElementById('edit-curriculum').value,
            scholarship_percentage: document.getElementById('edit-scholarship').value
        };
        
        fetch(`/admin/scholarships/${studentId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeEditModal();
                location.reload();
            } else {
                alert('Error updating student');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
    
    // Close modal when clicking outside
    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeEditModal();
        }
    });
</script>
@endsection
