<?php

namespace App\Http\Controllers;

use App\GroceryStockItem;
use App\GroceryStockTransaction;
use App\GroceryList;
use App\GroceryResponse;
use App\GroceryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroceryStockController extends Controller
{
    public function index(Request $request)
    {
        $term = $request->get('term', $this->getCurrentTerm());
        $year = $request->get('year', date('Y'));

        $stockItems = GroceryStockItem::where('is_active', true)
            ->with(['transactions' => function($query) use ($term, $year) {
                $query->where('term', $term)->where('year', $year)->orderBy('transaction_date', 'desc');
            }])
            ->orderBy('name')
            ->get();

        // Calculate balances for each item
        foreach ($stockItems as $item) {
            $item->balance_bf = $this->getBalanceBroughtForward($item->id, $term, $year);
            $item->received = $item->transactions->where('type', 'received')->sum('quantity');
            $item->usage = $item->transactions->where('type', 'usage')->sum('quantity');
            $item->bad_stock = $item->transactions->where('type', 'bad_stock')->sum('quantity');
            $item->closing_balance = $item->balance_bf + $item->received - $item->usage - $item->bad_stock;
        }

        // Get collected groceries from student responses for the selected term/year
        $collectedGroceries = $this->getCollectedGroceries($term, $year);

        $terms = ['term_1', 'term_2', 'term_3'];
        $years = range(date('Y') - 2, date('Y') + 1);

        return view('backend.finance.grocery-stock.index', compact('stockItems', 'term', 'year', 'terms', 'years', 'collectedGroceries'));
    }

    private function getCollectedGroceries($term, $year)
    {
        // Map term format (term_1 -> first, term_2 -> second, term_3 -> third)
        $termMap = [
            'term_1' => 'first',
            'term_2' => 'second', 
            'term_3' => 'third',
            'first' => 'first',
            'second' => 'second',
            'third' => 'third'
        ];
        $dbTerm = $termMap[$term] ?? $term;

        // Get grocery lists for the selected term and year
        $groceryLists = GroceryList::where('term', $dbTerm)
            ->where('year', $year)
            ->with(['items', 'responses' => function($query) {
                $query->where('submitted', true);
            }])
            ->get();

        $collectedItems = [];

        foreach ($groceryLists as $list) {
            foreach ($list->items as $item) {
                $itemName = $item->name;
                // Extract numeric value from quantity (e.g., "2kg" -> 2)
                $requiredQty = floatval(preg_replace('/[^0-9.]/', '', $item->quantity ?? '0'));
                
                if (!isset($collectedItems[$itemName])) {
                    $collectedItems[$itemName] = [
                        'name' => $itemName,
                        'required_per_student' => $item->quantity, // Keep original for display
                        'total_required' => 0,
                        'total_collected' => 0,
                        'total_short' => 0,
                        'total_extra' => 0,
                        'students_submitted' => 0
                    ];
                }

                foreach ($list->responses as $response) {
                    $itemsBought = $response->items_bought ?? [];
                    $actualQty = $response->item_actual_qty ?? [];
                    $shortQty = $response->item_short_qty ?? [];
                    $extraQty = $response->item_extra_qty ?? [];

                    // Check if this item was bought (handle both string and int IDs)
                    if (in_array($item->id, $itemsBought) || in_array((string)$item->id, $itemsBought)) {
                        $collectedItems[$itemName]['students_submitted']++;
                        $collectedItems[$itemName]['total_required'] += $requiredQty;
                        
                        // Get actual quantity brought
                        $actualKey = $item->id;
                        $actual = 0;
                        if (isset($actualQty[$actualKey])) {
                            $actual = floatval(preg_replace('/[^0-9.]/', '', $actualQty[$actualKey]));
                        } elseif (isset($actualQty[(string)$actualKey])) {
                            $actual = floatval(preg_replace('/[^0-9.]/', '', $actualQty[(string)$actualKey]));
                        } else {
                            $actual = $requiredQty; // Default to required if not specified
                        }
                        $collectedItems[$itemName]['total_collected'] += $actual;
                        
                        // Add short/extra quantities
                        if (isset($shortQty[$actualKey]) && is_numeric($shortQty[$actualKey])) {
                            $collectedItems[$itemName]['total_short'] += floatval($shortQty[$actualKey]);
                        } elseif (isset($shortQty[(string)$actualKey]) && is_numeric($shortQty[(string)$actualKey])) {
                            $collectedItems[$itemName]['total_short'] += floatval($shortQty[(string)$actualKey]);
                        }
                        if (isset($extraQty[$actualKey]) && is_numeric($extraQty[$actualKey])) {
                            $collectedItems[$itemName]['total_extra'] += floatval($extraQty[$actualKey]);
                        } elseif (isset($extraQty[(string)$actualKey]) && is_numeric($extraQty[(string)$actualKey])) {
                            $collectedItems[$itemName]['total_extra'] += floatval($extraQty[(string)$actualKey]);
                        }
                    }
                }
            }
        }

        return collect($collectedItems)->sortBy('name')->values();
    }

    public function items()
    {
        $items = GroceryStockItem::orderBy('name')->get();
        return view('backend.finance.grocery-stock.items', compact('items'));
    }

    public function storeItem(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50'
        ]);

        GroceryStockItem::create($validated);

        return redirect()->back()->with('success', 'Stock item added successfully!');
    }

    public function updateItem(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'is_active' => 'boolean'
        ]);

        $item = GroceryStockItem::findOrFail($id);
        $item->update($validated);

        return redirect()->back()->with('success', 'Stock item updated successfully!');
    }

    public function transactions(Request $request)
    {
        $term = $request->get('term', $this->getCurrentTerm());
        $year = $request->get('year', date('Y'));
        $type = $request->get('type');

        $query = GroceryStockTransaction::with(['stockItem', 'recordedBy'])
            ->where('term', $term)
            ->where('year', $year);

        if ($type) {
            $query->where('type', $type);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->orderBy('created_at', 'desc')->paginate(50);
        $stockItems = GroceryStockItem::where('is_active', true)->orderBy('name')->get();
        $terms = ['term_1', 'term_2', 'term_3'];
        $years = range(date('Y') - 2, date('Y') + 1);

        return view('backend.finance.grocery-stock.transactions', compact('transactions', 'stockItems', 'term', 'year', 'type', 'terms', 'years'));
    }

    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'stock_item_id' => 'required|exists:grocery_stock_items,id',
            'type' => 'required|in:received,usage,bad_stock,balance_bf,adjustment',
            'quantity' => 'required|numeric|min:0.01',
            'term' => 'required|string',
            'year' => 'required|integer',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date'
        ]);

        $item = GroceryStockItem::findOrFail($validated['stock_item_id']);
        
        // Calculate new balance
        if (in_array($validated['type'], ['received', 'balance_bf'])) {
            $newBalance = $item->current_balance + $validated['quantity'];
        } elseif ($validated['type'] == 'adjustment') {
            $newBalance = $item->current_balance + $validated['quantity']; // Can be negative
        } else {
            $newBalance = $item->current_balance - $validated['quantity'];
        }

        $validated['balance_after'] = $newBalance;
        $validated['recorded_by'] = Auth::id();

        GroceryStockTransaction::create($validated);

        // Update item balance
        $item->current_balance = $newBalance;
        $item->save();

        return redirect()->back()->with('success', 'Transaction recorded successfully!');
    }

    public function recordUsage()
    {
        $stockItems = GroceryStockItem::where('is_active', true)->orderBy('name')->get();
        $term = $this->getCurrentTerm();
        $year = date('Y');

        return view('backend.finance.grocery-stock.record-usage', compact('stockItems', 'term', 'year'));
    }

    public function storeUsage(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.stock_item_id' => 'required|exists:grocery_stock_items,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'term' => 'required|string',
            'year' => 'required|integer',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date'
        ]);

        foreach ($validated['items'] as $itemData) {
            if ($itemData['quantity'] <= 0) continue;

            $item = GroceryStockItem::findOrFail($itemData['stock_item_id']);
            $newBalance = $item->current_balance - $itemData['quantity'];

            GroceryStockTransaction::create([
                'stock_item_id' => $itemData['stock_item_id'],
                'type' => 'usage',
                'quantity' => $itemData['quantity'],
                'balance_after' => $newBalance,
                'term' => $validated['term'],
                'year' => $validated['year'],
                'description' => $validated['description'],
                'recorded_by' => Auth::id(),
                'transaction_date' => $validated['transaction_date']
            ]);

            $item->current_balance = $newBalance;
            $item->save();
        }

        return redirect()->route('admin.grocery-stock.index')->with('success', 'Usage recorded successfully!');
    }

    public function recordBadStock()
    {
        $stockItems = GroceryStockItem::where('is_active', true)->orderBy('name')->get();
        $term = $this->getCurrentTerm();
        $year = date('Y');

        return view('backend.finance.grocery-stock.record-bad-stock', compact('stockItems', 'term', 'year'));
    }

    public function storeBadStock(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.stock_item_id' => 'required|exists:grocery_stock_items,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'term' => 'required|string',
            'year' => 'required|integer',
            'description' => 'nullable|string|max:255',
            'transaction_date' => 'required|date'
        ]);

        foreach ($validated['items'] as $itemData) {
            if ($itemData['quantity'] <= 0) continue;

            $item = GroceryStockItem::findOrFail($itemData['stock_item_id']);
            $newBalance = $item->current_balance - $itemData['quantity'];

            GroceryStockTransaction::create([
                'stock_item_id' => $itemData['stock_item_id'],
                'type' => 'bad_stock',
                'quantity' => $itemData['quantity'],
                'balance_after' => $newBalance,
                'term' => $validated['term'],
                'year' => $validated['year'],
                'description' => $validated['description'] ?? 'Bad stock write-off',
                'recorded_by' => Auth::id(),
                'transaction_date' => $validated['transaction_date']
            ]);

            $item->current_balance = $newBalance;
            $item->save();
        }

        return redirect()->route('admin.grocery-stock.index')->with('success', 'Bad stock recorded successfully!');
    }

    public function carryForward(Request $request)
    {
        $validated = $request->validate([
            'from_term' => 'required|string',
            'from_year' => 'required|integer',
            'to_term' => 'required|string',
            'to_year' => 'required|integer'
        ]);

        $stockItems = GroceryStockItem::where('is_active', true)->get();

        foreach ($stockItems as $item) {
            $closingBalance = $this->getClosingBalance($item->id, $validated['from_term'], $validated['from_year']);
            
            if ($closingBalance > 0) {
                GroceryStockTransaction::create([
                    'stock_item_id' => $item->id,
                    'type' => 'balance_bf',
                    'quantity' => $closingBalance,
                    'balance_after' => $closingBalance,
                    'term' => $validated['to_term'],
                    'year' => $validated['to_year'],
                    'description' => "Balance B/F from {$validated['from_term']} {$validated['from_year']}",
                    'recorded_by' => Auth::id(),
                    'transaction_date' => now()->format('Y-m-d')
                ]);
            }
        }

        return redirect()->route('admin.grocery-stock.index', [
            'term' => $validated['to_term'],
            'year' => $validated['to_year']
        ])->with('success', 'Balances carried forward successfully!');
    }

    public function print(Request $request)
    {
        $term = $request->get('term', $this->getCurrentTerm());
        $year = $request->get('year', date('Y'));

        $stockItems = GroceryStockItem::where('is_active', true)->orderBy('name')->get();

        foreach ($stockItems as $item) {
            $item->balance_bf = $this->getBalanceBroughtForward($item->id, $term, $year);
            $item->received = GroceryStockTransaction::where('stock_item_id', $item->id)
                ->where('term', $term)->where('year', $year)->where('type', 'received')->sum('quantity');
            $item->usage = GroceryStockTransaction::where('stock_item_id', $item->id)
                ->where('term', $term)->where('year', $year)->where('type', 'usage')->sum('quantity');
            $item->bad_stock = GroceryStockTransaction::where('stock_item_id', $item->id)
                ->where('term', $term)->where('year', $year)->where('type', 'bad_stock')->sum('quantity');
            $item->closing_balance = $item->balance_bf + $item->received - $item->usage - $item->bad_stock;
        }

        return view('backend.finance.grocery-stock.print', compact('stockItems', 'term', 'year'));
    }

    private function getCurrentTerm()
    {
        $month = date('n');
        if ($month >= 1 && $month <= 4) return 'term_1';
        if ($month >= 5 && $month <= 8) return 'term_2';
        return 'term_3';
    }

    private function getBalanceBroughtForward($itemId, $term, $year)
    {
        return GroceryStockTransaction::where('stock_item_id', $itemId)
            ->where('term', $term)
            ->where('year', $year)
            ->where('type', 'balance_bf')
            ->sum('quantity');
    }

    private function getClosingBalance($itemId, $term, $year)
    {
        $balanceBf = $this->getBalanceBroughtForward($itemId, $term, $year);
        
        $received = GroceryStockTransaction::where('stock_item_id', $itemId)
            ->where('term', $term)->where('year', $year)->where('type', 'received')->sum('quantity');
        
        $usage = GroceryStockTransaction::where('stock_item_id', $itemId)
            ->where('term', $term)->where('year', $year)->where('type', 'usage')->sum('quantity');
        
        $badStock = GroceryStockTransaction::where('stock_item_id', $itemId)
            ->where('term', $term)->where('year', $year)->where('type', 'bad_stock')->sum('quantity');

        return $balanceBf + $received - $usage - $badStock;
    }
}
