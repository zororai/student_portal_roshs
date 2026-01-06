@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Add Multiple Subjects</h1>
                <p class="mt-2 text-sm text-gray-600">Create multiple subjects for a class with timetable settings</p>
            </div>
            <a href="{{ route('admin.subjects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <ul class="list-disc list-inside text-sm text-red-600">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('admin.subjects.store') }}" method="POST" id="subjectForm">
            @csrf
            
            <!-- Class Selection Section -->
            <div class="px-8 py-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center mb-4">
                    <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </span>
                    Select Class
                </h3>
                <div class="max-w-md">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Class <span class="text-red-500">*</span></label>
                    <select name="class_id" id="classSelect" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Select a Class --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" data-name="{{ $class->class_name }}">{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">All subjects below will be assigned to this class</p>
                </div>
            </div>

            <!-- Subjects Section -->
            <div class="px-8 py-6 border-b border-gray-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <span class="w-8 h-8 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </span>
                        Subjects
                    </h3>
                    <button type="button" onclick="addSubjectRow()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Subject
                    </button>
                </div>
                <p class="text-sm text-gray-500 mb-4">Subject codes are auto-generated: First + Last letter of subject name + First + Last character of class name</p>
            </div>

            <!-- Subject Rows Container -->
            <div id="subjectsContainer" class="divide-y divide-gray-100">
                <!-- Subject rows will be added here -->
            </div>

            <!-- Submit Section -->
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <p class="text-sm text-gray-500"><span id="subjectCount">0</span> subject(s) to create</p>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.subjects.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create All Subjects
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let subjectIndex = 0;
let selectedClassName = '';

document.getElementById('classSelect').addEventListener('change', function() {
    selectedClassName = this.options[this.selectedIndex].dataset.name || '';
    updateAllSubjectCodes();
});

function generateSubjectCode(subjectName) {
    if (!subjectName || !selectedClassName) return '';
    
    const cleanSubject = subjectName.toUpperCase().trim();
    const cleanClass = selectedClassName.toUpperCase().replace(/[^A-Za-z0-9]/g, '');
    
    if (cleanSubject.length < 1 || cleanClass.length < 1) return '';
    
    const firstLetterSubject = cleanSubject.charAt(0);
    const lastLetterSubject = cleanSubject.charAt(cleanSubject.length - 1);
    const firstCharClass = cleanClass.charAt(0);
    const lastCharClass = cleanClass.charAt(cleanClass.length - 1);
    
    return firstLetterSubject + lastLetterSubject + firstCharClass + lastCharClass;
}

function updateSubjectCode(index) {
    const nameInput = document.getElementById(`subject_name_${index}`);
    const codeInput = document.getElementById(`subject_code_${index}`);
    const codeDisplay = document.getElementById(`subject_code_display_${index}`);
    
    if (nameInput && codeInput && codeDisplay) {
        const code = generateSubjectCode(nameInput.value);
        codeInput.value = code;
        codeDisplay.textContent = code || '----';
    }
}

function updateAllSubjectCodes() {
    document.querySelectorAll('[id^="subject_name_"]').forEach(input => {
        const index = input.id.replace('subject_name_', '');
        updateSubjectCode(index);
    });
}

function calculateRowTotal(index) {
    const singles = parseInt(document.getElementById(`single_${index}`).value) || 0;
    const doubles = parseInt(document.getElementById(`double_${index}`).value) || 0;
    const triples = parseInt(document.getElementById(`triple_${index}`).value) || 0;
    const quads = parseInt(document.getElementById(`quad_${index}`).value) || 0;
    
    const total = (singles * 1) + (doubles * 2) + (triples * 3) + (quads * 4);
    document.getElementById(`total_${index}`).textContent = total;
}

function removeSubjectRow(index) {
    const row = document.getElementById(`subject_row_${index}`);
    if (row) {
        row.remove();
        updateSubjectCount();
    }
}

function updateSubjectCount() {
    const count = document.querySelectorAll('[id^="subject_row_"]').length;
    document.getElementById('subjectCount').textContent = count;
}

