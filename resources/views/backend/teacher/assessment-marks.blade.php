@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $class->class_name }} - Assessment Marks</h1>
                    <p class="mt-2 text-sm text-gray-600">Add and manage assessment marks for students</p>
                </div>
                <a href="{{ route('teacher.assessment.list', $class->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Assessments
                </a>
            </div>
        </div>

        @if(session('success') || request('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('success') ?? request('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Assessment Selection -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <label for="assessmentSelect" class="block text-sm font-semibold text-gray-700 mb-3">Select Assessment</label>
            <select id="assessmentSelect" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none bg-white" onchange="loadAssessmentMarks()">
                <option value="">Choose an assessment...</option>
                @foreach($assessments as $assessment)
                    <option value="{{ $assessment->id }}" 
                            data-papers="{{ htmlspecialchars(json_encode($assessment->papers ?? []), ENT_QUOTES, 'UTF-8') }}"
                            data-subject="{{ $assessment->subject->name ?? 'N/A' }}"
                            data-subject-id="{{ $assessment->subject_id }}"
                            data-topic="{{ $assessment->topic }}"
                            data-date="{{ $assessment->date->format('D, d M Y') }}"
                            data-type="{{ $assessment->assessment_type }}">
                        {{ $assessment->subject->name ?? 'N/A' }} - {{ $assessment->topic }} ({{ $assessment->date->format('M d, Y') }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Assessment Details and Marks Table -->
        <div id="marksContainer" class="hidden">
            <!-- Assessment Info -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-200 p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Subject</p>
                        <p class="text-lg font-bold text-gray-900" id="assessmentSubject">-</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Topic</p>
                        <p class="text-lg font-bold text-gray-900" id="assessmentTopic">-</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Date</p>
                        <p class="text-lg font-bold text-gray-900" id="assessmentDate">-</p>
                    </div>
                </div>
            </div>

            <!-- Marks Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr id="tableHeaderRow">
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sticky left-0 bg-gray-50">
                                    Student
                                </th>
                                <!-- Paper columns will be dynamically added here -->
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="marksTableBody">
                            @forelse($students as $student)
                                <tr class="hover:bg-gray-50 transition-colors" data-student-id="{{ $student->id }}">
                                    <td class="px-6 py-4 whitespace-nowrap sticky left-0 bg-white">
                                        <div class="flex items-center">
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $student->user->name }}</div>
                                                <div class="text-xs text-gray-500">ID: {{ $student->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="paper-marks-container">
                                        <!-- Paper marks will be dynamically added here -->
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                            </svg>
                                            <p class="text-gray-500 text-lg font-medium">No students found in this class</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Save Button -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="button" onclick="saveMarks()" class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 448 512">
                            <path d="M433.941 129.941l-83.882-83.882A48 48 0 0 0 316.118 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V163.882a48 48 0 0 0-14.059-33.941zM224 416c-35.346 0-64-28.654-64-64 0-35.346 28.654-64 64-64s64 28.654 64 64c0 35.346-28.654 64-64 64zm96-304.52V212c0 6.627-5.373 12-12 12H76c-6.627 0-12-5.373-12-12V108c0-6.627 5.373-12 12-12h228.52c3.183 0 6.235 1.264 8.485 3.515l3.48 3.48A11.996 11.996 0 0 1 320 111.48z"/>
                        </svg>
                        Save All Marks
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentAssessmentId = null;
        let currentPapers = [];
        let currentSubjectId = null;
        let assessmentComments = @json($assessmentComments);

        function loadAssessmentMarks() {
            const select = document.getElementById('assessmentSelect');
            const selectedOption = select.options[select.selectedIndex];
            
            if (!selectedOption.value) {
                document.getElementById('marksContainer').classList.add('hidden');
                return;
            }

            currentAssessmentId = selectedOption.value;
            
            // Parse papers data - handle HTML entities
            let papersData = selectedOption.dataset.papers;
            console.log('Raw papers data:', papersData);
            
            // Decode HTML entities
            const textArea = document.createElement('textarea');
            textArea.innerHTML = papersData;
            papersData = textArea.value;
            
            try {
                currentPapers = JSON.parse(papersData);
                console.log('Parsed papers:', currentPapers);
                
                // Ensure papers have numeric values
                currentPapers = currentPapers.map(paper => ({
                    ...paper,
                    total_marks: parseInt(paper.total_marks) || 0,
                    weight: parseInt(paper.weight) || 0
                }));
            } catch (e) {
                console.error('Error parsing papers:', e);
                currentPapers = [];
            }
            
            currentSubjectId = parseInt(selectedOption.dataset.subjectId);

            // Update assessment info
            document.getElementById('assessmentSubject').textContent = selectedOption.dataset.subject;
            document.getElementById('assessmentTopic').textContent = selectedOption.dataset.topic;
            document.getElementById('assessmentDate').textContent = selectedOption.dataset.date;

            // Build paper headers
            buildPaperHeaders();

            // Build marks inputs for each student
            buildMarksInputs();

            // Show the marks container
            document.getElementById('marksContainer').classList.remove('hidden');
        }

        function buildPaperHeaders() {
            const headerRow = document.getElementById('tableHeaderRow');
            
            // Remove any existing paper headers (keep only the Student header)
            while (headerRow.children.length > 1) {
                headerRow.removeChild(headerRow.lastChild);
            }

            // Filter out papers with null or undefined values
            const validPapers = currentPapers.filter(paper => 
                paper && paper.name && paper.total_marks && paper.weight
            );

            // Add header for each valid paper
            validPapers.forEach((paper, index) => {
                const th = document.createElement('th');
                th.scope = 'col';
                th.className = 'px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider border-l border-gray-200';
                th.innerHTML = `
                    <div class="space-y-1">
                        <div class="font-bold text-blue-600">Paper: ${paper.name}</div>
                        <div class="text-gray-500">Date: ${document.getElementById('assessmentDate').textContent}</div>
                        <div class="text-gray-500">Out of: ${paper.total_marks}</div>
                        <div class="text-gray-500">Weight: ${paper.weight}%</div>
                        <div class="text-gray-700 mt-2">Mark | Comment</div>
                    </div>
                `;
                headerRow.appendChild(th);
            });
            
            // Update currentPapers to only include valid papers
            currentPapers = validPapers;
        }

        function buildMarksInputs() {
            const rows = document.querySelectorAll('tbody tr[data-student-id]');

            rows.forEach(row => {
                const studentId = row.dataset.studentId;
                
                // Remove the placeholder td
                const marksContainer = row.querySelector('.paper-marks-container');
                if (marksContainer) {
                    marksContainer.remove();
                }
                
                // Add individual td elements for each paper
                currentPapers.forEach((paper, paperIndex) => {
                    const td = document.createElement('td');
                    td.className = 'px-6 py-4 border-l border-gray-200';
                    td.innerHTML = `
                        <div class="space-y-2">
                            <select name="marks[${studentId}][${paperIndex}][absence_reason]"
                                    class="absence-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                    data-student="${studentId}"
                                    data-paper="${paperIndex}"
                                    onchange="handleAbsenceChange(this)">
                                <option value="">Present</option>
                                <option value="Sick">Sick</option>
                                <option value="Absent">Absent</option>
                                <option value="Late">Late</option>
                                <option value="Excused">Excused</option>
                            </select>
                            <input type="number" 
                                   name="marks[${studentId}][${paperIndex}][mark]"
                                   placeholder="Mark"
                                   min="0"
                                   max="${paper.total_marks}"
                                   class="mark-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                   data-student="${studentId}"
                                   data-paper="${paperIndex}"
                                   data-total-marks="${paper.total_marks}"
                                   onchange="autoPopulateComment(this)">
                            <textarea name="marks[${studentId}][${paperIndex}][comment]"
                                      placeholder="Comment (optional)"
                                      rows="2"
                                      maxlength="500"
                                      class="comment-textarea w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm resize-none"
                                      data-student="${studentId}"
                                      data-paper="${paperIndex}"></textarea>
                        </div>
                    `;
                    row.appendChild(td);
                });
            });
        }

        function handleAbsenceChange(selectElement) {
            const studentId = selectElement.dataset.student;
            const paperIndex = selectElement.dataset.paper;
            const absenceReason = selectElement.value;
            
            // Find the corresponding mark input and comment textarea
            const markInput = document.querySelector(`input[name="marks[${studentId}][${paperIndex}][mark]"]`);
            const commentTextarea = document.querySelector(`textarea[name="marks[${studentId}][${paperIndex}][comment]"]`);
            
            if (absenceReason) {
                // Student is absent - disable mark entry and clear values
                markInput.disabled = true;
                markInput.value = '';
                markInput.style.backgroundColor = '#f3f4f6';
                commentTextarea.value = absenceReason;
                commentTextarea.disabled = true;
                commentTextarea.style.backgroundColor = '#f3f4f6';
            } else {
                // Student is present - enable mark entry
                markInput.disabled = false;
                markInput.style.backgroundColor = '';
                commentTextarea.value = '';
                commentTextarea.disabled = false;
                commentTextarea.style.backgroundColor = '';
            }
        }

        function calculateGrade(mark, totalMarks) {
            const percentage = (mark / totalMarks) * 100;
            
            if (percentage >= 80) return 'A';
            if (percentage >= 70) return 'B';
            if (percentage >= 60) return 'C';
            if (percentage >= 50) return 'D';
            if (percentage >= 40) return 'E';
            return 'F';
        }

        function autoPopulateComment(markInput) {
            const mark = parseFloat(markInput.value);
            const totalMarks = parseFloat(markInput.dataset.totalMarks);
            const studentId = markInput.dataset.student;
            const paperIndex = markInput.dataset.paper;

            console.log('Mark entered:', mark);
            console.log('Total marks:', totalMarks);
            console.log('Current subject ID:', currentSubjectId);

            if (!mark || !totalMarks || !currentSubjectId) {
                console.log('Missing required data');
                return;
            }

            // Calculate grade based on percentage
            const grade = calculateGrade(mark, totalMarks);
            console.log('Calculated grade:', grade);

            // Find the corresponding comment for this subject and grade
            const comment = assessmentComments.find(c => 
                c.subject_id === currentSubjectId && c.grade === grade
            );

            console.log('Found comment:', comment);

            // Populate the comment textarea
            const commentTextarea = document.querySelector(
                `textarea[name="marks[${studentId}][${paperIndex}][comment]"]`
            );

            if (commentTextarea && comment) {
                commentTextarea.value = comment.comment;
                console.log('Comment populated successfully');
                
                // Trigger auto-save after comment is populated
                setTimeout(() => {
                    checkAndAutoSave(studentId, paperIndex);
                }, 500);
            } else {
                console.log('Comment textarea or comment not found');
            }
        }

        function checkAndAutoSave(studentId, paperIndex) {
            const markInput = document.querySelector(`input[name="marks[${studentId}][${paperIndex}][mark]"]`);
            const commentTextarea = document.querySelector(`textarea[name="marks[${studentId}][${paperIndex}][comment]"]`);

            const mark = markInput ? parseFloat(markInput.value) : null;
            const comment = commentTextarea ? commentTextarea.value.trim() : '';

            console.log('Checking auto-save:', {mark, comment, hasComment: comment.length > 0});

            // Auto-save if mark is entered and comment exists
            if (mark && comment) {
                autoSaveMark(studentId, paperIndex, mark, markInput.dataset.totalMarks, comment);
            }
        }

        function autoSaveMark(studentId, paperIndex, mark, totalMarks, comment) {
            if (!currentAssessmentId || !currentPapers[paperIndex]) {
                console.error('Missing assessment or paper data');
                return;
            }

            const paper = currentPapers[paperIndex];
            const data = {
                assessment_id: currentAssessmentId,
                student_id: studentId,
                paper_name: paper.name,
                paper_index: paperIndex,
                mark: mark,
                total_marks: totalMarks,
                comment: comment
            };

            console.log('Auto-saving mark:', data);

            // Show saving indicator
            const markInput = document.querySelector(`input[name="marks[${studentId}][${paperIndex}][mark]"]`);
            if (markInput) {
                markInput.style.borderColor = '#FFA500';
            }

            fetch('{{ route("teacher.assessment.marks.save") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                console.log('Save result:', result);
                if (result.success) {
                    // Show success indicator
                    if (markInput) {
                        markInput.style.borderColor = '#10B981';
                        setTimeout(() => {
                            markInput.style.borderColor = '';
                        }, 2000);
                    }
                } else {
                    console.error('Save failed:', result.message);
                    if (markInput) {
                        markInput.style.borderColor = '#EF4444';
                    }
                }
            })
            .catch(error => {
                console.error('Auto-save error:', error);
                if (markInput) {
                    markInput.style.borderColor = '#EF4444';
                }
            });
        }

        async function saveMarks() {
            if (!currentAssessmentId) {
                showErrorDialog('Please select an assessment first.');
                return;
            }

            const marksData = [];
            const rows = document.querySelectorAll('tbody tr[data-student-id]');

            rows.forEach(row => {
                const studentId = row.dataset.studentId;

                currentPapers.forEach((paper, paperIndex) => {
                    const markInput = row.querySelector(`input[name="marks[${studentId}][${paperIndex}][mark]"]`);
                    const commentTextarea = row.querySelector(`textarea[name="marks[${studentId}][${paperIndex}][comment]"]`);

                    if (markInput && markInput.value) {
                        marksData.push({
                            assessment_id: currentAssessmentId,
                            student_id: studentId,
                            paper_index: paperIndex,
                            paper_name: paper.name,
                            mark: parseFloat(markInput.value),
                            total_marks: paper.total_marks,
                            comment: commentTextarea ? commentTextarea.value : ''
                        });
                    }
                });
            });

            if (marksData.length === 0) {
                showErrorDialog('Please enter at least one mark before saving.');
                return;
            }

            // Show saving indicator
            const saveButton = document.querySelector('button[onclick="saveMarks()"]');
            const originalText = saveButton.innerHTML;
            saveButton.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...';
            saveButton.disabled = true;

            let successCount = 0;
            let errorCount = 0;

            // Save each mark individually
            for (const markData of marksData) {
                try {
                    const response = await fetch('{{ route("teacher.assessment.marks.save") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(markData)
                    });

                    const result = await response.json();
                    if (result.success) {
                        successCount++;
                    } else {
                        errorCount++;
                        console.error('Save failed:', result.message);
                    }
                } catch (error) {
                    errorCount++;
                    console.error('Save error:', error);
                }
            }

            // Restore button
            saveButton.innerHTML = originalText;
            saveButton.disabled = false;

            // Show result and redirect on success
            if (errorCount === 0) {
                // Redirect back to the same page with success message
                window.location.href = '{{ route("teacher.assessment.marks", $class->id) }}?success=' + encodeURIComponent(`${successCount} mark(s) saved successfully!`);
            } else if (successCount > 0) {
                showSuccessDialog('Partially saved', `${successCount} mark(s) saved, ${errorCount} failed.`);
            } else {
                showErrorDialog('Failed to save marks. Please try again.');
            }
        }

        function showSuccessDialog(title, message) {
            const modalContent = `
                <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="successModal">
                    <div class="relative top-20 mx-auto p-8 w-full max-w-md">
                        <div class="bg-white rounded-2xl shadow-2xl">
                            <div class="px-8 py-6">
                                <div class="flex items-center justify-center mb-4">
                                    <div class="bg-green-100 rounded-full p-3">
                                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">${title}</h3>
                                <p class="text-gray-600 text-center mb-6">${message}</p>
                                <div class="flex justify-center">
                                    <button type="button" onclick="closeSuccessModal()" class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                                        OK
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalContent);
        }

        function closeSuccessModal() {
            const modal = document.getElementById('successModal');
            if (modal) {
                modal.remove();
            }
        }

        function showErrorDialog(message) {
            const modalContent = `
                <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="errorModal">
                    <div class="relative top-20 mx-auto p-8 w-full max-w-md">
                        <div class="bg-white rounded-2xl shadow-2xl">
                            <div class="px-8 py-6">
                                <div class="flex items-center justify-center mb-4">
                                    <div class="bg-red-100 rounded-full p-3">
                                        <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Error</h3>
                                <p class="text-gray-600 text-center mb-6">${message}</p>
                                <div class="flex justify-center">
                                    <button type="button" onclick="closeErrorModal()" class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors">
                                        OK
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalContent);
        }

        function closeErrorModal() {
            const modal = document.getElementById('errorModal');
            if (modal) {
                modal.remove();
            }
        }
    </script>
@endsection
