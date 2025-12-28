@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create Purchase Order</h1>
        <a href="{{ route('admin.finance.purchase-orders.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <form action="{{ route('admin.finance.purchase-orders.store') }}" method="POST" x-data="poForm()">
        @csrf
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Order Date *</label><input type="date" name="order_date" value="{{ date('Y-m-d') }}" class="w-full border rounded-lg px-3 py-2" required></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                    <select name="supplier_id" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Expected Delivery</label><input type="date" name="expected_delivery_date" class="w-full border rounded-lg px-3 py-2"></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <h3 class="font-semibold mb-4">Order Items</h3>
            <template x-for="(item, index) in items" :key="index">
                <div class="grid grid-cols-12 gap-2 mb-2">
                    <input type="text" :name="'items['+index+'][item_name]'" x-model="item.name" placeholder="Item Name" class="col-span-4 border rounded px-2 py-1" required>
                    <input type="number" :name="'items['+index+'][quantity]'" x-model="item.qty" placeholder="Qty" class="col-span-2 border rounded px-2 py-1" min="1" required>
                    <input type="text" :name="'items['+index+'][unit]'" x-model="item.unit" placeholder="Unit" class="col-span-2 border rounded px-2 py-1" value="pcs">
                    <input type="number" :name="'items['+index+'][unit_price]'" x-model="item.price" placeholder="Price" step="0.01" class="col-span-2 border rounded px-2 py-1" required>
                    <div class="col-span-1 text-right py-1" x-text="'$' + (item.qty * item.price || 0).toFixed(2)"></div>
                    <button type="button" @click="items.splice(index, 1)" class="col-span-1 text-red-600">&times;</button>
                </div>
            </template>
            <button type="button" @click="items.push({name:'',qty:1,unit:'pcs',price:0})" class="text-blue-600 text-sm">+ Add Item</button>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full border rounded-lg px-3 py-2"></textarea>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Create Purchase Order</button>
    </form>
</div>

<script>
function poForm() {
    return { items: [{name:'',qty:1,unit:'pcs',price:0}] };
}
</script>
@endsection
