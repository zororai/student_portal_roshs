@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="mt-2 text-sm text-gray-600">Welcome back, {{ Auth::user()->name }}!</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    {{ now()->format('l, M d, Y') }}
                </span>
            </div>
        </div>
    </div>

    @if(Auth::user()->hasRole('Admin'))
        @include('dashboard.admin')
    @elseif(Auth::user()->hasRole('Parent'))
        @include('dashboard.parents')
    @elseif(Auth::user()->hasRole('Teacher'))
        @include('dashboard.teacher')
    @elseif(Auth::user()->hasRole('Student'))
        @include('dashboard.student')
    @else
        @include('dashboard.admin')
    @endif
</div>
@endsection
