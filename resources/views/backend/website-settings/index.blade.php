@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Website Settings</h1>
        <p class="text-gray-600">Manage home banners, newsletters, and events</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- General Settings Card -->
        <a href="{{ route('admin.website.general') }}" class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">General Settings</h3>
                        <p class="text-sm text-gray-600">School name, contact info</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Colors Card -->
        <a href="{{ route('admin.website.colors') }}" class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Theme Colors</h3>
                        <p class="text-sm text-gray-600">Customize website colors</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Images Card -->
        <a href="{{ route('admin.website.images') }}" class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-pink-100 rounded-full">
                        <svg class="w-8 h-8 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Images</h3>
                        <p class="text-sm text-gray-600">Logo, favicon, footer logo</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Text Content Card -->
        <a href="{{ route('admin.website.text') }}" class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Text Content</h3>
                        <p class="text-sm text-gray-600">Vision, mission, about text</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Pages Card -->
        <a href="{{ route('admin.website.pages') }}" class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-teal-100 rounded-full">
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Pages</h3>
                        <p class="text-sm text-gray-600">About & Courses page content</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Home Banners Card -->
        <a href="{{ route('admin.website.banners') }}" class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-full">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Home Banners</h3>
                        <p class="text-sm text-gray-600">Slider images on homepage</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Newsletter Card -->
        <a href="{{ route('newsletters.index') }}" class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-indigo-100 rounded-full">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Newsletters</h3>
                        <p class="text-sm text-gray-600">Manage school newsletters</p>
                    </div>
                </div>
            </div>
        </a>

        <!-- Events Card -->
        <a href="{{ route('events.index') }}" class="block bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Events</h3>
                        <p class="text-sm text-gray-600">Manage school events</p>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
