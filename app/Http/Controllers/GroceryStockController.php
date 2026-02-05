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

        // Get collected groceries from student responses for the selected term/year
        $collectedGroceries = $this->getCollectedGroceries($term, $year);

        // Create a lookup map of collected groceries by stock_item_id
        $collectedByStockId = [];
        foreach ($collectedGroceries as $grocery) {
            if ($grocery['stock_item_id']) {
                $collectedByStockId[$grocery['stock_item_id']] = $grocery['total_collected'];
            }
        }

        $stockItems = GroceryStockItem::where('is_active', true)
            ->with(['transactions' => function($query) use ($term, $year) {
                $query->where('term', $term)->where('year', $year)->orderBy('transaction_date', 'desc');
            }])
            ->orderBy('name')
            ->get();

        // Calculate balances for each item
        foreach ($stockItems as $item) {
            $item->balance_bf = $this->getBalanceBroughtForward($item->id, $term, $year);
            // Received = automatically pulled from collected groceries
            $item->received = $collectedByStockId[$item->id] ?? 0;
            $item->usage = $item->transactions->where('type', 'usage')->sum('quantity');
            $item->bad_stock = $item->transactions->where('type', 'bad_stock')->sum('quantity');
            $item->closing_balance = $item->balance_bf + $item->received - $item->usage - $item->bad_stock;
        }

        $terms = ['term_1', 'term_2', 'term_3'];
        $years = range(date('Y') - 2, date('Y') + 1);

        // Get all active stock items for the dropdown (including manually added ones)
        $allStockItems = GroceryStockItem::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(function($item) use ($term, $year) {
                $usage = GroceryStockTransaction::where('stock_item_id', $item->id)
                    ->where('term', $term)
                    ->where('year', $year)
                    ->where('type', 'usage')
                    ->sum('quantity');
                $spoiled = GroceryStockTransaction::where('stock_item_id', $item->id)
                    ->where('term', $term)
                    ->where('year', $year)
                    ->where('type', 'bad_stock')
                    ->sum('quantity');
                $balance_bf = $this->getBalanceBroughtForward($item->id, $term, $year);
                $received = GroceryStockTransaction::where('stock_item_id', $item->id)
                    ->where('term', $term)
                    ->where('year', $year)
                    ->where('type', 'received')
                    ->sum('quantity');
                
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'unit' => $item->unit,
                    'is_manual' => $item->is_manual,
                    'balance' => $balance_bf + $received - $usage - $spoiled,
                    'received' => $received,
                ];
            });

        return view('backend.finance.grocery-stock.index', compact('stockItems', 'term', 'year', 'terms', 'years', 'collectedGroceries', 'allStockItems'));
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
                        'students_submitted' => 0,
                        'usage' => 0,
                        'spoiled' => 0,
                        'balance' => 0,
                        'stock_item_id' => null,
                        'is_extra' => false
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

            // Process extra items added by parents (not on original list)
            foreach ($list->responses as $response) {
                $extraItems = $response->extra_items ?? [];
                
                foreach ($extraItems as $extraItem) {
                    if (empty($extraItem['name'])) continue;
                    
                    $itemName = trim($extraItem['name']);
                    $quantity = floatval(preg_replace('/[^0-9.]/', '', $extraItem['quantity'] ?? '1'));
                    
                    if (!isset($collectedItems[$itemName])) {
                        $collectedItems[$itemName] = [
                            'name' => $itemName,
                            'required_per_student' => $extraItem['quantity'] ?? '1',
                            'total_required' => 0,
                            'total_collected' => 0,
                            'total_short' => 0,
                            'total_extra' => 0,
                            'students_submitted' => 0,
                            'usage' => 0,
                            'spoiled' => 0,
                            'balance' => 0,
                            'stock_item_id' => null,
                            'is_extra' => true
                        ];
                    }
                    
                    $collectedItems[$itemName]['students_submitted']++;
                    $collectedItems[$itemName]['total_collected'] += $quantity;
                    $collectedItems[$itemName]['is_extra'] = true;
                }
            }
        }

        // Get usage and spoiled data from stock transactions (match by item name)
        foreach ($collectedItems as $itemName => &$itemData) {
            // Find matching stock item by name (case-insensitive)
            $stockItem = GroceryStockItem::where('name', 'LIKE', $itemName)->first();
            if (!$stockItem) {
                $stockItem = GroceryStockItem::where('name', $itemName)->first();
            }
            
            // Auto-create stock item if it doesn't exist
            if (!$stockItem && $itemData['total_collected'] > 0) {
                // Extract unit from required_per_student (e.g., "2kg" -> "kg")
                $unit = preg_replace('/[0-9.]/', '', $itemData['required_per_student'] ?? '');
                $unit = trim($unit) ?: 'units';
                
                $stockItem = GroceryStockItem::create([
                    'name' => $itemName,
                    'unit' => $unit,
                    'current_balance' => 0,
                    'is_active' => true
                ]);
            }
            
            if ($stockItem) {
                $itemData['stock_item_id'] = $stockItem->id;
                $itemData['unit'] = $stockItem->unit;
                $itemData['description'] = $stockItem->description;
                
                // Get usage for this term/year
                $itemData['usage'] = GroceryStockTransaction::where('stock_item_id', $stockItem->id)
                    ->where('term', $term)
                    ->where('year', $year)
                    ->where('type', 'usage')
                    ->sum('quantity');
                
                // Get spoiled/bad stock for this term/year
                $itemData['spoiled'] = GroceryStockTransaction::where('stock_item_id', $stockItem->id)
                    ->where('term', $term)
                    ->where('year', $year)
                    ->where('type', 'bad_stock')
                    ->sum('quantity');
            }
            
            // Calculate balance: Collected - Usage - Spoiled
            $itemData['balance'] = $itemData['total_collected'] - $itemData['usage'] - $itemData['spoiled'];
        }

        // Add manually added stock items (from donors, etc.) that are not in collectedItems
        $manualItems = GroceryStockItem::where('is_active', true)
            ->where('is_manual', true)
            ->get();
        
        foreach ($manualItems as $manualItem) {
            // Check if this item is already in collectedItems
            $existsInCollected = false;
            foreach ($collectedItems as $itemName => $itemData) {
                if (strtolower($itemName) === strtolower($manualItem->name)) {
                    $existsInCollected = true;
                    break;
                }
            }
            
            if (!$existsInCollected) {
                // Get received transactions for this term/year
                $received = GroceryStockTransaction::where('stock_item_id', $manualItem->id)
                    ->where('term', $term)
                    ->where('year', $year)
                    ->where('type', 'received')
                    ->sum('quantity');
                
                // Get usage for this term/year
                $usage = GroceryStockTransaction::where('stock_item_id', $manualItem->id)
                    ->where('term', $term)
                    ->where('year', $year)
                    ->where('type', 'usage')
                    ->sum('quantity');
                
                // Get spoiled/bad stock for this term/year
                $spoiled = GroceryStockTransaction::where('stock_item_id', $manualItem->id)
                    ->where('term', $term)
                    ->where('year', $year)
                    ->where('type', 'bad_stock')
                    ->sum('quantity');
                
                // Get balance brought forward
                $balance_bf = $this->getBalanceBroughtForward($manualItem->id, $term, $year);
                
                // Total collected = balance_bf + received
                $totalCollected = $balance_bf + $received;
                
                $collectedItems[$manualItem->name] = [
                    'name' => $manualItem->name,
                    'description' => $manualItem->description,
                    'required_per_student' => '-',
                    'total_required' => 0,
                    'total_collected' => $totalCollected,
                    'total_short' => 0,
                    'total_extra' => 0,
                    'students_submitted' => 0,
                    'usage' => $usage,
                    'spoiled' => $spoiled,
                    'balance' => $totalCollected - $usage - $spoiled,
                    'stock_item_id' => $manualItem->id,
                    'unit' => $manualItem->unit,
                    'is_extra' => false,
                    'is_manual' => true
                ];
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
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'initial_quantity' => 'nullable|numeric|min:0'
        ]);

        $initialQty = $validated['initial_quantity'] ?? 0;
        unset($validated['initial_quantity']);
        
        $validated['current_balance'] = $initialQty;
        $validated['is_manual'] = true;
        $item = GroceryStockItem::create($validated);

        // Create initial balance transaction if quantity > 0
        if ($initialQty > 0) {
            GroceryStockTransaction::create([
                'stock_item_id' => $item->id,
                'type' => 'received',
                'quantity' => $initialQty,
                'balance_after' => $initialQty,
                'term' => $this->getCurrentTerm(),
                'year' => date('Y'),
                'description' => 'Initial stock quantity',
                'recorded_by' => Auth::id(),
                'transaction_date' => now()
            ]);
        }

        return redirect()->back()->with('success', 'Stock item added successfully!');
    }

    public function updateItem(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit' => 'required|string|max:50',
            'quantity' => 'nullable|numeric',
            'is_active' => 'boolean'
        ]);

        $item = GroceryStockItem::findOrFail($id);
        $newQuantity = $validated['quantity'] ?? $item->current_balance;
        unset($validated['quantity']);
        
        // Check if quantity changed - create adjustment transaction
        if ($newQuantity != $item->current_balance) {
            $difference = $newQuantity - $item->current_balance;
            
            GroceryStockTransaction::create([
                'stock_item_id' => $item->id,
                'type' => 'adjustment',
                'quantity' => $difference,
                'balance_after' => $newQuantity,
                'term' => $this->getCurrentTerm(),
                'year' => date('Y'),
                'description' => 'Stock adjustment via item edit',
                'recorded_by' => Auth::id(),
                'transaction_date' => now()
            ]);
            
            $item->current_balance = $newQuantity;
        }
        
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

    /**
     * Automatically carry forward grocery stock balances when a new term is created.
     * Called from ResultsStatusController when a new term is created.
     * 
     * @param string $newTerm The new term (first, second, third)
     * @param int $newYear The year for the new term
     * @return int Number of items carried forward
     */
    public static function autoCarryForwardForNewTerm($newTerm, $newYear)
    {
        // Map term format
        $termMap = [
            'first' => 'term_1',
            'second' => 'term_2',
            'third' => 'term_3',
            'term_1' => 'term_1',
            'term_2' => 'term_2',
            'term_3' => 'term_3'
        ];
        
        $toTerm = $termMap[$newTerm] ?? $newTerm;
        
        // Determine the previous term
        $previousTermMap = [
            'term_1' => ['term' => 'term_3', 'year' => $newYear - 1],
            'term_2' => ['term' => 'term_1', 'year' => $newYear],
            'term_3' => ['term' => 'term_2', 'year' => $newYear]
        ];
        
        $fromData = $previousTermMap[$toTerm] ?? null;
        if (!$fromData) {
            return 0;
        }
        
        $fromTerm = $fromData['term'];
        $fromYear = $fromData['year'];
        
        // Check if carry forward already exists for this term
        $existingCarryForward = GroceryStockTransaction::where('term', $toTerm)
            ->where('year', $newYear)
            ->where('type', 'balance_bf')
            ->exists();
        
        if ($existingCarryForward) {
            return 0; // Already carried forward
        }
        
        $stockItems = GroceryStockItem::where('is_active', true)->get();
        $itemsCarriedForward = 0;
        
        foreach ($stockItems as $item) {
            // Calculate closing balance from previous term
            $balanceBf = GroceryStockTransaction::where('stock_item_id', $item->id)
                ->where('term', $fromTerm)->where('year', $fromYear)->where('type', 'balance_bf')->sum('quantity');
            
            $received = GroceryStockTransaction::where('stock_item_id', $item->id)
                ->where('term', $fromTerm)->where('year', $fromYear)->where('type', 'received')->sum('quantity');
            
            $usage = GroceryStockTransaction::where('stock_item_id', $item->id)
                ->where('term', $fromTerm)->where('year', $fromYear)->where('type', 'usage')->sum('quantity');
            
            $badStock = GroceryStockTransaction::where('stock_item_id', $item->id)
                ->where('term', $fromTerm)->where('year', $fromYear)->where('type', 'bad_stock')->sum('quantity');
            
            $closingBalance = $balanceBf + $received - $usage - $badStock;
            
            // Also add current_balance for items that may not have previous term transactions
            if ($closingBalance <= 0 && $item->current_balance > 0) {
                $closingBalance = $item->current_balance;
            }
            
            if ($closingBalance > 0) {
                GroceryStockTransaction::create([
                    'stock_item_id' => $item->id,
                    'type' => 'balance_bf',
                    'quantity' => $closingBalance,
                    'balance_after' => $closingBalance,
                    'term' => $toTerm,
                    'year' => $newYear,
                    'description' => "Auto Balance B/F from {$fromTerm} {$fromYear}",
                    'recorded_by' => Auth::id() ?? 1,
                    'transaction_date' => now()->format('Y-m-d')
                ]);
                $itemsCarriedForward++;
            }
        }
        
        return $itemsCarriedForward;
    }
}
