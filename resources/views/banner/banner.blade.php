@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Banner Management</h1>
                <p class="mt-2 text-sm text-gray-600">Manage homepage banner images for the website</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Error Alert -->
    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm text-red-700 font-medium">Please fix the following errors:</p>
                    <ul class="mt-1 text-sm text-red-600 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Banner Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('banner.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Current Banners Section -->
            <div class="px-8 py-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    Current Banner Images
                </h3>
            </div>

            <div class="px-8 py-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @if(isset($banner))
                        <!-- Banner 1 -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-2 text-xs font-bold">1</span>
                                Banner Image 1
                            </p>
                            @if($banner->image_path_1)
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $banner->image_path_1) }}" class="w-full h-40 object-cover rounded-lg shadow-md">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 rounded-lg transition-all flex items-center justify-center">
                                        <span class="text-white opacity-0 group-hover:opacity-100 text-sm font-medium">Current Image</span>
                                    </div>
                                </div>
                            @else
                                <div class="w-full h-40 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <div class="text-center">
                                        <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-gray-500 text-sm">No image</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Banner 2 -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                <span class="w-6 h-6 bg-green-100 text-green-600 rounded-full flex items-center justify-center mr-2 text-xs font-bold">2</span>
                                Banner Image 2
                            </p>
                            @if($banner->image_path_2)
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $banner->image_path_2) }}" class="w-full h-40 object-cover rounded-lg shadow-md">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 rounded-lg transition-all flex items-center justify-center">
                                        <span class="text-white opacity-0 group-hover:opacity-100 text-sm font-medium">Current Image</span>
                                    </div>
                                </div>
                            @else
                                <div class="w-full h-40 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <div class="text-center">
                                        <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-gray-500 text-sm">No image</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Banner 3 -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                <span class="w-6 h-6 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mr-2 text-xs font-bold">3</span>
                                Banner Image 3
                            </p>
                            @if($banner->image_path_3)
                                <div class="relative group">
                                    <img src="{{ asset('storage/' . $banner->image_path_3) }}" class="w-full h-40 object-cover rounded-lg shadow-md">
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 rounded-lg transition-all flex items-center justify-center">
                                        <span class="text-white opacity-0 group-hover:opacity-100 text-sm font-medium">Current Image</span>
                                    </div>
                                </div>
                            @else
                                <div class="w-full h-40 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <div class="text-center">
                                        <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-gray-500 text-sm">No image</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="col-span-3 text-center py-8">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-gray-500">No banner images uploaded yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Upload New Images Section -->
            <div class="px-8 py-6 border-t border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                    </span>
                    Upload New Images
                </h3>
            </div>

            <div class="px-8 py-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Upload 1 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image 1</label>
                        <label id="uploadLabel1" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                            <div id="uploadPlaceholder1" class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-xs text-gray-500"><span class="font-semibold">Click to upload</span></p>
                            </div>
                            <img id="imagePreview1" class="hidden w-full h-32 object-cover rounded-xl" alt="Preview" />
                            <input type="file" id="bannerInput1" name="image_path_1" class="hidden" accept="image/*" />
                        </label>
                    </div>

                    <!-- Upload 2 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image 2</label>
                        <label id="uploadLabel2" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                            <div id="uploadPlaceholder2" class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-xs text-gray-500"><span class="font-semibold">Click to upload</span></p>
                            </div>
                            <img id="imagePreview2" class="hidden w-full h-32 object-cover rounded-xl" alt="Preview" />
                            <input type="file" id="bannerInput2" name="image_path_2" class="hidden" accept="image/*" />
                        </label>
                    </div>

                    <!-- Upload 3 -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Banner Image 3</label>
                        <label id="uploadLabel3" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors">
                            <div id="uploadPlaceholder3" class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <p class="text-xs text-gray-500"><span class="font-semibold">Click to upload</span></p>
                            </div>
                            <img id="imagePreview3" class="hidden w-full h-32 object-cover rounded-xl" alt="Preview" />
                            <input type="file" id="bannerInput3" name="image_path_3" class="hidden" accept="image/*" />
                        </label>
                    </div>
                </div>
                <p class="text-xs text-gray-500 text-center">Recommended: 1920x600px, JPG or PNG format</p>
            </div>

            <!-- Submit Section -->
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex items-center justify-end">
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Banner Images
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Image preview for banner 1
        $('#bannerInput1').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview1').attr('src', e.target.result).removeClass('hidden');
                    $('#uploadPlaceholder1').addClass('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        // Image preview for banner 2
        $('#bannerInput2').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview2').attr('src', e.target.result).removeClass('hidden');
                    $('#uploadPlaceholder2').addClass('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        // Image preview for banner 3
        $('#bannerInput3').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview3').attr('src', e.target.result).removeClass('hidden');
                    $('#uploadPlaceholder3').addClass('hidden');
                }
                reader.readAsDataURL(file);
            }
        });
    });
</script>
@endpush
