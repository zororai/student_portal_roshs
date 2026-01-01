<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Parent Registration - RSH School</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-purple-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">Welcome to RSH School!</h1>
                <p class="text-lg text-gray-600">Complete Your Parent Registration</p>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle mr-3 mt-1"></i>
                        <div>
                            <p class="font-semibold">Please fix the following errors:</p>
                            <ul class="list-disc list-inside mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Student Information Card -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user-graduate text-blue-500 mr-2"></i>
                    Your Child's Information
                </h2>
                <div class="grid md:grid-cols-2 gap-4">
                    @foreach($parent->students as $student)
                        <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-500">
                            <div class="flex items-center space-x-3">
                                <img src="{{ asset('images/profile/' . $student->user->profile_picture) }}"
                                     class="w-16 h-16 rounded-full object-cover border-2 border-blue-300"
                                     alt="{{ $student->user->name }}">
                                <div>
                                    <p class="font-bold text-gray-800">{{ $student->user->name }}</p>
                                    <p class="text-sm text-gray-600">Roll Number: <span class="font-semibold text-blue-600">{{ $student->roll_number }}</span></p>
                                    @if($student->class)
                                        <p class="text-sm text-gray-600">Class: {{ $student->class->name }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Registration Form -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-clipboard-check text-green-500 mr-2"></i>
                    Complete Your Profile
                </h2>

                <form action="{{ route('parent.register.complete', $parent->registration_token) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Full Name</label>
                        <input type="text" value="{{ $parent->user->name }}" readonly
                               class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">This name was provided during student registration</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input name="email" type="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400"
                               placeholder="parent@example.com">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <input name="password" type="password" required
                                   class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400"
                                   placeholder="Create a strong password">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Confirm Password <span class="text-red-500">*</span>
                            </label>
                            <input name="password_confirmation" type="password" required
                                   class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400"
                                   placeholder="Confirm your password">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Phone Number
                        </label>
                        <input type="text" value="{{ $parent->phone }}" readonly
                               class="w-full px-4 py-3 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">Contact admin to update your phone number</p>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-6 mt-3">
                            <label class="flex items-center cursor-pointer group">
                                <input name="gender" class="w-5 h-5 text-blue-500 border-gray-300 focus:ring-2 focus:ring-blue-500"
                                       type="radio" value="male" {{ old('gender') == 'male' ? 'checked' : '' }} required>
                                <span class="ml-2 text-gray-700 group-hover:text-blue-600 font-medium">Male</span>
                            </label>
                            <label class="flex items-center cursor-pointer group">
                                <input name="gender" class="w-5 h-5 text-blue-500 border-gray-300 focus:ring-2 focus:ring-blue-500"
                                       type="radio" value="female" {{ old('gender') == 'female' ? 'checked' : '' }} required>
                                <span class="ml-2 text-gray-700 group-hover:text-blue-600 font-medium">Female</span>
                            </label>
                            <label class="flex items-center cursor-pointer group">
                                <input name="gender" class="w-5 h-5 text-blue-500 border-gray-300 focus:ring-2 focus:ring-blue-500"
                                       type="radio" value="other" {{ old('gender') == 'other' ? 'checked' : '' }} required>
                                <span class="ml-2 text-gray-700 group-hover:text-blue-600 font-medium">Other</span>
                            </label>
                        </div>
                        @error('gender')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Current Address <span class="text-red-500">*</span>
                        </label>
                        <textarea name="current_address" rows="3" required
                                  class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400"
                                  placeholder="Enter your current residential address">{{ old('current_address') }}</textarea>
                        @error('current_address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Permanent Address <span class="text-red-500">*</span>
                        </label>
                        <textarea name="permanent_address" rows="3" required
                                  class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 hover:border-gray-400"
                                  placeholder="Enter your permanent address">{{ old('permanent_address') }}</textarea>
                        @error('permanent_address')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Profile Picture</label>
                        <div class="flex items-center space-x-4">
                            <!-- Image Preview -->
                            <div id="imagePreviewContainer" class="hidden">
                                <img id="imagePreview" src="" alt="Profile Preview" class="w-24 h-24 rounded-full object-cover border-4 border-blue-500 shadow-lg">
                            </div>
                            <label id="uploadLabel" class="flex items-center justify-center px-6 py-4 bg-white border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-500 hover:bg-blue-50 transition duration-200 flex-1">
                                <svg id="uploadIcon" class="w-8 h-8 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span id="uploadText" class="text-gray-600 font-medium">Choose your profile picture</span>
                                <input name="profile_picture" id="profilePictureInput" class="hidden" type="file" accept="image/*" onchange="previewImage(this)">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Supported formats: JPG, PNG, GIF (Max 2MB)</p>
                        @error('profile_picture')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="/" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-bold rounded-lg transition duration-200">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-bold rounded-lg shadow-md hover:shadow-lg transition duration-200 flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            Complete Registration
                        </button>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="text-center mt-8 text-gray-600">
                <p class="text-sm">© {{ date('Y') }} RSH School. All rights reserved.</p>
            </div>
        </div>
    </div>
    <script>
        function previewImage(input) {
            const previewContainer = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('imagePreview');
            const uploadText = document.getElementById('uploadText');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.classList.remove('hidden');
                    uploadText.textContent = input.files[0].name;
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.classList.add('hidden');
                uploadText.textContent = 'Choose your profile picture';
            }
        }
    </script>
</body>
</html>
