@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Approve Student Results</h1>
        <div class="flex space-x-3">
            <button onclick="approveAllResultsGlobal()" id="approveAllGlobalBtn" 
                class="{{ $pendingCounts->sum('count') > 0 ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-400 cursor-not-allowed' }} text-white font-semibold py-2 px-4 rounded-lg flex items-center"
                {{ $pendingCounts->sum('count') == 0 ? 'disabled' : '' }}>
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Approve All ({{ $pendingCounts->sum('count') }})
            </button>
            <a href="{{ route('admin.view-results') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded-lg">
                View All Results
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 mr-4">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-yellow-600 font-medium">Pending Approval</p>
                    <p class="text-2xl font-bold text-yellow-700">{{ $pendingCounts->sum('count') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-blue-600 font-medium">Classes with Pending</p>
                    <p class="text-2xl font-bold text-blue-700">{{ $pendingCounts->unique('class_id')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-green-600 font-medium">Total Classes</p>
                    <p class="text-2xl font-bold text-green-700">{{ $classes->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Class Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="classCards">
        @foreach($classes as $class)
        @php
            $classPending = $pendingCounts->where('class_id', $class->id)->sum('count');
        @endphp
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow {{ $classPending > 0 ? 'ring-2 ring-yellow-400' : '' }}">
            <div class="bg-gradient-to-r {{ $classPending > 0 ? 'from-yellow-500 to-yellow-600' : 'from-blue-500 to-blue-600' }} px-4 py-3">
                <h3 class="text-lg font-bold text-white">{{ $class->class_name }}</h3>
            </div>
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-gray-600">Students:</span>
                    <span class="text-xl font-bold text-blue-600">{{ $class->students_count }}</span>
                </div>
                @if($classPending > 0)
                <div class="flex items-center justify-between mb-4">
                    <span class="text-yellow-600 font-medium">Pending:</span>
                    <span class="text-xl font-bold text-yellow-600">{{ $classPending }}</span>
                </div>
                @else
                <div class="flex items-center justify-between mb-4">
                    <span class="text-green-600 font-medium">Status:</span>
                    <span class="text-sm font-semibold text-green-600">All Approved</span>
                </div>
                @endif
                <button onclick="openApprovalModal({{ $class->id }}, '{{ $class->class_name }}')" 
                    class="w-full {{ $classPending > 0 ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white font-semibold py-2 px-4 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $classPending > 0 ? 'Review & Approve' : 'View Status' }}
                </button>
            </div>
        </div>
        @endforeach
    </div>

    @if($classes->count() == 0)
    <div class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="mt-4 text-gray-500">No classes found.</p>
    </div>
    @endif
</div>

<!-- Year/Term Selection Modal -->
<div id="selectTermModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">
                <span id="modalClassName"></span> - Select Year & Term
            </h3>
            <button onclick="closeSelectTermModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="space-y-4">
            <input type="hidden" id="selectedClassId">
            
            <!-- Select Year -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Year</label>
                <select id="selectYear" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">-- Select Year --</option>
                    @foreach($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Select Term -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Term</label>
                <select id="selectTerm" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">-- Select Term --</option>
                    <option value="first">First Term</option>
                    <option value="second">Second Term</option>
                    <option value="third">Third Term</option>
                </select>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeSelectTermModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <button type="button" onclick="loadPendingResults()" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700">
                    View Pending Results
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Results Display Modal -->
<div id="resultsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-6xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">
                <span id="resultsClassName"></span> - Pending Results
                <span id="resultsTermYear" class="text-sm font-normal text-gray-500"></span>
            </h3>
            <button onclick="closeResultsModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Pending Count Banner -->
        <div id="pendingBanner" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-yellow-700 font-medium"><span id="pendingCount">0</span> results pending approval</span>
                </div>
                <button onclick="approveAllResults()" id="approveAllBtn" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Approve All
                </button>
            </div>
        </div>

        <!-- Student Cards Grid -->
        <div id="studentCardsContainer" class="max-h-[500px] overflow-y-auto">
            <div id="studentCardsGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <!-- Student cards will be loaded here -->
            </div>
        </div>

        <div id="noResultsMessage" class="hidden text-center py-8">
            <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="mt-4 text-green-600 font-medium">All results for this class/term have been approved!</p>
        </div>

        <div class="flex justify-end space-x-3 mt-4 pt-4 border-t">
            <button type="button" onclick="closeResultsModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Student Results Detail Modal -->
<div id="studentResultsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-900">
                <span id="studentResultsName"></span> - Results
            </h3>
            <button onclick="closeStudentResultsModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Student Results Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marks</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grade</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                    </tr>
                </thead>
                <tbody id="studentResultsTableBody" class="bg-white divide-y divide-gray-200">
                    <!-- Results will be loaded here -->
                </tbody>
            </table>
        </div>

        <div class="flex justify-end space-x-3 mt-4 pt-4 border-t">
            <button type="button" onclick="closeStudentResultsModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                Back to Students
            </button>
        </div>
    </div>
</div>

<!-- Success/Error Toast -->
<div id="toast" class="fixed bottom-4 right-4 hidden z-50">
    <div id="toastContent" class="rounded-lg shadow-lg p-4 flex items-center">
        <span id="toastMessage"></span>
    </div>
</div>

@endsection

@push('scripts')
<script>
    let currentClassId = null;
    let currentYear = null;
    let currentTerm = null;
    let currentResultsData = null;

    function openApprovalModal(classId, className) {
        currentClassId = classId;
        document.getElementById('selectedClassId').value = classId;
        document.getElementById('modalClassName').textContent = className;
        document.getElementById('selectTermModal').classList.remove('hidden');
    }

    function closeSelectTermModal() {
        document.getElementById('selectTermModal').classList.add('hidden');
    }

    function closeResultsModal() {
        document.getElementById('resultsModal').classList.add('hidden');
    }

    function closeStudentResultsModal() {
        document.getElementById('studentResultsModal').classList.add('hidden');
    }

    function loadPendingResults() {
        const classId = document.getElementById('selectedClassId').value;
        const year = document.getElementById('selectYear').value;
        const term = document.getElementById('selectTerm').value;

        if (!year || !term) {
            alert('Please select both year and term.');
            return;
        }

        currentYear = year;
        currentTerm = term;

        // Close term selection modal
        closeSelectTermModal();

        // Show loading state
        document.getElementById('studentCardsGrid').innerHTML = `
            <div class="col-span-full text-center py-8">
                <svg class="animate-spin h-8 w-8 mx-auto text-blue-600" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="mt-2 text-gray-500">Loading students...</p>
            </div>
        `;

        // Show results modal
        document.getElementById('resultsModal').classList.remove('hidden');
        document.getElementById('resultsClassName').textContent = document.getElementById('modalClassName').textContent;
        document.getElementById('resultsTermYear').textContent = `(${term.charAt(0).toUpperCase() + term.slice(1)} Term ${year})`;

        // Fetch pending results
        fetch('{{ route("admin.results.get-pending") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                class_id: classId,
                year: year,
                term: term
            })
        })
        .then(response => response.json())
        .then(data => {
            currentResultsData = data;
            displayStudentCards(data);
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('studentCardsGrid').innerHTML = `
                <div class="col-span-full text-center py-8 text-red-500">
                    Failed to load results. Please try again.
                </div>
            `;
        });
    }

    function displayStudentCards(data) {
        const cardsGrid = document.getElementById('studentCardsGrid');
        const noResultsMessage = document.getElementById('noResultsMessage');
        const pendingBanner = document.getElementById('pendingBanner');
        const pendingCount = document.getElementById('pendingCount');
        const studentCardsContainer = document.getElementById('studentCardsContainer');

        cardsGrid.innerHTML = '';

        if (data.pending_count === 0) {
            noResultsMessage.classList.remove('hidden');
            pendingBanner.classList.add('hidden');
            studentCardsContainer.classList.add('hidden');
            return;
        }

        noResultsMessage.classList.add('hidden');
        pendingBanner.classList.remove('hidden');
        studentCardsContainer.classList.remove('hidden');
        pendingCount.textContent = data.pending_count;

        // Create student cards grouped by student
        Object.entries(data.results).forEach(([studentId, studentResults]) => {
            if (studentResults.length === 0) return;
            
            const student = studentResults[0].student;
            const studentName = student?.user?.name || 'Unknown Student';
            const resultsCount = studentResults.length;
            
            const card = document.createElement('div');
            card.className = 'bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-all cursor-pointer transform hover:scale-105 border-2 border-yellow-300';
            card.onclick = () => showStudentResults(studentId, studentName, studentResults);
            
            card.innerHTML = `
                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-4 py-3">
                    <div class="flex items-center justify-center">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="p-4 text-center">
                    <h4 class="font-semibold text-gray-800 text-sm mb-2">${studentName}</h4>
                    <div class="flex items-center justify-center space-x-1">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            ${resultsCount} subject${resultsCount > 1 ? 's' : ''} pending
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Click to view results</p>
                </div>
            `;
            
            cardsGrid.appendChild(card);
        });
    }

    function showStudentResults(studentId, studentName, results) {
        document.getElementById('studentResultsName').textContent = studentName;
        
        const tableBody = document.getElementById('studentResultsTableBody');
        tableBody.innerHTML = '';
        
        results.forEach(result => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${result.subject?.name || 'N/A'}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-semibold">${result.marks}%</td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${getGradeColor(result.mark_grade)}">${result.mark_grade || 'N/A'}</span>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${result.teacher?.user?.name || 'N/A'}</td>
            `;
            tableBody.appendChild(row);
        });
        
        document.getElementById('studentResultsModal').classList.remove('hidden');
    }

    function getGradeColor(grade) {
        switch(grade) {
            case 'A': return 'bg-green-100 text-green-800';
            case 'B': return 'bg-blue-100 text-blue-800';
            case 'C': return 'bg-yellow-100 text-yellow-800';
            case 'D': return 'bg-orange-100 text-orange-800';
            case 'U': return 'bg-red-100 text-red-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }

    function approveAllResults() {
        if (!confirm('Are you sure you want to approve all pending results for this class/term? Students and parents will be able to view these results.')) {
            return;
        }

        const approveBtn = document.getElementById('approveAllBtn');
        approveBtn.disabled = true;
        approveBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Approving...
        `;

        fetch('{{ route("admin.results.approve") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                class_id: currentClassId,
                year: currentYear,
                term: currentTerm
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Reload results to show updated status
                loadPendingResults();
                // Refresh the page after a short delay to update the cards
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showToast(data.message || 'Failed to approve results.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while approving results.', 'error');
        })
        .finally(() => {
            approveBtn.disabled = false;
            approveBtn.innerHTML = `
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Approve All
            `;
        });
    }

    function approveAllResultsGlobal() {
        if (!confirm('Are you sure you want to approve ALL pending results across ALL classes? This will make all results visible to students and parents.')) {
            return;
        }

        const approveBtn = document.getElementById('approveAllGlobalBtn');
        approveBtn.disabled = true;
        approveBtn.innerHTML = `
            <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Approving...
        `;

        fetch('{{ route("admin.results.approve-all") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast(data.message || 'Failed to approve results.', 'error');
                approveBtn.disabled = false;
                approveBtn.innerHTML = `
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Approve All
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while approving results.', 'error');
            approveBtn.disabled = false;
            approveBtn.innerHTML = `
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Approve All
            `;
        });
    }

    function showToast(message, type) {
        const toast = document.getElementById('toast');
        const toastContent = document.getElementById('toastContent');
        const toastMessage = document.getElementById('toastMessage');

        toastMessage.textContent = message;
        
        if (type === 'success') {
            toastContent.className = 'rounded-lg shadow-lg p-4 flex items-center bg-green-500 text-white';
        } else {
            toastContent.className = 'rounded-lg shadow-lg p-4 flex items-center bg-red-500 text-white';
        }

        toast.classList.remove('hidden');
        
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 5000);
    }
</script>
@endpush
