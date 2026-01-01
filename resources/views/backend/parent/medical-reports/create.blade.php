@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 -m-6 p-6">
    <div class="max-w-3xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center space-x-4">
                <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Add Medical Report</h1>
                    <p class="text-gray-500 mt-1">Report a medical condition for your child</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('parent.medical-reports.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="p-6 space-y-6">
                    <!-- Student Selection -->
                    <div>
                        <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">Select Your Child *</label>
                        @if($students->count() == 1)
                        <div class="flex items-center p-4 bg-blue-50 border border-blue-200 rounded-xl">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-200 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-sm font-bold text-indigo-600">{{ substr($students->first()->user->name ?? 'S', 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $students->first()->user->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-500">{{ $students->first()->class->class_name ?? 'No Class' }}</p>
                            </div>
                            <input type="hidden" name="student_id" value="{{ $students->first()->id }}">
                        </div>
                        @else
                        <select name="student_id" id="student_id" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                            <option value="">-- Select Your Child --</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->user->name ?? 'Unknown' }} ({{ $student->class->class_name ?? 'No Class' }})
                            </option>
                            @endforeach
                        </select>
                        @endif
                        @error('student_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Condition Type -->
                    <div>
                        <label for="condition_type" class="block text-sm font-medium text-gray-700 mb-2">Condition Type *</label>
                        <select name="condition_type" id="condition_type" required class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                            <option value="">-- Select Type --</option>
                            @foreach($conditionTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('condition_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('condition_type')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Condition Name -->
                    <div>
                        <label for="condition_name" class="block text-sm font-medium text-gray-700 mb-2">Condition Name *</label>
                        <input type="text" name="condition_name" id="condition_name" value="{{ old('condition_name') }}" required
                            placeholder="e.g., Asthma, Diabetes, Peanut Allergy"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                        @error('condition_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea name="description" id="description" rows="4" required
                            placeholder="Please describe the condition in detail..."
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all resize-none">{{ old('description') }}</textarea>
                        @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Medications -->
                    <div>
                        <label for="medications" class="block text-sm font-medium text-gray-700 mb-2">Current Medications</label>
                        <textarea name="medications" id="medications" rows="3"
                            placeholder="List any medications the student is taking..."
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all resize-none">{{ old('medications') }}</textarea>
                        @error('medications')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Emergency Instructions -->
                    <div>
                        <label for="emergency_instructions" class="block text-sm font-medium text-gray-700 mb-2">Emergency Instructions</label>
                        <textarea name="emergency_instructions" id="emergency_instructions" rows="3"
                            placeholder="What should be done in case of emergency..."
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all resize-none">{{ old('emergency_instructions') }}</textarea>
                        @error('emergency_instructions')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Diagnosis Date -->
                        <div>
                            <label for="diagnosis_date" class="block text-sm font-medium text-gray-700 mb-2">Diagnosis Date</label>
                            <input type="date" name="diagnosis_date" id="diagnosis_date" value="{{ old('diagnosis_date') }}"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                            @error('diagnosis_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Doctor Name -->
                        <div>
                            <label for="doctor_name" class="block text-sm font-medium text-gray-700 mb-2">Doctor's Name</label>
                            <input type="text" name="doctor_name" id="doctor_name" value="{{ old('doctor_name') }}"
                                placeholder="Dr. John Smith"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                            @error('doctor_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Doctor Contact -->
                    <div>
                        <label for="doctor_contact" class="block text-sm font-medium text-gray-700 mb-2">Doctor's Contact</label>
                        <input type="text" name="doctor_contact" id="doctor_contact" value="{{ old('doctor_contact') }}"
                            placeholder="Phone number or email"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                        @error('doctor_contact')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Attachment -->
                    <div>
                        <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">Attachment (Medical Document)</label>
                        <div id="upload-area" class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-red-400 transition-colors cursor-pointer">
                            <input type="file" name="attachment" id="attachment" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                            <div id="upload-placeholder">
                                <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-gray-600">Click to upload or drag and drop</p>
                                <p class="text-sm text-gray-400 mt-1">PDF, JPG, PNG (Max 5MB)</p>
                            </div>
                            <div id="file-preview" class="hidden">
                                <div class="flex items-center justify-center space-x-3">
                                    <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="text-left">
                                        <p id="file-name" class="font-medium text-gray-800"></p>
                                        <p id="file-size" class="text-sm text-gray-500"></p>
                                    </div>
                                </div>
                                <button type="button" id="remove-file" class="mt-3 text-sm text-red-500 hover:text-red-700">Remove file</button>
                            </div>
                        </div>
                        @error('attachment')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('parent.medical-reports.index') }}" class="px-5 py-2.5 text-gray-600 hover:text-gray-800 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl hover:from-red-600 hover:to-pink-700 transition-all duration-200 shadow-lg">
                        Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('upload-area');
    const fileInput = document.getElementById('attachment');
    const placeholder = document.getElementById('upload-placeholder');
    const preview = document.getElementById('file-preview');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const removeBtn = document.getElementById('remove-file');

    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            placeholder.classList.add('hidden');
            preview.classList.remove('hidden');
            uploadArea.classList.remove('border-gray-200');
            uploadArea.classList.add('border-emerald-400', 'bg-emerald-50');
        }
    });

    removeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        fileInput.value = '';
        placeholder.classList.remove('hidden');
        preview.classList.add('hidden');
        uploadArea.classList.add('border-gray-200');
        uploadArea.classList.remove('border-emerald-400', 'bg-emerald-50');
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endsection
