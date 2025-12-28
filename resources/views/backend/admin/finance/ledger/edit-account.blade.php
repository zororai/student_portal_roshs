@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Account - {{ $account->account_code }}</h1>
        <a href="{{ route('admin.finance.ledger.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6 max-w-2xl">
        <form action="{{ route('admin.finance.ledger.update-account', $account->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Account Name *</label><input type="text" name="account_name" value="{{ $account->account_name }}" class="w-full border rounded-lg px-3 py-2" required></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Category</label><input type="text" name="category" value="{{ $account->category }}" class="w-full border rounded-lg px-3 py-2"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Description</label><textarea name="description" rows="3" class="w-full border rounded-lg px-3 py-2">{{ $account->description }}</textarea></div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Update Account</button>
            </div>
        </form>
    </div>
</div>
@endsection