function addSubjectRow() {
    const container = document.getElementById('subjectsContainer');
    const index = subjectIndex++;
    
    const rowHtml = `
        <div id="subject_row_${index}" class="px-8 py-6 bg-white hover:bg-gray-50 transition-colors">
            <div class="flex items-start justify-between mb-4">
                <h4 class="text-md font-semibold text-gray-800 flex items-center">
                    <span class="w-6 h-6 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mr-2 text-xs font-bold">${index + 1}</span>
                    Subject Details
                </h4>
                <button type="button" onclick="removeSubjectRow(${index})" class="text-red-500 hover:text-red-700 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
            
            <!-- Subject Name & Code -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject Name <span class="text-red-500">*</span></label>
                    <input type="text" id="subject_name_${index}" name="subjects[${index}][name]" required
                        oninput="updateSubjectCode(${index})"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="e.g. Mathematics">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject Code</label>
                    <div class="px-4 py-2 bg-gradient-to-r from-green-50 to-blue-50 border-2 border-green-300 rounded-lg text-center">
                        <span id="subject_code_display_${index}" class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-blue-600">----</span>
                    </div>
                    <input type="hidden" id="subject_code_${index}" name="subjects[${index}][subject_code]" value="">
                </div>
            </div>
            
            <!-- Timetable Settings -->
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-sm font-medium text-gray-700 mb-3">Timetable Settings (Lessons per week)</p>
                <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-100">
                        <label class="block text-xs font-medium text-blue-800 mb-1">Single (1)</label>
                        <input type="number" id="single_${index}" name="subjects[${index}][single_lessons_per_week]" value="0" min="0" max="20"
                            onchange="calculateRowTotal(${index})" oninput="calculateRowTotal(${index})"
                            class="w-full px-2 py-1 border border-blue-200 rounded text-center text-sm font-semibold">
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 border border-green-100">
                        <label class="block text-xs font-medium text-green-800 mb-1">Double (2)</label>
                        <input type="number" id="double_${index}" name="subjects[${index}][double_lessons_per_week]" value="0" min="0" max="10"
                            onchange="calculateRowTotal(${index})" oninput="calculateRowTotal(${index})"
                            class="w-full px-2 py-1 border border-green-200 rounded text-center text-sm font-semibold">
                    </div>
                    <div class="bg-amber-50 rounded-lg p-3 border border-amber-100">
                        <label class="block text-xs font-medium text-amber-800 mb-1">Triple (3)</label>
                        <input type="number" id="triple_${index}" name="subjects[${index}][triple_lessons_per_week]" value="0" min="0" max="5"
                            onchange="calculateRowTotal(${index})" oninput="calculateRowTotal(${index})"
                            class="w-full px-2 py-1 border border-amber-200 rounded text-center text-sm font-semibold">
                    </div>
                    <div class="bg-red-50 rounded-lg p-3 border border-red-100">
                        <label class="block text-xs font-medium text-red-800 mb-1">Quad (4)</label>
                        <input type="number" id="quad_${index}" name="subjects[${index}][quad_lessons_per_week]" value="0" min="0" max="5"
                            onchange="calculateRowTotal(${index})" oninput="calculateRowTotal(${index})"
                            class="w-full px-2 py-1 border border-red-200 rounded text-center text-sm font-semibold">
                    </div>
                    <div class="bg-indigo-100 rounded-lg p-3 border border-indigo-200">
                        <label class="block text-xs font-medium text-indigo-800 mb-1">Total</label>
                        <div class="text-center">
                            <span id="total_${index}" class="text-lg font-bold text-indigo-600">0</span>
                            <span class="text-xs text-indigo-500">periods</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', rowHtml);
    updateSubjectCount();
    
    // Update subject code if class is already selected
    if (selectedClassName) {
        updateSubjectCode(index);
    }
}

// Add first subject row on page load
document.addEventListener('DOMContentLoaded', function() {
    addSubjectRow();
});
</script>
@endsection