@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Receipt Settings</h1>
        <p class="text-gray-600">Customize the information displayed on payment receipts</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md">
        <form action="{{ route('admin.settings.receipt.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                <div class="border-b border-gray-200 pb-6">
                    <label for="receipt_school_short_name" class="block text-sm font-medium text-gray-700 mb-1">
                        School Short Name
                    </label>
                    <p class="text-xs text-gray-500 mb-2">Short name displayed at the top of receipts (e.g., ROSHS)</p>
                    <input 
                        type="text" 
                        name="receipt_school_short_name" 
                        id="receipt_school_short_name" 
                        value="{{ $settings['receipt_school_short_name'] }}"
                        class="w-full max-w-md px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                    @error('receipt_school_short_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-b border-gray-200 pb-6">
                    <label for="receipt_school_full_name" class="block text-sm font-medium text-gray-700 mb-1">
                        School Full Name
                    </label>
                    <p class="text-xs text-gray-500 mb-2">Full school name displayed below the short name on receipts</p>
                    <input 
                        type="text" 
                        name="receipt_school_full_name" 
                        id="receipt_school_full_name" 
                        value="{{ $settings['receipt_school_full_name'] }}"
                        class="w-full max-w-md px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                    @error('receipt_school_full_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-b border-gray-200 pb-6">
                    <label for="receipt_footer_message" class="block text-sm font-medium text-gray-700 mb-1">
                        Footer Message
                    </label>
                    <p class="text-xs text-gray-500 mb-2">Thank you message displayed at the bottom of receipts</p>
                    <input 
                        type="text" 
                        name="receipt_footer_message" 
                        id="receipt_footer_message" 
                        value="{{ $settings['receipt_footer_message'] }}"
                        class="w-full max-w-md px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                    @error('receipt_footer_message')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="receipt_footer_note" class="block text-sm font-medium text-gray-700 mb-1">
                        Footer Note
                    </label>
                    <p class="text-xs text-gray-500 mb-2">Additional note displayed at the very bottom of receipts</p>
                    <input 
                        type="text" 
                        name="receipt_footer_note" 
                        id="receipt_footer_note" 
                        value="{{ $settings['receipt_footer_note'] }}"
                        class="w-full max-w-md px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                    @error('receipt_footer_note')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 rounded-b-lg flex justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Receipt Preview</h2>
        <div class="border border-dashed border-gray-300 p-4 max-w-xs mx-auto text-center font-mono text-sm">
            <div class="border-b border-dashed border-gray-300 pb-2 mb-2">
                <p class="font-bold text-lg" id="preview_short_name">{{ $settings['receipt_school_short_name'] }}</p>
                <p class="text-xs" id="preview_full_name">{{ $settings['receipt_school_full_name'] }}</p>
                <p class="text-xs">Payment Receipt</p>
            </div>
            <p class="text-xs text-gray-500 my-4">... receipt details ...</p>
            <div class="border-t border-dashed border-gray-300 pt-2 mt-2">
                <p class="font-bold text-sm" id="preview_footer_message">{{ $settings['receipt_footer_message'] }}</p>
                <p class="text-xs" id="preview_footer_note">{{ $settings['receipt_footer_note'] }}</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shortNameInput = document.getElementById('receipt_school_short_name');
    const fullNameInput = document.getElementById('receipt_school_full_name');
    const footerMessageInput = document.getElementById('receipt_footer_message');
    const footerNoteInput = document.getElementById('receipt_footer_note');

    shortNameInput.addEventListener('input', function() {
        document.getElementById('preview_short_name').textContent = this.value;
    });
    fullNameInput.addEventListener('input', function() {
        document.getElementById('preview_full_name').textContent = this.value;
    });
    footerMessageInput.addEventListener('input', function() {
        document.getElementById('preview_footer_message').textContent = this.value;
    });
    footerNoteInput.addEventListener('input', function() {
        document.getElementById('preview_footer_note').textContent = this.value;
    });
});
</script>
@endsection
