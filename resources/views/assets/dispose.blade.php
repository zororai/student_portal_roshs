@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('finance.assets.show', $asset) }}" class="text-gray-600 hover:text-gray-800 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dispose Asset</h1>
            <p class="text-gray-600">{{ $asset->asset_code }} - {{ $asset->name }}</p>
        </div>
    </div>

    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
                <h3 class="text-yellow-800 font-semibold">Warning</h3>
                <p class="text-yellow-700">Disposing an asset is a permanent action. The asset will be marked as disposed and cannot be reassigned. Financial entries will be posted to the ledger.</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Asset Summary</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-500">Asset Code</span>
                    <span class="font-medium">{{ $asset->asset_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Purchase Cost</span>
                    <span class="font-medium">${{ number_format($asset->purchase_cost, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Current Value</span>
                    <span class="font-medium text-green-600">${{ number_format($asset->current_value, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Depreciation</span>
                    <span class="font-medium text-red-600">${{ number_format($asset->purchase_cost - $asset->current_value, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Age</span>
                    <span class="font-medium">{{ $asset->age_in_years }} years</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Disposal Details</h2>
            <form action="{{ route('finance.assets.dispose', $asset) }}" method="POST">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Disposal *</label>
                        <select name="disposal_reason" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('disposal_reason') border-red-500 @enderror">
                            <option value="">Select Reason</option>
                            <option value="End of useful life">End of useful life</option>
                            <option value="Irreparable damage">Irreparable damage</option>
                            <option value="Obsolete">Obsolete/Outdated</option>
                            <option value="Sold">Sold</option>
                            <option value="Donated">Donated</option>
                            <option value="Lost/Stolen">Lost/Stolen</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('disposal_reason')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Disposal Value ($)</label>
                        <input type="number" name="disposal_value" value="0" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-gray-500 text-xs mt-1">Amount received from sale or salvage value</p>
                    </div>
                </div>

                <div class="mt-6 flex gap-4">
                    <a href="{{ route('finance.assets.show', $asset) }}" class="flex-1 px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-center">Cancel</a>
                    <button type="submit" class="flex-1 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700" onclick="return confirm('Are you sure you want to dispose this asset? This action cannot be undone.')">Dispose Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
