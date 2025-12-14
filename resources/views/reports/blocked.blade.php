@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-8 text-center">
                <div class="w-20 h-20 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Results Access Blocked</h1>
            </div>
            
            <!-- Content -->
            <div class="px-6 py-8">
                <div class="text-center mb-6">
                    <p class="text-gray-700 text-lg mb-4">{{ $message }}</p>
                    
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6 mt-6">
                        <p class="text-sm text-gray-600 uppercase tracking-wide mb-2">Outstanding Balance</p>
                        <p class="text-4xl font-bold text-red-600">${{ number_format($outstanding, 2) }}</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-6 mt-6">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        How to Resolve
                    </h3>
                    <ul class="text-gray-600 space-y-2 text-sm">
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">1.</span>
                            Please visit the school finance office to settle your outstanding fees.
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">2.</span>
                            You can also make payment via bank transfer to the school account.
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">3.</span>
                            Once payment is confirmed, results access will be restored automatically.
                        </li>
                    </ul>
                </div>
                
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500 mb-4">If you believe this is an error, please contact the school administration.</p>
                    <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
