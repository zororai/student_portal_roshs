@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h1>
            <p class="text-gray-600">Barcode: <span class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $product->barcode }}</span></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('finance.products.edit', $product->id) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700">Edit Product</a>
            <a href="{{ route('finance.products') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <div class="flex gap-6">
                    @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-48 h-48 object-cover rounded-lg">
                    @else
                    <div class="w-48 h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    @endif
                    <div class="flex-1">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-gray-500 text-sm">SKU</span>
                                <p class="font-semibold">{{ $product->sku ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Category</span>
                                <p class="font-semibold">{{ $product->category ?? 'Uncategorized' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Selling Price</span>
                                <p class="font-semibold text-green-600 text-xl">${{ number_format($product->price, 2) }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Cost Price</span>
                                <p class="font-semibold">${{ number_format($product->cost_price, 2) }}</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Current Stock</span>
                                <p class="font-semibold text-2xl {{ $product->quantity <= 0 ? 'text-red-600' : ($product->isLowStock() ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ $product->quantity }}
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Min Stock Level</span>
                                <p class="font-semibold">{{ $product->min_stock_level }}</p>
                            </div>
                        </div>
                        @if($product->description)
                        <div class="mt-4 pt-4 border-t">
                            <span class="text-gray-500 text-sm">Description</span>
                            <p class="text-gray-700">{{ $product->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Stock Movements -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b">
                    <h3 class="font-semibold text-gray-800">Stock Movements</h3>
                </div>
                <div class="divide-y max-h-96 overflow-y-auto">
                    @forelse($product->stockMovements as $movement)
                    <div class="px-6 py-3 flex justify-between items-center">
                        <div>
                            <span class="px-2 py-1 text-xs rounded-full 
                                {{ $movement->type == 'in' ? 'bg-green-100 text-green-800' : ($movement->type == 'out' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ ucfirst($movement->type) }}
                            </span>
                            <span class="ml-2 text-gray-700">{{ $movement->reason }}</span>
                        </div>
                        <div class="text-right">
                            <div class="font-semibold {{ $movement->type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $movement->type == 'in' ? '+' : '-' }}{{ $movement->quantity }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $movement->created_at->format('d M Y H:i') }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-8 text-center text-gray-500">No stock movements recorded</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Sidebar -->
        <div class="space-y-6">
            <!-- Stock Adjustment -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Adjust Stock</h3>
                <form action="{{ route('finance.products.adjust-stock', $product->id) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment Type</label>
                            <select name="adjustment_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="add">Add Stock</option>
                                <option value="remove">Remove Stock</option>
                                <option value="set">Set to Amount</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                            <input type="number" name="quantity" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                            <input type="text" name="reason" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Optional">
                        </div>
                        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Update Stock</button>
                    </div>
                </form>
            </div>

            <!-- Barcode Display -->
            <div class="bg-white rounded-lg shadow-sm border p-6 text-center" id="barcode-section">
                <h3 class="font-semibold text-gray-800 mb-4 no-print">Product Barcode</h3>
                <div class="bg-white p-4 border-2 border-dashed rounded-lg print-barcode" id="barcode-container">
                    <svg id="barcode"></svg>
                    <p class="font-mono text-lg mt-2">{{ $product->barcode }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $product->name }}</p>
                </div>
                <div class="mt-4 flex gap-2 justify-center no-print">
                    <button onclick="printBarcode()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print
                    </button>
                    <button onclick="downloadBarcode()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download
                    </button>
                </div>
            </div>

            <!-- Stats -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Sales Stats</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Sold:</span>
                        <span class="font-semibold">{{ $product->total_sold ?? 0 }} units</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Revenue:</span>
                        <span class="font-semibold text-green-600">${{ number_format($product->total_revenue ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Stock Value:</span>
                        <span class="font-semibold">${{ number_format($product->price * $product->quantity, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #barcode-container, #barcode-container * { visibility: visible; }
    #barcode-container { 
        position: absolute; 
        left: 50%; 
        top: 50%; 
        transform: translate(-50%, -50%);
        border: none !important;
    }
    .no-print { display: none !important; }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
JsBarcode("#barcode", "{{ $product->barcode }}", {
    format: "CODE128",
    width: 2,
    height: 80,
    displayValue: false
});

function printBarcode() {
    window.print();
}

function downloadBarcode() {
    const svg = document.getElementById('barcode');
    const svgData = new XMLSerializer().serializeToString(svg);
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    
    img.onload = function() {
        canvas.width = img.width + 40;
        canvas.height = img.height + 80;
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 20, 10);
        
        // Add barcode text
        ctx.font = '14px monospace';
        ctx.fillStyle = 'black';
        ctx.textAlign = 'center';
        ctx.fillText('{{ $product->barcode }}', canvas.width / 2, img.height + 30);
        ctx.font = '12px sans-serif';
        ctx.fillText('{{ $product->name }}', canvas.width / 2, img.height + 50);
        
        const link = document.createElement('a');
        link.download = '{{ $product->barcode }}.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    };
    
    img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
}
</script>
@endsection
