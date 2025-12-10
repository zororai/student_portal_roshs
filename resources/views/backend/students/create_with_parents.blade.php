@extends('layouts.app')

@section('content')
    <div class="roles">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-gray-700 uppercase font-bold">Add New Student with Parents</h2>
            </div>
            <div class="flex flex-wrap items-center">
                <a href="{{ route('student.index') }}" class="bg-gray-700 text-white text-sm uppercase py-2 px-4 flex items-center rounded">
                    <svg class="w-3 h-3 fill-current" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="long-arrow-alt-left" class="svg-inline--fa fa-long-arrow-alt-left fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z"></path></svg>
                    <span class="ml-2 text-xs font-semibold">Back</span>
                </a>
            </div>
        </div>

        <!-- Stepper -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between mb-8">
                <div class="flex-1 flex items-center step-indicator" data-step="1">
                    <div class="step-circle active flex items-center justify-center w-10 h-10 rounded-full bg-blue-500 text-white font-bold">
                        1
                    </div>
                    <div class="ml-3">
                        <div class="step-title font-semibold text-gray-700">Student Information</div>
                        <div class="step-subtitle text-xs text-gray-500">Basic student details</div>
                    </div>
                </div>
                <div class="flex-shrink-0 w-16 h-1 bg-gray-300 step-line"></div>
                <div class="flex-1 flex items-center step-indicator" data-step="2">
                    <div class="step-circle flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-white font-bold">
                        2
                    </div>
                    <div class="ml-3">
                        <div class="step-title font-semibold text-gray-500">Parent Information</div>
                        <div class="step-subtitle text-xs text-gray-500">Add one or more parents</div>
                    </div>
                </div>
                <div class="flex-shrink-0 w-16 h-1 bg-gray-300 step-line"></div>
                <div class="flex-1 flex items-center step-indicator" data-step="3">
                    <div class="step-circle flex items-center justify-center w-10 h-10 rounded-full bg-gray-300 text-white font-bold">
                        3
                    </div>
                    <div class="ml-3">
                        <div class="step-title font-semibold text-gray-500">Review & Submit</div>
                        <div class="step-subtitle text-xs text-gray-500">Confirm details</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-lg">
            <form action="{{ route('student.store-with-parents') }}" method="POST" id="studentParentForm" enctype="multipart/form-data">
                @csrf

                <!-- Step 1: Student Information -->
                <div class="step-content px-6 py-8" id="step-1">
                    <h3 class="text-xl font-bold text-gray-800 mb-8 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                        </svg>
                        Student Information
                    </h3>

                    <div class="max-w-3xl">
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name *</label>
                            <input name="student_name" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400" type="text" value="{{ old('student_name') }}" placeholder="Enter student's full name">
                            @error('student_name')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address *</label>
                                <input name="student_email" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400" type="email" value="{{ old('student_email') }}" placeholder="student@example.com">
                                @error('student_email')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Password *</label>
                                <input name="student_password" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400" type="password" placeholder="Create a password">
                                @error('student_password')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Roll Number *</label>
                                <input name="roll_number" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400" type="number" value="{{ old('roll_number') }}" placeholder="Enter roll number">
                                @error('roll_number')
                                    <p class="text-red-500 text-xs mt-1 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Phone Number *</label>
                                <input name="student_phone" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400" type="text" value="{{ old('student_phone') }}" placeholder="(123) 456-7890">
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

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Gender *</label>
                            <div class="flex gap-6">
                                <label class="flex items-center cursor-pointer group">
                                    <input name="student_gender" class="w-5 h-5 text-blue-500 border-gray-300 focus:ring-2 focus:ring-blue-500" type="radio" value="male">
                                    <span class="ml-3 text-gray-700 group-hover:text-blue-600 font-medium">Male</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input name="student_gender" class="w-5 h-5 text-blue-500 border-gray-300 focus:ring-2 focus:ring-blue-500" type="radio" value="female">
                                    <span class="ml-3 text-gray-700 group-hover:text-blue-600 font-medium">Female</span>
                                </label>
                                <label class="flex items-center cursor-pointer group">
                                    <input name="student_gender" class="w-5 h-5 text-blue-500 border-gray-300 focus:ring-2 focus:ring-blue-500" type="radio" value="other">
                                    <span class="ml-3 text-gray-700 group-hover:text-blue-600 font-medium">Other</span>
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

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Date of Birth *</label>
                                <input name="dateofbirth" id="datepicker-student" autocomplete="off" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400" type="text" value="{{ old('dateofbirth') }}" placeholder="YYYY-MM-DD">
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
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Class *</label>
                                <select name="class_id" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400">
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

                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Current Address *</label>
                            <textarea name="student_current_address" rows="2" class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400" placeholder="Enter current residential address">{{ old('student_current_address') }}</textarea>
                            @error('student_current_address')
                                <p class="text-red-500 text-xs mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>

                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Permanent Address *</label>
                            </div>
                            <div class="md:w-2/3">
                                <input name="student_permanent_address" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="text" value="{{ old('student_permanent_address') }}">
                                @error('student_permanent_address')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Assign Class *</label>
                            </div>
                            <div class="md:w-2/3">
                                <select name="class_id" class="block appearance-none w-full bg-gray-200 border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                    <option value="">--Select Class--</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="md:flex md:items-center mb-6">
                            <div class="md:w-1/3">
                                <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Picture</label>
                            </div>
                            <div class="md:w-2/3">
                                <input name="student_profile_picture" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="file">
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
                    <h3 class="text-lg font-bold text-gray-700 mb-6">Parent Information</h3>

                    <div class="max-w-2xl">
                        <div id="parents-container">
                            <!-- Parent 1 -->
                            <div class="parent-block border-2 border-gray-300 rounded-lg p-6 mb-6" data-parent-index="0">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="text-md font-bold text-gray-600">Parent #1</h4>
                                </div>

                                <div class="md:flex md:items-center mb-4">
                                    <div class="md:w-1/3">
                                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Name *</label>
                                    </div>
                                    <div class="md:w-2/3">
                                        <input name="parents[0][name]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="text">
                                    </div>
                                </div>

                                <div class="md:flex md:items-center mb-4">
                                    <div class="md:w-1/3">
                                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Email *</label>
                                    </div>
                                    <div class="md:w-2/3">
                                        <input name="parents[0][email]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="email">
                                    </div>
                                </div>

                                <div class="md:flex md:items-center mb-4">
                                    <div class="md:w-1/3">
                                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Password *</label>
                                    </div>
                                    <div class="md:w-2/3">
                                        <input name="parents[0][password]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="password">
                                    </div>
                                </div>

                                <div class="md:flex md:items-center mb-4">
                                    <div class="md:w-1/3">
                                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Phone *</label>
                                    </div>
                                    <div class="md:w-2/3">
                                        <input name="parents[0][phone]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="text">
                                    </div>
                                </div>

                                <div class="md:flex md:items-center mb-4">
                                    <div class="md:w-1/3">
                                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Gender *</label>
                                    </div>
                                    <div class="md:w-2/3">
                                        <div class="flex flex-row items-center">
                                            <label class="block text-gray-500 font-bold">
                                                <input name="parents[0][gender]" class="mr-2 leading-tight" type="radio" value="male">
                                                <span class="text-sm">Male</span>
                                            </label>
                                            <label class="ml-4 block text-gray-500 font-bold">
                                                <input name="parents[0][gender]" class="mr-2 leading-tight" type="radio" value="female">
                                                <span class="text-sm">Female</span>
                                            </label>
                                            <label class="ml-4 block text-gray-500 font-bold">
                                                <input name="parents[0][gender]" class="mr-2 leading-tight" type="radio" value="other">
                                                <span class="text-sm">Other</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="md:flex md:items-center mb-4">
                                    <div class="md:w-1/3">
                                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Current Address *</label>
                                    </div>
                                    <div class="md:w-2/3">
                                        <input name="parents[0][current_address]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="text">
                                    </div>
                                </div>

                                <div class="md:flex md:items-center mb-4">
                                    <div class="md:w-1/3">
                                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Permanent Address *</label>
                                    </div>
                                    <div class="md:w-2/3">
                                        <input name="parents[0][permanent_address]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="text">
                                    </div>
                                </div>

                                <div class="md:flex md:items-center mb-4">
                                    <div class="md:w-1/3">
                                        <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Picture</label>
                                    </div>
                                    <div class="md:w-2/3">
                                        <input name="parents[0][profile_picture]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="file">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-center mb-6">
                            <button type="button" id="add-parent-btn" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-6 rounded flex items-center">
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
                    <h3 class="text-lg font-bold text-gray-700 mb-6">Review & Submit</h3>

                    <div class="max-w-3xl">
                        <div class="border-2 border-gray-300 rounded-lg p-6 mb-6">
                            <h4 class="text-md font-bold text-gray-600 mb-4">Student Information</h4>
                            <div id="review-student" class="grid grid-cols-2 gap-4 text-sm"></div>
                        </div>

                        <div class="border-2 border-gray-300 rounded-lg p-6 mb-6">
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
            if (currentStep < 3) {
                currentStep++;
                showStep(currentStep);
            }
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
                <div class="parent-block border-2 border-gray-300 rounded-lg p-6 mb-6" data-parent-index="${parentCount}">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="text-md font-bold text-gray-600">Parent #${parentCount + 1}</h4>
                        <button type="button" class="remove-parent-btn bg-red-500 hover:bg-red-600 text-white font-bold py-1 px-3 rounded text-sm">
                            Remove
                        </button>
                    </div>

                    <div class="md:flex md:items-center mb-4">
                        <div class="md:w-1/3">
                            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Name *</label>
                        </div>
                        <div class="md:w-2/3">
                            <input name="parents[${parentCount}][name]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="text">
                        </div>
                    </div>

                    <div class="md:flex md:items-center mb-4">
                        <div class="md:w-1/3">
                            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Email *</label>
                        </div>
                        <div class="md:w-2/3">
                            <input name="parents[${parentCount}][email]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="email">
                        </div>
                    </div>

                    <div class="md:flex md:items-center mb-4">
                        <div class="md:w-1/3">
                            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Password *</label>
                        </div>
                        <div class="md:w-2/3">
                            <input name="parents[${parentCount}][password]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="password">
                        </div>
                    </div>

                    <div class="md:flex md:items-center mb-4">
                        <div class="md:w-1/3">
                            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Phone *</label>
                        </div>
                        <div class="md:w-2/3">
                            <input name="parents[${parentCount}][phone]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="text">
                        </div>
                    </div>

                    <div class="md:flex md:items-center mb-4">
                        <div class="md:w-1/3">
                            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Gender *</label>
                        </div>
                        <div class="md:w-2/3">
                            <div class="flex flex-row items-center">
                                <label class="block text-gray-500 font-bold">
                                    <input name="parents[${parentCount}][gender]" class="mr-2 leading-tight" type="radio" value="male">
                                    <span class="text-sm">Male</span>
                                </label>
                                <label class="ml-4 block text-gray-500 font-bold">
                                    <input name="parents[${parentCount}][gender]" class="mr-2 leading-tight" type="radio" value="female">
                                    <span class="text-sm">Female</span>
                                </label>
                                <label class="ml-4 block text-gray-500 font-bold">
                                    <input name="parents[${parentCount}][gender]" class="mr-2 leading-tight" type="radio" value="other">
                                    <span class="text-sm">Other</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="md:flex md:items-center mb-4">
                        <div class="md:w-1/3">
                            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Current Address *</label>
                        </div>
                        <div class="md:w-2/3">
                            <input name="parents[${parentCount}][current_address]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="text">
                        </div>
                    </div>

                    <div class="md:flex md:items-center mb-4">
                        <div class="md:w-1/3">
                            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Permanent Address *</label>
                        </div>
                        <div class="md:w-2/3">
                            <input name="parents[${parentCount}][permanent_address]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="text">
                        </div>
                    </div>

                    <div class="md:flex md:items-center mb-4">
                        <div class="md:w-1/3">
                            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4">Picture</label>
                        </div>
                        <div class="md:w-2/3">
                            <input name="parents[${parentCount}][profile_picture]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" type="file">
                        </div>
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
                'Roll Number': $('input[name="roll_number"]').val(),
                'Phone': $('input[name="student_phone"]').val(),
                'Gender': $('input[name="student_gender"]:checked').val(),
                'Date of Birth': $('input[name="dateofbirth"]').val(),
                'Class': $('select[name="class_id"] option:selected').text(),
                'Current Address': $('input[name="student_current_address"]').val(),
                'Permanent Address': $('input[name="student_permanent_address"]').val()
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
                    'Email': $(`input[name="parents[${parentIndex}][email]"]`).val(),
                    'Phone': $(`input[name="parents[${parentIndex}][phone]"]`).val(),
                    'Gender': $(`input[name="parents[${parentIndex}][gender]"]:checked`).val(),
                    'Current Address': $(`input[name="parents[${parentIndex}][current_address]"]`).val(),
                    'Permanent Address': $(`input[name="parents[${parentIndex}][permanent_address]"]`).val()
                };

                parentsHtml += `<div class="mb-4 pb-4 border-b border-gray-200">
                    <h5 class="font-semibold mb-2">Parent #${index + 1}</h5>
                    <div class="grid grid-cols-2 gap-2 text-sm">`;

                for (const [key, value] of Object.entries(parentInfo)) {
                    parentsHtml += `<div><strong>${key}:</strong> ${value || 'N/A'}</div>`;
                }

                parentsHtml += `</div></div>`;
            });
            $('#review-parents').html(parentsHtml);
        }
    });
</script>
@endpush
