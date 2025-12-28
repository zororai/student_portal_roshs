@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Suppliers</h1>
        <a href="{{ route('admin.finance.purchase-orders.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs">Name</th><th class="px-6 py-3 text-left text-xs">Contact</th><th class="px-6 py-3 text-right text-xs">Orders</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($suppliers as $supplier)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium">{{ $supplier->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $supplier->phone ?? $supplier->email ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-right">{{ $supplier->purchase_orders_count }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="font-semibold mb-4">Add Supplier</h3>
            <form action="{{ route('admin.finance.purchase-orders.store-supplier') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Name *</label><input type="text" name="name" class="w-full border rounded-lg px-3 py-2" required></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label><input type="text" name="contact_person" class="w-full border rounded-lg px-3 py-2"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Email</label><input type="email" name="email" class="w-full border rounded-lg px-3 py-2"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Phone</label><input type="text" name="phone" class="w-full border rounded-lg px-3 py-2"></div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><textarea name="address" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea></div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
