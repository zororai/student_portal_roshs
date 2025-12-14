@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">View Student Results</h1>
    </div>

    <!-- Class Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="classCards">
        @foreach($classes as $class)
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-3">
                <h3 class="text-lg font-bold text-white">{{ $class->class_name }}</h3>
            </div>
            <div class="p-4">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-gray-600">Students:</span>
                    <span class="text-2xl font-bold text-blue-600">{{ $class->students_count }}</span>
                </div>
                <button onclick="openResultsModal({{ $class->id }}, '{{ $class->class_name }}')" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    View Results
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
                <button type="button" onclick="loadResults()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    View Results
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
                <span id="resultsClassName"></span> - Results
                <span id="resultsTermYear" class="text-sm font-normal text-gray-500"></span>
            </h3>
            <button onclick="closeResultsModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="hidden text-center py-8">
            <svg class="animate-spin h-10 w-10 text-blue-600 mx-auto" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="mt-2 text-gray-500">Loading results...</p>
        </div>

        <!-- Results Table Container -->
        <div id="resultsTableContainer" class="overflow-x-auto">
            <!-- Results will be loaded here -->
        </div>

        <!-- No Results Message -->
        <div id="noResultsMessage" class="hidden text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="mt-4 text-gray-500">No results found for this class, year, and term.</p>
        </div>

        <div class="flex justify-end mt-4">
            <button onclick="closeResultsModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                Close
            </button>
        </div>
    </div>
</div>

<script>
let currentClassId = null;
let currentClassName = '';

function openResultsModal(classId, className) {
    currentClassId = classId;
    currentClassName = className;
    document.getElementById('modalClassName').textContent = className;
    document.getElementById('selectedClassId').value = classId;
    document.getElementById('selectYear').value = '';
    document.getElementById('selectTerm').value = '';
    document.getElementById('selectTermModal').classList.remove('hidden');
}

function closeSelectTermModal() {
    document.getElementById('selectTermModal').classList.add('hidden');
}

function closeResultsModal() {
    document.getElementById('resultsModal').classList.add('hidden');
}

function loadResults() {
    const classId = document.getElementById('selectedClassId').value;
    const year = document.getElementById('selectYear').value;
    const term = document.getElementById('selectTerm').value;

    if (!year || !term) {
        alert('Please select both year and term');
        return;
    }

    // Close term selection modal
    closeSelectTermModal();

    // Show results modal with loading
    document.getElementById('resultsClassName').textContent = currentClassName;
    document.getElementById('resultsTermYear').textContent = `(${term.charAt(0).toUpperCase() + term.slice(1)} Term ${year})`;
    document.getElementById('resultsModal').classList.remove('hidden');
    document.getElementById('loadingSpinner').classList.remove('hidden');
    document.getElementById('resultsTableContainer').innerHTML = '';
    document.getElementById('noResultsMessage').classList.add('hidden');

    // Fetch results
    fetch('{{ route("admin.get-results") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            class_id: classId,
            year: year,
            term: term
        })
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loadingSpinner').classList.add('hidden');

        if (Object.keys(data.results).length === 0) {
            document.getElementById('noResultsMessage').classList.remove('hidden');
            return;
        }

        // Build results table
        let html = `
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subjects & Marks</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Average</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
        `;

        let index = 1;
        for (const studentId in data.results) {
            const studentResults = data.results[studentId];
            const student = studentResults[0].student;
            
            let subjectsHtml = '<div class="flex flex-wrap gap-2">';
            let total = 0;
            let count = 0;

            studentResults.forEach(result => {
                const score = parseFloat(result.score) || 0;
                total += score;
                count++;
                
                let badgeColor = 'bg-green-100 text-green-800';
                if (score < 50) badgeColor = 'bg-red-100 text-red-800';
                else if (score < 70) badgeColor = 'bg-yellow-100 text-yellow-800';

                subjectsHtml += `
                    <span class="px-2 py-1 text-xs rounded-full ${badgeColor}">
                        ${result.subject ? result.subject.subject_name : 'N/A'}: ${score}%
                    </span>
                `;
            });
            subjectsHtml += '</div>';

            const average = count > 0 ? (total / count).toFixed(1) : 0;
            
            let avgColor = 'text-green-600';
            if (average < 50) avgColor = 'text-red-600';
            else if (average < 70) avgColor = 'text-yellow-600';

            html += `
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-900">${index++}</td>
                    <td class="px-4 py-3 text-sm font-medium text-gray-900">${student && student.user ? student.user.name : 'Unknown'}</td>
                    <td class="px-4 py-3">${subjectsHtml}</td>
                    <td class="px-4 py-3 text-sm text-center font-semibold text-gray-900">${total.toFixed(0)}</td>
                    <td class="px-4 py-3 text-sm text-center font-bold ${avgColor}">${average}%</td>
                </tr>
            `;
        }

        html += '</tbody></table>';
        document.getElementById('resultsTableContainer').innerHTML = html;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loadingSpinner').classList.add('hidden');
        document.getElementById('resultsTableContainer').innerHTML = '<p class="text-red-500 text-center py-4">Error loading results. Please try again.</p>';
    });
}

// Close modals when clicking outside
window.onclick = function(event) {
    const selectModal = document.getElementById('selectTermModal');
    const resultsModal = document.getElementById('resultsModal');
    
    if (event.target == selectModal) {
        closeSelectTermModal();
    }
    if (event.target == resultsModal) {
        closeResultsModal();
    }
}
</script>
@endsection
