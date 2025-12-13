@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $class->class_name }} - Marking Scheme</h1>
                    <p class="mt-2 text-sm text-gray-600">View and export assessment marks</p>
                </div>
                <a href="{{ route('teacher.assessment') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Classes
                </a>
            </div>
        </div>

        <!-- Assessment Selection -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <label for="assessmentSelect" class="block text-sm font-semibold text-gray-700 mb-3">Select Assessment</label>
            <select id="assessmentSelect" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all appearance-none bg-white" onchange="loadMarks()">
                <option value="">Choose an assessment...</option>
                @foreach($assessments as $assessment)
                    <option value="{{ $assessment->id }}" 
                            data-papers='@json($assessment->papers)'
                            data-subject="{{ $assessment->subject->name ?? 'N/A' }}"
                            data-topic="{{ $assessment->topic }}"
                            data-date="{{ $assessment->date->format('D, d M Y') }}">
                        {{ $assessment->subject->name ?? 'N/A' }} - {{ $assessment->topic }} ({{ $assessment->date->format('M d, Y') }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Marks Display -->
        <div id="marksContainer" class="hidden">
            <!-- Assessment Info -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-200 p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 flex-1">
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
                    <button type="button" onclick="exportToExcel()" class="ml-4 inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 384 512">
                            <path d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm76.45 211.36l-96.42 95.7c-6.65 6.61-17.39 6.61-24.04 0l-96.42-95.7C73.42 337.29 80.54 320 94.82 320H160v-80c0-8.84 7.16-16 16-16h32c8.84 0 16 7.16 16 16v80h65.18c14.28 0 21.4 17.29 11.27 27.36zM377 105L279.1 7c-4.5-4.5-10.6-7-17-7H256v128h128v-6.1c0-6.3-2.5-12.4-7-16.9z"/>
                        </svg>
                        Export to Excel
                    </button>
                </div>
            </div>

            <!-- Marks Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="marksTable">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr id="tableHeaderRow">
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider sticky left-0 bg-gray-50">
                                    Student
                                </th>
                                <!-- Paper columns will be dynamically added here -->
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="marksTableBody">
                            <!-- Marks will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentAssessmentId = null;
        let currentPapers = [];

        function loadMarks() {
            const select = document.getElementById('assessmentSelect');
            const selectedOption = select.options[select.selectedIndex];
            
            if (!selectedOption.value) {
                document.getElementById('marksContainer').classList.add('hidden');
                return;
            }

            currentAssessmentId = selectedOption.value;
            
            try {
                currentPapers = JSON.parse(selectedOption.dataset.papers);
            } catch (e) {
                console.error('Error parsing papers:', e);
                currentPapers = [];
            }

            // Update assessment info
            document.getElementById('assessmentSubject').textContent = selectedOption.dataset.subject;
            document.getElementById('assessmentTopic').textContent = selectedOption.dataset.topic;
            document.getElementById('assessmentDate').textContent = selectedOption.dataset.date;

            // Fetch marks from server
            fetchMarks();
        }

        function fetchMarks() {
            fetch(`/api/assessment/${currentAssessmentId}/marks`)
                .then(response => response.json())
                .then(data => {
                    buildTable(data.marks);
                    document.getElementById('marksContainer').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching marks:', error);
                    buildTable([]);
                    document.getElementById('marksContainer').classList.remove('hidden');
                });
        }

        function buildTable(marks) {
            // Build headers
            const headerRow = document.getElementById('tableHeaderRow');
            while (headerRow.children.length > 1) {
                headerRow.removeChild(headerRow.lastChild);
            }

            const validPapers = currentPapers.filter(paper => 
                paper && paper.name && paper.total_marks && paper.weight
            );

            validPapers.forEach(paper => {
                // Mark column
                const thMark = document.createElement('th');
                thMark.scope = 'col';
                thMark.className = 'px-6 py-4 text-center text-xs font-semibold text-gray-700 border-l border-gray-200';
                thMark.innerHTML = `
                    <div class="space-y-1">
                        <div class="font-bold text-gray-900">${paper.name}</div>
                        <div class="text-gray-600 text-xs">Type: ${paper.name}</div>
                        <div class="font-semibold text-gray-700">Paper 1</div>
                        <div class="text-gray-600">Out of ${paper.weight} marks</div>
                    </div>
                `;
                headerRow.appendChild(thMark);
                
                // Comment column
                const thComment = document.createElement('th');
                thComment.scope = 'col';
                thComment.className = 'px-6 py-4 text-center text-xs font-semibold text-gray-700 border-l border-gray-200';
                thComment.innerHTML = `<div class="font-semibold text-gray-900">Comment</div>`;
                headerRow.appendChild(thComment);
            });

            // Build body
            const tbody = document.getElementById('marksTableBody');
            tbody.innerHTML = '';

            if (marks.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="${validPapers.length + 1}" class="px-6 py-12 text-center text-gray-500">
                            No marks found for this assessment
                        </td>
                    </tr>
                `;
                return;
            }

            marks.forEach(studentMark => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 transition-colors';
                
                const nameTd = document.createElement('td');
                nameTd.className = 'px-6 py-4 whitespace-nowrap sticky left-0 bg-white';
                nameTd.innerHTML = `
                    <div class="text-sm font-semibold text-gray-900">${studentMark.student_name}</div>
                `;
                tr.appendChild(nameTd);

                validPapers.forEach((paper, index) => {
                    const paperMark = studentMark.papers[index];
                    
                    // Mark column
                    const tdMark = document.createElement('td');
                    tdMark.className = 'px-6 py-4 border-l border-gray-200 text-center';
                    tdMark.innerHTML = paperMark ? `<div class="text-sm font-bold text-gray-900">${paperMark.mark}</div>` : `<div class="text-sm text-gray-400">-</div>`;
                    tr.appendChild(tdMark);
                    
                    // Comment column
                    const tdComment = document.createElement('td');
                    tdComment.className = 'px-6 py-4 border-l border-gray-200';
                    tdComment.innerHTML = paperMark ? `<div class="text-xs text-gray-600">${paperMark.comment || ''}</div>` : `<div class="text-sm text-gray-400">-</div>`;
                    tr.appendChild(tdComment);
                });

                tbody.appendChild(tr);
            });
        }

        function exportToExcel() {
            if (!currentAssessmentId) {
                alert('Please select an assessment first.');
                return;
            }

            window.location.href = `{{ route('teacher.assessment.marking.export', $class->id) }}?assessment_id=${currentAssessmentId}`;
        }
    </script>
@endsection
