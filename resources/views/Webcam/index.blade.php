@extends('layouts.app')

@section('title', 'Student ID Generator')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                </div>
                Student ID Generator
            </h1>
            <p class="text-gray-500 mt-1 ml-13">Capture photo and generate student ID card</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Panel - Student Selection & Webcam -->
        <div class="space-y-6">
            <!-- Student Selection -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Select Student</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                        <select id="class-select" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                        <select id="student-select" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500" disabled>
                            <option value="">Select Student</option>
                        </select>
                    </div>
                </div>

                <!-- Student Info Card -->
                <div id="student-info" class="hidden bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-100">
                    <div class="flex items-center gap-4">
                        <div id="student-photo-container" class="w-16 h-16 bg-gray-200 rounded-full overflow-hidden flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 id="student-name" class="font-semibold text-gray-800">-</h3>
                            <p id="student-class" class="text-sm text-gray-500">-</p>
                            <p id="student-roll" class="text-sm text-gray-500">Roll: -</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Webcam Section -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Capture Photo</h2>
                
                <div class="relative">
                    <video id="video" class="w-full rounded-xl bg-gray-900" autoplay playsinline></video>
                    <canvas id="canvas" class="hidden"></canvas>
                </div>

                <div class="flex gap-3 mt-4">
                    <button id="start-camera" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Start Camera
                    </button>
                    <button id="snap" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center gap-2" disabled>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Capture Photo
                    </button>
                </div>

                <!-- Captured Photo Preview -->
                <div id="preview-container" class="hidden mt-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Captured Photo</h3>
                    <div class="flex items-start gap-4">
                        <img id="preview-image" class="w-32 h-32 object-cover rounded-xl border-2 border-blue-500" alt="Captured">
                        <div class="space-y-2">
                            <button id="save-photo" class="w-full px-4 py-2 bg-emerald-600 text-white rounded-xl font-semibold hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Save Photo
                            </button>
                            <button id="retake-photo" class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-colors">
                                Retake
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - ID Card Preview -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">ID Card Preview</h2>
                <button id="generate-id" class="px-4 py-2 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-colors flex items-center gap-2" disabled>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download ID
                </button>
            </div>

            <!-- ID Card -->
            <div id="id-card" class="mx-auto" style="width: 340px;">
                <div class="bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-700 rounded-2xl overflow-hidden shadow-xl">
                    <!-- Header -->
                    <div class="bg-white/10 backdrop-blur-sm px-4 py-3 text-center border-b border-white/20">
                        <h3 class="text-white font-bold text-lg">ROSHS</h3>
                        <p class="text-white/80 text-xs">Student Identification Card</p>
                    </div>

                    <!-- Body -->
                    <div class="p-5">
                        <div class="flex gap-4">
                            <!-- Photo -->
                            <div class="flex-shrink-0">
                                <div id="id-photo" class="w-24 h-28 bg-white rounded-lg overflow-hidden flex items-center justify-center border-2 border-white/50">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 text-white space-y-2">
                                <div>
                                    <p class="text-xs text-white/60 uppercase tracking-wide">Name</p>
                                    <p id="id-name" class="font-semibold text-sm">Select a student</p>
                                </div>
                                <div>
                                    <p class="text-xs text-white/60 uppercase tracking-wide">Class</p>
                                    <p id="id-class" class="font-medium text-sm">-</p>
                                </div>
                                <div>
                                    <p class="text-xs text-white/60 uppercase tracking-wide">Roll No.</p>
                                    <p id="id-roll" class="font-medium text-sm">-</p>
                                </div>
                            </div>
                        </div>

                        <!-- ID Number -->
                        <div class="mt-4 bg-white/10 rounded-lg p-3 text-center">
                            <p class="text-xs text-white/60 uppercase tracking-wide">Student ID</p>
                            <p id="id-number" class="font-mono font-bold text-white text-lg">ROSHS-0000</p>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-white/10 backdrop-blur-sm px-4 py-2 text-center border-t border-white/20">
                        <p class="text-white/70 text-xs">Valid for Academic Year {{ date('Y') }}-{{ date('Y') + 1 }}</p>
                    </div>
                </div>
            </div>

            <!-- Print Button -->
            <div class="mt-6 flex gap-3 justify-center">
                <button id="print-id" class="px-6 py-2 bg-gray-800 text-white rounded-xl font-semibold hover:bg-gray-900 transition-colors flex items-center gap-2" disabled>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print ID Card
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const context = canvas.getContext('2d');
    const classSelect = document.getElementById('class-select');
    const studentSelect = document.getElementById('student-select');
    const startCameraBtn = document.getElementById('start-camera');
    const snapBtn = document.getElementById('snap');
    const savePhotoBtn = document.getElementById('save-photo');
    const retakeBtn = document.getElementById('retake-photo');
    const generateIdBtn = document.getElementById('generate-id');
    const printIdBtn = document.getElementById('print-id');
    const previewContainer = document.getElementById('preview-container');
    const previewImage = document.getElementById('preview-image');
    const studentInfo = document.getElementById('student-info');
    
    let stream = null;
    let selectedStudent = null;
    let capturedImage = null;

    // Class selection change
    classSelect.addEventListener('change', function() {
        const classId = this.value;
        studentSelect.innerHTML = '<option value="">Select Student</option>';
        studentSelect.disabled = true;
        
        if (classId) {
            fetch(`/webcam/students/${classId}`)
                .then(response => response.json())
                .then(students => {
                    students.forEach(student => {
                        const option = document.createElement('option');
                        option.value = student.id;
                        option.textContent = `${student.name} (${student.roll_number || 'No Roll'})`;
                        studentSelect.appendChild(option);
                    });
                    studentSelect.disabled = false;
                })
                .catch(error => console.error('Error fetching students:', error));
        }
    });

    // Student selection change
    studentSelect.addEventListener('change', function() {
        const studentId = this.value;
        
        if (studentId) {
            fetch(`/webcam/student/${studentId}`)
                .then(response => response.json())
                .then(student => {
                    selectedStudent = student;
                    
                    // Update student info card
                    document.getElementById('student-name').textContent = student.name;
                    document.getElementById('student-class').textContent = student.class_name;
                    document.getElementById('student-roll').textContent = `Roll: ${student.roll_number || 'N/A'}`;
                    studentInfo.classList.remove('hidden');
                    
                    // Update ID card preview
                    document.getElementById('id-name').textContent = student.name;
                    document.getElementById('id-class').textContent = student.class_name;
                    document.getElementById('id-roll').textContent = student.roll_number || 'N/A';
                    document.getElementById('id-number').textContent = `ROSHS-${String(student.id).padStart(4, '0')}`;
                    
                    // Update photo if exists
                    if (student.photo) {
                        const photoUrl = '/' + student.photo;
                        document.getElementById('id-photo').innerHTML = `<img src="${photoUrl}" class="w-full h-full object-cover" alt="Student Photo">`;
                        document.getElementById('student-photo-container').innerHTML = `<img src="${photoUrl}" class="w-full h-full object-cover" alt="Student Photo">`;
                    }
                    
                    generateIdBtn.disabled = false;
                    printIdBtn.disabled = false;
                })
                .catch(error => console.error('Error fetching student:', error));
        } else {
            studentInfo.classList.add('hidden');
            generateIdBtn.disabled = true;
            printIdBtn.disabled = true;
        }
    });

    // Start camera
    startCameraBtn.addEventListener('click', function() {
        navigator.mediaDevices.getUserMedia({ 
            video: { 
                width: { ideal: 640 },
                height: { ideal: 480 },
                facingMode: 'user'
            } 
        })
        .then(s => {
            stream = s;
            video.srcObject = stream;
            snapBtn.disabled = false;
            startCameraBtn.textContent = 'Camera Active';
            startCameraBtn.classList.add('bg-green-700');
        })
        .catch(error => {
            console.error('Error accessing webcam:', error);
            alert('Unable to access camera. Please ensure you have granted permission.');
        });
    });

    // Capture photo
    snapBtn.addEventListener('click', function() {
        if (!selectedStudent) {
            alert('Please select a student first');
            return;
        }
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0);
        
        capturedImage = canvas.toDataURL('image/png');
        previewImage.src = capturedImage;
        previewContainer.classList.remove('hidden');
    });

    // Retake photo
    retakeBtn.addEventListener('click', function() {
        previewContainer.classList.add('hidden');
        capturedImage = null;
    });

    // Save photo
    savePhotoBtn.addEventListener('click', function() {
        if (!selectedStudent || !capturedImage) {
            alert('Please select a student and capture a photo');
            return;
        }

        fetch('/webcam/capture', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                student_id: selectedStudent.id,
                image: capturedImage
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Photo saved successfully!');
                
                // Update ID card photo
                document.getElementById('id-photo').innerHTML = `<img src="${data.photo_url}" class="w-full h-full object-cover" alt="Student Photo">`;
                document.getElementById('student-photo-container').innerHTML = `<img src="${data.photo_url}" class="w-full h-full object-cover" alt="Student Photo">`;
                
                previewContainer.classList.add('hidden');
            } else {
                alert('Error saving photo: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error saving photo:', error);
            alert('Error saving photo. Please try again.');
        });
    });

    // Generate/Download ID
    generateIdBtn.addEventListener('click', function() {
        if (selectedStudent) {
            window.open(`/webcam/id-card/${selectedStudent.id}`, '_blank');
        }
    });

    // Print ID Card
    printIdBtn.addEventListener('click', function() {
        const idCard = document.getElementById('id-card');
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Student ID Card</title>
                <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
                <style>
                    @media print {
                        body { margin: 0; padding: 20px; }
                        .id-card { page-break-inside: avoid; }
                    }
                </style>
            </head>
            <body>
                ${idCard.outerHTML}
                <script>window.onload = function() { window.print(); }<\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    });
});
</script>
@endsection
