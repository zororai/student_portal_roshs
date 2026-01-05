@extends('layouts.app')

@section('content')
    <div class="roles">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-gray-700 uppercase font-bold">Add New Student with Parents</h2>
            </div>
            <div class="flex flex-wrap items-center">
                <a href="{{ auth()->user()->hasRole('Admin') ? route('student.index') : route('teacher.class-students') }}" class="bg-gray-700 text-white text-sm uppercase py-2 px-4 flex items-center rounded">
                    <svg class="w-3 h-3 fill-current" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="long-arrow-alt-left" class="svg-inline--fa fa-long-arrow-alt-left fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z"></path></svg>
                    <span class="ml-2 text-xs font-medium">Back</span>
                </a>
            </div>
        </div>

        <!-- Stepper -->
        <div class="bg-white rounded-lg shadow-lg p-4 mb-4">
            <div class="flex items-center justify-between mb-4">
                <div class="flex-1 flex items-center step-indicator" data-step="1">
                    <div class="step-circle active flex items-center justify-center w-8 h-8 rounded-full bg-blue-500 text-white text-sm font-bold">
                        1
                    </div>
                    <div class="ml-2">
                        <div class="step-title text-sm font-medium text-gray-700">Student Information</div>
                    </div>
                </div>
                <div class="flex-shrink-0 w-12 h-1 bg-gray-300 step-line"></div>
                <div class="flex-1 flex items-center step-indicator" data-step="2">
                    <div class="step-circle flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-white text-sm font-bold">
                        2
                    </div>
                    <div class="ml-2">
                        <div class="step-title text-sm font-medium text-gray-500">Parent Information</div>
                    </div>
                </div>
                <div class="flex-shrink-0 w-12 h-1 bg-gray-300 step-line"></div>
                <div class="flex-1 flex items-center step-indicator" data-step="3">
                    <div class="step-circle flex items-center justify-center w-8 h-8 rounded-full bg-gray-300 text-white text-sm font-bold">
                        3
                    </div>
                    <div class="ml-2">
                        <div class="step-title text-sm font-medium text-gray-500">Review & Submit</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error/Success Messages -->
        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-3 rounded text-sm" role="alert">
                <strong class="font-bold">Error:</strong> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-3 rounded text-sm" role="alert">
                <strong class="font-bold">Success:</strong> {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-3 rounded text-sm" role="alert">
                <strong class="font-bold">Errors:</strong>
                <ul class="mt-1 ml-4 list-disc">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-lg">
            <form action="{{ url('/student-with-parents') }}" method="POST" id="studentParentForm" enctype="multipart/form-data">
                @csrf

                <!-- Step 1: Student Information -->
                <div class="step-content px-4 py-4" id="step-1">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                        </svg>
                        Student Information
                    </h3>

                    <div class="max-w-2xl">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                            <input name="student_name" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" type="text" value="{{ old('student_name') }}" placeholder="Enter student's full name">
                            @error('student_name')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address (Auto-generated) *</label>
                            <input name="student_email" id="student_email" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" type="email" value="{{ strtolower($nextRollNumber) }}@roshs.co.zw" readonly placeholder="Auto-generated from roll number">
                            <p class="text-xs text-gray-500 mt-1">Email: {{ $nextRollNumber }}@roshs.co.zw | Default Password: 12345678 (must be changed on first login)</p>
                            @error('student_email')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Roll Number</label>
                                <div class="w-full px-5 py-4 bg-gradient-to-r from-green-50 to-blue-50 border-2 border-green-400 rounded-lg shadow-md">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-gray-600">
                                            <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="text-sm font-medium">Auto-generated:</span>
                                        </div>
                                        <span class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-blue-600">{{ $nextRollNumber }}</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">This roll number will be assigned to the new student</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number (Optional)</label>
                                <input name="student_phone" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400" type="text" value="{{ old('student_phone') }}" placeholder="(123) 456-7890">
                                @error('student_phone')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Gender *</label>
                            <div class="flex gap-3">
                                <label class="flex items-center cursor-pointer group">
                                    <input name="student_gender" class="w-5 h-5 text-blue-500 border-gray-300 focus:ring-2 focus:ring-blue-500" type="radio" value="male">
                                    <span class="ml-3 text-gray-700 group-hover:text-blue-600 font-medium">Male</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input name="student_gender" class="w-5 h-5 text-blue-500 border-gray-300 focus:ring-2 focus:ring-blue-500" type="radio" value="female">
                                    <span class="ml-3 text-gray-700 group-hover:text-blue-600 font-medium">Female</span>
                                </label>
                            </div>
                            @error('student_gender')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth *</label>
                                <input name="dateofbirth" id="datepicker-student" autocomplete="off" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400" type="text" value="{{ old('dateofbirth') }}" placeholder="YYYY-MM-DD">
                                @error('dateofbirth')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Class *</label>
                                <select name="class_id" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400">
                                    <option value="">Select a class</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>


                    </div>

                    <div class="flex justify-end mt-8">
                        <button type="button" class="next-btn bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                            Next Step →
                        </button>
                    </div>
                </div>

                <!-- Step 2: Parent Information -->
                <div class="step-content hidden px-6 py-8" id="step-2">
                    <h3 class="text-xl font-bold text-gray-800 mb-8 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                        </svg>
                        Parent Information
                    </h3>

                    <div class="max-w-3xl">
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-3">
                            <div class="flex">
                                <svg class="w-6 h-6 text-blue-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-blue-800">Parent Registration via SMS</p>
                                    <p class="text-sm text-blue-700 mt-1">Enter parent details below. Each parent will receive an SMS with a secure link to complete their registration (set password, email, and addresses).</p>
                                </div>
                            </div>
                        </div>

                        <div id="parents-container">
                            <!-- Parent 1 -->
                            <div class="parent-block bg-gradient-to-br from-blue-50 to-white border-2 border-blue-200 rounded-xl p-6 mb-3 shadow-sm hover:shadow-md transition duration-200" data-parent-index="0">
                                <div class="flex justify-between items-center mb-3">
                                    <h4 class="text-lg font-bold text-blue-700 flex items-center">
                                        <span class="bg-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center mr-2 text-sm">1</span>
                                        Parent #1
                                    </h4>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                    <input name="parents[0][name]" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400" type="text" placeholder="Enter parent's full name" required>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number (with country code) *</label>
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-3 py-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-gray-600">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                            </svg>
                                        </span>
                                        <input name="parents[0][phone]" id="parent-phone-0" class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-r-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400" type="text" placeholder="+27123456789" required>
                                        <button type="button" onclick="testSms(0)" class="ml-2 px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg shadow-sm transition duration-200" title="Test SMS">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Include country code (e.g., +27 for South Africa) - Click yellow button to test SMS</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-center mb-3">
                            <button type="button" id="add-parent-btn" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded flex items-center shadow-md hover:shadow-lg transition duration-200">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                                Add Another Parent
                            </button>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="button" class="prev-btn bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                            ← Previous
                        </button>
                        <button type="button" class="next-btn bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded">
                            Next Step →
                        </button>
                    </div>
                </div>

                <!-- Step 3: Review & Submit -->
                <div class="step-content hidden px-6 py-8" id="step-3">
                    <h3 class="text-lg font-bold text-gray-700 mb-3">Review & Submit</h3>

                    <div class="max-w-3xl">
                        <div class="border-2 border-gray-300 rounded-lg p-6 mb-3">
                            <h4 class="text-md font-bold text-gray-600 mb-4">Student Information</h4>
                            <div id="review-student" class="grid grid-cols-2 gap-4 text-sm"></div>
                        </div>

                        <div class="border-2 border-gray-300 rounded-lg p-6 mb-3">
                            <h4 class="text-md font-bold text-gray-600 mb-4">Parents Information</h4>
                            <div id="review-parents"></div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button type="button" class="prev-btn bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
                            ← Previous
                        </button>
                        <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded">
                            Submit All
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function() {
        let currentStep = 1;
        let parentCount = 1;

        // Datepicker
        $("#datepicker-student").datepicker({ dateFormat: 'yy-mm-dd' });

        // Step navigation
        function showStep(step) {
            $('.step-content').addClass('hidden');
            $(`#step-${step}`).removeClass('hidden');

            $('.step-indicator').each(function() {
                const stepNum = $(this).data('step');
                if (stepNum < step) {
                    $(this).find('.step-circle').removeClass('bg-gray-300 bg-blue-500').addClass('bg-green-500');
                    $(this).find('.step-title').removeClass('text-gray-500').addClass('text-green-600');
                } else if (stepNum === step) {
                    $(this).find('.step-circle').removeClass('bg-gray-300 bg-green-500').addClass('bg-blue-500');
                    $(this).find('.step-title').removeClass('text-gray-500 text-green-600').addClass('text-gray-700');
                } else {
                    $(this).find('.step-circle').removeClass('bg-blue-500 bg-green-500').addClass('bg-gray-300');
                    $(this).find('.step-title').removeClass('text-gray-700 text-green-600').addClass('text-gray-500');
                }
            });

            if (step === 3) {
                updateReview();
            }
        }

        $('.next-btn').click(function() {
            // Clear all previous validation errors
            $('.validation-error').remove();

            if (currentStep === 1) {
                // Validate Step 1: Student Information
                let isValid = true;

                // Check student name
                if (!$('input[name="student_name"]').val().trim()) {
                    isValid = false;
                    showError('input[name="student_name"]', 'Student Full Name is required');
                }

                // Student phone is now optional - no validation needed

                // Check student gender
                if (!$('input[name="student_gender"]:checked').val()) {
                    isValid = false;
                    showError('input[name="student_gender"]', 'Please select a gender', true);
                }

                // Check date of birth
                if (!$('input[name="dateofbirth"]').val().trim()) {
                    isValid = false;
                    showError('input[name="dateofbirth"]', 'Date of Birth is required');
                }

                // Check class
                if (!$('select[name="class_id"]').val()) {
                    isValid = false;
                    showError('select[name="class_id"]', 'Please select a class');
                }

                if (!isValid) {
                    // Scroll to first error
                    $('html, body').animate({
                        scrollTop: $('.validation-error:first').offset().top - 100
                    }, 500);
                    return;
                }
            }

            if (currentStep === 2) {
                // Validate Step 2: Parent Information
                let isValid = true;

                $('.parent-block').each(function(index) {
                    const parentIndex = $(this).data('parent-index');
                    const $nameInput = $(`input[name="parents[${parentIndex}][name]"]`);
                    const $phoneInput = $(`input[name="parents[${parentIndex}][phone]"]`);
                    const parentName = $nameInput.val().trim();
                    const parentPhone = $phoneInput.val().trim();

                    if (!parentName) {
                        isValid = false;
                        showParentError($nameInput, `Parent #${index + 1} Name is required`);
                    }

                    if (!parentPhone) {
                        isValid = false;
                        showParentError($phoneInput, `Parent #${index + 1} Phone Number is required`);
                    }
                });

                if (!isValid) {
                    // Scroll to first error
                    $('html, body').animate({
                        scrollTop: $('.validation-error:first').offset().top - 100
                    }, 500);
                    return;
                }
            }

            if (currentStep < 3) {
                currentStep++;
                showStep(currentStep);
            }
        });

        // Function to show validation error
        function showError(selector, message, isRadio = false) {
            const $field = $(selector);
            const errorHtml = `
                <p class="validation-error text-red-500 text-xs mt-1 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    ${message}
                </p>
            `;

            if (isRadio) {
                // For radio buttons, insert after the parent div
                $field.closest('.mb-3').append(errorHtml);
            } else {
                // For other inputs, insert after the field
                $field.after(errorHtml);
            }

            // Add error border
            $field.addClass('border-red-500');
        }

        // Function to show parent validation error
        function showParentError($field, message) {
            const errorHtml = `
                <p class="validation-error text-red-500 text-xs mt-1 flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    ${message}
                </p>
            `;

            $field.closest('.mb-4').append(errorHtml);
            $field.addClass('border-red-500');
        }

        // Clear validation errors when user starts typing
        $(document).on('input change', 'input, select, textarea', function() {
            $(this).removeClass('border-red-500');
            $(this).siblings('.validation-error').remove();
            $(this).closest('.mb-3, .mb-4').find('.validation-error').remove();
        });

        $('.prev-btn').click(function() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });

        // Add parent
        $('#add-parent-btn').click(function() {
            const newParentHtml = `
                <div class="parent-block bg-gradient-to-br from-blue-50 to-white border-2 border-blue-200 rounded-xl p-6 mb-3 shadow-sm hover:shadow-md transition duration-200" data-parent-index="${parentCount}">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="text-lg font-bold text-blue-700 flex items-center">
                            <span class="bg-blue-500 text-white w-8 h-8 rounded-full flex items-center justify-center mr-2 text-sm">${parentCount + 1}</span>
                            Parent #${parentCount + 1}
                        </h4>
                        <button type="button" class="remove-parent-btn bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded text-sm shadow-md hover:shadow-lg transition duration-200">
                            Remove
                        </button>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input name="parents[${parentCount}][name]" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400" type="text" placeholder="Enter parent's full name" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number (with country code) *</label>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-3 py-3 bg-gray-100 border border-r-0 border-gray-300 rounded-l-lg text-gray-600">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                                </svg>
                            </span>
                            <input name="parents[${parentCount}][phone]" class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded-r-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400" type="text" placeholder="+27123456789" required>
                            <button type="button" onclick="testSms(${parentCount})" class="ml-2 px-3 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg shadow-sm transition duration-200" title="Test SMS">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Include country code (e.g., +27 for South Africa) - Click yellow button to test SMS</p>
                    </div>
                </div>
            `;

            $('#parents-container').append(newParentHtml);
            parentCount++;
        });

        // Remove parent
        $(document).on('click', '.remove-parent-btn', function() {
            $(this).closest('.parent-block').remove();
            // Renumber parents
            $('.parent-block').each(function(index) {
                $(this).find('h4').text(`Parent #${index + 1}`);
            });
        });

        // Update review section
        function updateReview() {
            // Student info
            const studentInfo = {
                'Name': $('input[name="student_name"]').val(),
                'Email': $('input[name="student_email"]').val(),
                'Default Password': '12345678 (must change on first login)',
                'Roll Number': '{{ $nextRollNumber }}',
                'Phone': $('input[name="student_phone"]').val(),
                'Gender': $('input[name="student_gender"]:checked').val(),
                'Date of Birth': $('input[name="dateofbirth"]').val(),
                'Class': $('select[name="class_id"] option:selected').text()
            };

            let studentHtml = '';
            for (const [key, value] of Object.entries(studentInfo)) {
                studentHtml += `<div><strong>${key}:</strong> ${value || 'N/A'}</div>`;
            }
            $('#review-student').html(studentHtml);

            // Parents info
            let parentsHtml = '';
            $('.parent-block').each(function(index) {
                const parentIndex = $(this).data('parent-index');
                const parentInfo = {
                    'Name': $(`input[name="parents[${parentIndex}][name]"]`).val(),
                    'Phone': $(`input[name="parents[${parentIndex}][phone]"]`).val()
                };

                parentsHtml += `<div class="mb-4 pb-4 border-b border-gray-200">
                    <h5 class="font-medium mb-2 flex items-center">
                        <span class="bg-blue-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs mr-2">${index + 1}</span>
                        Parent #${index + 1}
                    </h5>
                    <div class="grid grid-cols-2 gap-2 text-sm">`;

                for (const [key, value] of Object.entries(parentInfo)) {
                    parentsHtml += `<div><strong>${key}:</strong> ${value || 'N/A'}</div>`;
                }

                parentsHtml += `</div>
                    <p class="text-xs text-blue-600 mt-2"><i class="fas fa-sms mr-1"></i> SMS will be sent to complete registration</p>
                </div>`;
            });
            $('#review-parents').html(parentsHtml);
        }

        // Form submission handler
        $('#studentParentForm').on('submit', function(e) {
            console.log('=== FORM SUBMISSION DEBUG ===');
            console.log('Form data:');

            // Log all form data
            const formData = new FormData(this);
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }

            console.log('Parents count:', $('.parent-block').length);
            console.log('=== END DEBUG ===');

            // Continue with normal submission
            // e.preventDefault(); // Uncomment to prevent actual submission for testing
        });
    });

    // Test SMS function - opens SMS test page with phone number and auto-submits
    function testSms(parentIndex) {
        const phoneInput = document.querySelector(`input[name="parents[${parentIndex}][phone]"]`);
        if (phoneInput && phoneInput.value) {
            const phone = encodeURIComponent(phoneInput.value);
            window.open(`{{ route('sms-test.index') }}?phone=${phone}&auto=1`, '_blank');
        } else {
            alert('Please enter a phone number first');
        }
    }
</script>
@endpush
