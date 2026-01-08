@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Approve Assessment Marks</h1>
        <div class="flex space-x-3">
            <button onclick="approveAllMarksGlobal()" id="approveAllGlobalBtn" 
                class="{{ $totalPending > 0 ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed' }} text-white font-semibold py-2 px-4 rounded-lg flex items-center"
                {{ $totalPending == 0 ? 'disabled' : '' }}>
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Approve All ({{ $totalPending }})
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-yellow-600">Pending Approval</p>
                    <p class="text-2xl font-bold text-yellow-800">{{ $totalPending }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-blue-600">Classes with Pending</p>
                    <p class="text-2xl font-bold text-blue-800">{{ $pendingCounts->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-green-600">Total Classes</p>
                    <p class="text-2xl font-bold text-green-800">{{ $classes->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Classes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($classes as $class)
            @php
                $pending = $pendingCounts->get($class->id);
                $hasPending = $pending && $pending->marks_count > 0;
            @endphp
            <div class="bg-white rounded-lg shadow-sm border {{ $hasPending ? 'border-yellow-300' : 'border-gray-200' }} p-4 hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-semibold text-gray-800">{{ $class->class_name }}</h3>
                        <p class="text-sm text-gray-500">{{ $class->students_count }} students</p>
                    </div>
                    @if($hasPending)
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full">
                            {{ $pending->marks_count }} pending
                        </span>
                    @else
                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full">
                            All approved
                        </span>
                    @endif
                </div>
                
                @if($hasPending)
                    <div class="text-sm text-gray-600 mb-3">
                        <span class="font-medium">{{ $pending->assessment_count }}</span> assessment(s) with pending marks
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="viewPendingMarks({{ $class->id }}, '{{ $class->class_name }}')" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors">
                            View Pending
                        </button>
                        <button onclick="approveClassMarks({{ $class->id }}, '{{ $class->class_name }}')" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-3 rounded-lg transition-colors">
                            Approve All
                        </button>
                    </div>
                @else
                    <div class="text-sm text-gray-500 italic">
                        No pending assessment marks
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

<!-- Assessment Marks Modal -->
<div id="marksModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-8 w-full max-w-4xl">
        <div class="bg-white rounded-2xl shadow-2xl">
            <div class="flex items-center justify-between px-8 py-6 border-b border-gray-200">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900" id="modalTitle">Pending Assessment Marks</h3>
                    <p class="text-sm text-gray-500 mt-1" id="modalSubtitle"></p>
                </div>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="px-8 py-6 max-h-[60vh] overflow-y-auto" id="modalContent">
                <div class="flex items-center justify-center py-8">
                    <svg class="animate-spin h-8 w-8 text-blue-600" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="ml-3 text-gray-600">Loading...</span>
                </div>
            </div>
            <div class="px-8 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-lg transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="hidden fixed bottom-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50"></div>

@push('scripts')
<script>
    let currentClassId = null;

    function viewPendingMarks(classId, className) {
        currentClassId = classId;
        document.getElementById('modalTitle').textContent = className + ' - Pending Marks';
        document.getElementById('modalSubtitle').textContent = 'Review and approve assessment marks';
        document.getElementById('marksModal').classList.remove('hidden');
        
        // Load pending assessments
        fetch('{{ route("admin.assessment-marks.get-pending") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ class_id: classId })
        })
        .then(response => response.json())
        .then(data => {
            displayAssessments(data.assessments, className);
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to load pending marks', 'error');
        });
    }

    function displayAssessments(assessments, className) {
        const content = document.getElementById('modalContent');
        
        if (assessments.length === 0) {
            content.innerHTML = `
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="mt-2 text-gray-500">No pending assessment marks</p>
                </div>
            `;
            return;
        }

        let html = '<div class="space-y-4">';
        assessments.forEach(assessment => {
            const teacherName = assessment.teacher && assessment.teacher.user ? assessment.teacher.user.name : 'N/A';
            const subjectName = assessment.subject ? assessment.subject.name : 'N/A';
            
            html += `
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h4 class="font-semibold text-gray-800">${subjectName}</h4>
                            <p class="text-sm text-gray-500">${assessment.assessment_type || 'Assessment'} - ${assessment.topic || 'N/A'}</p>
                            <p class="text-xs text-gray-400">Teacher: ${teacherName}</p>
                        </div>
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full">
                            ${assessment.pending_count} pending
                        </span>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="viewAssessmentMarks(${assessment.id}, '${subjectName}')" 
                            class="bg-blue-100 hover:bg-blue-200 text-blue-700 text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                            View Marks
                        </button>
                        <button onclick="approveAssessmentMarks(${assessment.id})" 
                            class="bg-green-100 hover:bg-green-200 text-green-700 text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                            Approve
                        </button>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        content.innerHTML = html;
    }

    function viewAssessmentMarks(assessmentId, subjectName) {
        fetch('{{ route("admin.assessment-marks.get-marks") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ assessment_id: assessmentId })
        })
        .then(response => response.json())
        .then(data => {
            displayStudentMarks(data.marks, data.assessment, assessmentId);
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to load marks', 'error');
        });
    }

    function displayStudentMarks(marks, assessment, assessmentId) {
        const content = document.getElementById('modalContent');
        document.getElementById('modalSubtitle').textContent = assessment.topic || 'Assessment marks';
        
        let html = `
            <div class="mb-4 flex justify-end">
                <button onclick="approveAssessmentMarks(${assessmentId})" 
                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-lg transition-colors">
                    Approve All Marks
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        `;
        
        Object.entries(marks).forEach(([studentId, studentMarks]) => {
            const firstMark = studentMarks[0];
            const studentName = firstMark.student && firstMark.student.user ? firstMark.student.user.name : 'Unknown';
            
            let marksHtml = '';
            let totalMark = 0;
            let totalPossible = 0;
            
            studentMarks.forEach(mark => {
                totalMark += parseFloat(mark.mark) || 0;
                totalPossible += parseFloat(mark.total_marks) || 0;
                const percentage = mark.total_marks > 0 ? ((mark.mark / mark.total_marks) * 100).toFixed(1) : 0;
                marksHtml += `
                    <div class="flex justify-between items-center py-1 border-b border-gray-100 last:border-0">
                        <span class="text-sm text-gray-600">${mark.paper_name}</span>
                        <span class="text-sm font-medium ${percentage >= 50 ? 'text-green-600' : 'text-red-600'}">${mark.mark}/${mark.total_marks}</span>
                    </div>
                `;
            });
            
            const overallPercentage = totalPossible > 0 ? ((totalMark / totalPossible) * 100).toFixed(1) : 0;
            
            html += `
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="bg-blue-500 px-4 py-2">
                        <h4 class="text-white font-semibold">${studentName}</h4>
                    </div>
                    <div class="p-4">
                        ${marksHtml}
                        <div class="mt-2 pt-2 border-t border-gray-200 flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Total</span>
                            <span class="px-2 py-1 rounded text-sm font-bold ${overallPercentage >= 50 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">${totalMark}/${totalPossible} (${overallPercentage}%)</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        content.innerHTML = html;
    }

    function approveAssessmentMarks(assessmentId) {
        if (!confirm('Are you sure you want to approve all marks for this assessment?')) return;
        
        fetch('{{ route("admin.assessment-marks.approve") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ assessment_id: assessmentId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('Failed to approve marks', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function approveClassMarks(classId, className) {
        if (!confirm(`Are you sure you want to approve all pending marks for ${className}?`)) return;
        
        fetch('{{ route("admin.assessment-marks.approve") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ class_id: classId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('Failed to approve marks', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function approveAllMarksGlobal() {
        if (!confirm('Are you sure you want to approve ALL pending assessment marks across all classes?')) return;
        
        const btn = document.getElementById('approveAllGlobalBtn');
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Approving...';
        
        fetch('{{ route("admin.assessment-marks.approve-all") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('Failed to approve marks', 'error');
                btn.disabled = false;
                btn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Approve All ({{ $totalPending }})';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
            btn.disabled = false;
        });
    }

    function closeModal() {
        document.getElementById('marksModal').classList.add('hidden');
    }

    function showToast(message, type) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = `fixed bottom-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
        
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }
</script>
@endpush
@endsection
