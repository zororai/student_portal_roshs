@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Point of Sale</h1>
            <p class="text-gray-600">Scan barcode or search products to make a sale</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('finance.products.sales-history') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Sales History</a>
            <a href="{{ route('finance.products') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Back to Products</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Product Search & Barcode Scanner -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Barcode Scanner -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    Scan Barcode
                </h3>
                <div class="flex gap-2">
                    <input type="text" id="barcodeInput" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-lg font-mono" placeholder="Scan or enter barcode..." autofocus>
                    <button onclick="searchBarcode()" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-500 mt-2">Press Enter after scanning or click Search</p>
            </div>

            <!-- Product Grid -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Select Products</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-96 overflow-y-auto">
                    @foreach($products as $product)
                    <button onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->quantity }}, '{{ $product->barcode }}')" 
                        class="p-3 border rounded-lg hover:bg-blue-50 hover:border-blue-300 transition-colors text-left">
                        @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-16 object-cover rounded mb-2">
                        @else
                        <div class="w-full h-16 bg-gray-100 rounded mb-2 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        @endif
                        <div class="font-medium text-sm text-gray-800 truncate">{{ $product->name }}</div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-green-600 font-bold">${{ number_format($product->price, 2) }}</span>
                            <span class="text-xs text-gray-500">Qty: {{ $product->quantity }}</span>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right: Cart -->
        <div class="bg-white rounded-lg shadow-sm border p-6 h-fit sticky top-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Cart
            </h3>

            <div id="cartItems" class="space-y-3 max-h-64 overflow-y-auto mb-4">
                <p class="text-gray-500 text-center py-8" id="emptyCartMessage">No items in cart</p>
            </div>

            <div class="border-t pt-4 space-y-3">
                <div class="flex justify-between text-lg font-bold">
                    <span>Total:</span>
                    <span id="cartTotal">$0.00</span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount Received</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-500">$</span>
                        <input type="number" id="amountPaid" step="0.01" min="0" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="0.00" onkeyup="calculateChange()">
                    </div>
                </div>

                <div class="flex justify-between text-lg">
                    <span>Change:</span>
                    <span id="changeAmount" class="font-bold text-green-600">$0.00</span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select id="paymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name (Optional)</label>
                    <input type="text" id="customerName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500" placeholder="Walk-in customer">
                </div>

                <button onclick="processSale()" id="checkoutBtn" disabled class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed font-semibold">
                    Complete Sale
                </button>
                <button onclick="clearCart()" class="w-full bg-red-100 text-red-600 py-2 rounded-lg hover:bg-red-200">
                    Clear Cart
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="text-center">
            <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Sale Completed!</h3>
            <p id="saleDetails" class="text-gray-600 mb-4"></p>
            <div class="flex gap-3 justify-center">
                <button onclick="printReceipt()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Print Receipt</button>
                <button onclick="closeModal()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">New Sale</button>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
let lastSaleNumber = '';

document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        searchBarcode();
    }
});

function searchBarcode() {
    const barcode = document.getElementById('barcodeInput').value.trim();
    if (!barcode) return;

    fetch('{{ route("finance.products.find-by-barcode") }}?barcode=' + barcode)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                addToCart(data.product.id, data.product.name, data.product.price, data.product.quantity, data.product.barcode);
                document.getElementById('barcodeInput').value = '';
            } else {
                alert(data.message);
            }
        })
        .catch(err => alert('Error searching product'));
    
    document.getElementById('barcodeInput').focus();
}

function addToCart(id, name, price, stock, barcode) {
    const existing = cart.find(item => item.id === id);
    
    if (existing) {
        if (existing.quantity >= stock) {
            alert('Cannot add more - only ' + stock + ' in stock');
            return;
        }
        existing.quantity++;
    } else {
        cart.push({ id, name, price, stock, barcode, quantity: 1 });
    }
    
    updateCartDisplay();
}

function updateQuantity(id, change) {
    const item = cart.find(i => i.id === id);
    if (!item) return;
    
    const newQty = item.quantity + change;
    if (newQty < 1) {
        removeFromCart(id);
        return;
    }
    if (newQty > item.stock) {
        alert('Cannot add more - only ' + item.stock + ' in stock');
        return;
    }
    
    item.quantity = newQty;
    updateCartDisplay();
}

function removeFromCart(id) {
    cart = cart.filter(item => item.id !== id);
    updateCartDisplay();
}

function clearCart() {
    cart = [];
    updateCartDisplay();
}

function updateCartDisplay() {
    const container = document.getElementById('cartItems');
    const emptyMsg = document.getElementById('emptyCartMessage');
    const checkoutBtn = document.getElementById('checkoutBtn');
    
    if (cart.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8" id="emptyCartMessage">No items in cart</p>';
        checkoutBtn.disabled = true;
    } else {
        container.innerHTML = cart.map(item => `
            <div class="flex items-center justify-between bg-gray-50 p-2 rounded">
                <div class="flex-1">
                    <div class="font-medium text-sm">${item.name}</div>
                    <div class="text-xs text-gray-500">$${item.price.toFixed(2)} each</div>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="updateQuantity(${item.id}, -1)" class="w-6 h-6 bg-gray-200 rounded hover:bg-gray-300">-</button>
                    <span class="w-8 text-center">${item.quantity}</span>
                    <button onclick="updateQuantity(${item.id}, 1)" class="w-6 h-6 bg-gray-200 rounded hover:bg-gray-300">+</button>
                    <span class="w-16 text-right font-semibold">$${(item.price * item.quantity).toFixed(2)}</span>
                    <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700 ml-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        `).join('');
        checkoutBtn.disabled = false;
    }
    
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    document.getElementById('cartTotal').textContent = '$' + total.toFixed(2);
    calculateChange();
}

function calculateChange() {
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const change = Math.max(0, paid - total);
    document.getElementById('changeAmount').textContent = '$' + change.toFixed(2);
}

function processSale() {
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
    
    if (amountPaid < total) {
        alert('Insufficient payment amount');
        return;
    }
    
    const data = {
        items: cart.map(item => ({ product_id: item.id, quantity: item.quantity })),
        amount_paid: amountPaid,
        payment_method: document.getElementById('paymentMethod').value,
        customer_name: document.getElementById('customerName').value || null,
    };
    
    fetch('{{ route("finance.products.process-sale") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            lastSaleNumber = data.sale.sale_number;
            document.getElementById('saleDetails').innerHTML = `
                Sale #${data.sale.sale_number}<br>
                Total: $${data.sale.total_amount.toFixed(2)}<br>
                Change: $${data.sale.change_given.toFixed(2)}
            `;
            document.getElementById('successModal').classList.remove('hidden');
            clearCart();
            document.getElementById('amountPaid').value = '';
            document.getElementById('customerName').value = '';
        } else {
            alert(data.message);
        }
    })
    .catch(err => alert('Error processing sale'));
}

function closeModal() {
    document.getElementById('successModal').classList.add('hidden');
    document.getElementById('barcodeInput').focus();
}

function printReceipt() {
    window.open('{{ url("finance/products/sales") }}/' + lastSaleNumber.split('-').pop() + '/receipt', '_blank');
}
</script>
@endsection
