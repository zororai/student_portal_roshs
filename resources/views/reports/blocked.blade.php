@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            @if(isset($pending) && $pending)
            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 px-6 py-8 text-center">
                <div class="w-20 h-20 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Payment Verification Pending</h1>
            </div>
            @else
            <div class="bg-gradient-to-r from-red-500 to-red-600 px-6 py-8 text-center">
                <div class="w-20 h-20 mx-auto bg-white/20 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Results Access Blocked</h1>
            </div>
            @endif
            
            <!-- Content -->
            <div class="px-6 py-8">
                <div class="text-center mb-6">
                    <p class="text-gray-700 text-lg mb-4">{{ $message }}</p>
                    
                    @if(isset($details) && count($details) > 0)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6 mt-6">
                        <p class="text-sm text-gray-600 uppercase tracking-wide mb-4">Outstanding Balances</p>
                        <div class="space-y-3">
                            @foreach($details as $detail)
                            <div class="flex items-center justify-between bg-white px-4 py-3 rounded-lg">
                                <span class="text-gray-700">{{ $detail }}</span>
                            </div>
                            @endforeach
                        </div>
                        @php
                            $totalBalance = ($outstanding ?? 0) + ($grocery_arrears ?? 0);
                        @endphp
                        <div class="border-t border-red-200 mt-4 pt-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-700 font-semibold">Total Outstanding:</span>
                                <span class="text-2xl font-bold text-red-600">${{ number_format($totalBalance, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    @elseif(isset($outstanding) && $outstanding > 0)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6 mt-6">
                        <p class="text-sm text-gray-600 uppercase tracking-wide mb-2">Outstanding Balance</p>
                        <p class="text-4xl font-bold text-red-600">${{ number_format($outstanding, 2) }}</p>
                    </div>
                    @endif
                </div>
                
                @if(isset($show_verification_link) && $show_verification_link)
                <div class="bg-blue-50 rounded-lg p-6 mt-6">
                    <h3 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        How to View Results
                    </h3>
                    <ul class="text-gray-600 space-y-2 text-sm">
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">1.</span>
                            Make your fee payment via bank transfer or at the school finance office.
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">2.</span>
                            Upload your payment receipt with the receipt/reference number.
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">3.</span>
                            Wait for admin verification (usually within 24-48 hours).
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-600 mr-2">4.</span>
                            Once verified, you can view your child's results.
                        </li>
                    </ul>
                </div>
                
                <div class="mt-6 text-center space-y-3">
                    @if(!isset($pending) || !$pending)
                    <a href="{{ route('parent.payment-verification.create') }}" class="inline-flex items-center px-6 py-3 bg-emerald-600 text-white font-semibold rounded-lg hover:bg-emerald-700 transition-colors w-full justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Submit Payment Proof
                    </a>
                    @else
                    <a href="{{ route('parent.payment-verification.create') }}" class="inline-flex items-center px-6 py-3 bg-yellow-600 text-white font-semibold rounded-lg hover:bg-yellow-700 transition-colors w-full justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        View Submission Status
                    </a>
                    @endif
                    <a href="{{ route('home') }}" class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors w-full justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
                @else
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
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
