<?php

namespace App\Services;

use App\Asset;
use App\AssetCategory;
use App\AssetDepreciation;
use App\AssetAssignmentHistory;
use App\AssetMaintenance;
use App\LedgerAccount;
use App\LedgerEntry;
use App\AuditTrail;
use App\PurchaseOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AssetManagementService
{
    /**
     * Create a new asset from a purchase order
     */
    public function createAssetFromPurchaseOrder(PurchaseOrder $purchaseOrder, array $assetData)
    {
        return DB::transaction(function () use ($purchaseOrder, $assetData) {
            $category = AssetCategory::findOrFail($assetData['category_id']);
            
            $asset = Asset::create([
                'asset_code' => Asset::generateAssetCode($category->code),
                'name' => $assetData['name'],
                'category_id' => $category->id,
                'serial_number' => $assetData['serial_number'] ?? null,
                'purchase_date' => $purchaseOrder->order_date,
                'purchase_cost' => $assetData['purchase_cost'],
                'residual_value' => $assetData['residual_value'] ?? 0,
                'current_value' => $assetData['purchase_cost'],
                'condition' => 'new',
                'status' => 'active',
                'location_id' => $assetData['location_id'] ?? null,
                'purchase_order_id' => $purchaseOrder->id,
                'notes' => $assetData['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Post asset purchase to ledger
            $this->postAssetPurchaseToLedger($asset);

            // Log audit trail
            AuditTrail::log('create', "Asset {$asset->asset_code} created from PO {$purchaseOrder->po_number}", $asset);

            return $asset;
        });
    }

    /**
     * Create a standalone asset (not from PO)
     */
    public function createAsset(array $assetData)
    {
        return DB::transaction(function () use ($assetData) {
            $category = AssetCategory::findOrFail($assetData['category_id']);
            
            $asset = Asset::create([
                'asset_code' => Asset::generateAssetCode($category->code),
                'name' => $assetData['name'],
                'category_id' => $category->id,
                'serial_number' => $assetData['serial_number'] ?? null,
                'purchase_date' => $assetData['purchase_date'],
                'purchase_cost' => $assetData['purchase_cost'],
                'residual_value' => $assetData['residual_value'] ?? 0,
                'current_value' => $assetData['purchase_cost'],
                'condition' => $assetData['condition'] ?? 'new',
                'status' => 'active',
                'location_id' => $assetData['location_id'] ?? null,
                'notes' => $assetData['notes'] ?? null,
                'created_by' => Auth::id(),
            ]);

            // Post asset purchase to ledger
            $this->postAssetPurchaseToLedger($asset);

            // Log audit trail
            AuditTrail::log('create', "Asset {$asset->asset_code} created", $asset);

            return $asset;
        });
    }

    /**
     * Post asset purchase to ledger (double-entry)
     * Debit: Asset Account
     * Credit: Cash/Bank Account
     */
    public function postAssetPurchaseToLedger(Asset $asset)
    {
        // Find or create Fixed Assets account
        $assetAccount = LedgerAccount::firstOrCreate(
            ['account_code' => '1200'],
            [
                'account_name' => 'Fixed Assets',
                'account_type' => 'asset',
                'category' => 'Non-Current Assets',
                'description' => 'School fixed assets',
                'opening_balance' => 0,
                'current_balance' => 0,
            ]
        );

        // Find or create Cash account
        $cashAccount = LedgerAccount::firstOrCreate(
            ['account_code' => '1001'],
            [
                'account_name' => 'Cash',
                'account_type' => 'asset',
                'category' => 'Current Assets',
                'description' => 'Cash on hand and in bank',
                'opening_balance' => 0,
                'current_balance' => 0,
            ]
        );

        $referenceNumber = LedgerEntry::generateReferenceNumber();

        // Debit Fixed Assets
        LedgerEntry::create([
            'entry_date' => $asset->purchase_date,
            'term' => $this->getCurrentTerm(),
            'year' => $asset->purchase_date->year,
            'reference_number' => $referenceNumber,
            'account_id' => $assetAccount->id,
            'entry_type' => 'debit',
            'amount' => $asset->purchase_cost,
            'description' => "Asset purchase: {$asset->name} ({$asset->asset_code})",
            'created_by' => Auth::id() ?? 1,
            'notes' => 'Auto-posted from Asset Management',
        ]);

        // Credit Cash
        LedgerEntry::create([
            'entry_date' => $asset->purchase_date,
            'term' => $this->getCurrentTerm(),
            'year' => $asset->purchase_date->year,
            'reference_number' => $referenceNumber,
            'account_id' => $cashAccount->id,
            'entry_type' => 'credit',
            'amount' => $asset->purchase_cost,
            'description' => "Asset purchase: {$asset->name} ({$asset->asset_code})",
            'created_by' => Auth::id() ?? 1,
            'notes' => 'Auto-posted from Asset Management',
        ]);

        // Update account balances
        $assetAccount->updateBalance();
        $cashAccount->updateBalance();
    }

    /**
     * Calculate and record depreciation for a specific year
     */
    public function calculateDepreciation(Asset $asset, int $year)
    {
        if ($asset->isDisposed()) {
            return null;
        }

        // Check if depreciation already exists for this year
        $existingDepreciation = AssetDepreciation::where('asset_id', $asset->id)
            ->where('year', $year)
            ->first();

        if ($existingDepreciation) {
            return $existingDepreciation;
        }

        $category = $asset->category;
        if (!$category) {
            return null;
        }

        // Get last depreciation or use purchase cost
        $lastDepreciation = AssetDepreciation::where('asset_id', $asset->id)
            ->orderBy('year', 'desc')
            ->first();

        $openingValue = $lastDepreciation 
            ? $lastDepreciation->closing_value 
            : $asset->purchase_cost;

        // Don't depreciate below residual value
        if ($openingValue <= $asset->residual_value) {
            return null;
        }

        // Calculate depreciation based on method
        if ($category->depreciation_method === 'straight_line') {
            $annualDepreciation = ($asset->purchase_cost - $asset->residual_value) / $category->useful_life_years;
        } else {
            // Reducing balance (double declining balance)
            $rate = 2 / $category->useful_life_years;
            $annualDepreciation = $openingValue * $rate;
        }

        // Ensure we don't depreciate below residual value
        $closingValue = max($asset->residual_value, $openingValue - $annualDepreciation);
        $actualDepreciation = $openingValue - $closingValue;

        $depreciation = AssetDepreciation::create([
            'asset_id' => $asset->id,
            'year' => $year,
            'opening_value' => $openingValue,
            'depreciation_amount' => $actualDepreciation,
            'closing_value' => $closingValue,
            'posted_to_ledger' => false,
        ]);

        // Update asset current value
        $asset->current_value = $closingValue;
        $asset->save();

        return $depreciation;
    }

    /**
     * Post depreciation to ledger
     * Debit: Depreciation Expense
     * Credit: Accumulated Depreciation
     */
    public function postDepreciationToLedger(AssetDepreciation $depreciation)
    {
        if ($depreciation->posted_to_ledger) {
            throw new \Exception('Depreciation already posted to ledger');
        }

        return DB::transaction(function () use ($depreciation) {
            $asset = $depreciation->asset;

            // Find or create Depreciation Expense account
            $depreciationExpenseAccount = LedgerAccount::firstOrCreate(
                ['account_code' => '5010'],
                [
                    'account_name' => 'Depreciation Expense',
                    'account_type' => 'expense',
                    'category' => 'Operating Expenses',
                    'description' => 'Annual depreciation expense for fixed assets',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                ]
            );

            // Find or create Accumulated Depreciation account (contra-asset)
            $accumulatedDepreciationAccount = LedgerAccount::firstOrCreate(
                ['account_code' => '1201'],
                [
                    'account_name' => 'Accumulated Depreciation',
                    'account_type' => 'asset',
                    'category' => 'Non-Current Assets',
                    'description' => 'Accumulated depreciation on fixed assets (contra-asset)',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                ]
            );

            $referenceNumber = LedgerEntry::generateReferenceNumber();

            // Debit Depreciation Expense
            $debitEntry = LedgerEntry::create([
                'entry_date' => Carbon::create($depreciation->year, 12, 31),
                'term' => 3,
                'year' => $depreciation->year,
                'reference_number' => $referenceNumber,
                'account_id' => $depreciationExpenseAccount->id,
                'entry_type' => 'debit',
                'amount' => $depreciation->depreciation_amount,
                'description' => "Depreciation: {$asset->name} ({$asset->asset_code}) - Year {$depreciation->year}",
                'created_by' => Auth::id() ?? 1,
                'notes' => 'Auto-posted depreciation',
            ]);

            // Credit Accumulated Depreciation
            LedgerEntry::create([
                'entry_date' => Carbon::create($depreciation->year, 12, 31),
                'term' => 3,
                'year' => $depreciation->year,
                'reference_number' => $referenceNumber,
                'account_id' => $accumulatedDepreciationAccount->id,
                'entry_type' => 'credit',
                'amount' => $depreciation->depreciation_amount,
                'description' => "Depreciation: {$asset->name} ({$asset->asset_code}) - Year {$depreciation->year}",
                'created_by' => Auth::id() ?? 1,
                'notes' => 'Auto-posted depreciation',
            ]);

            // Mark depreciation as posted
            $depreciation->posted_to_ledger = true;
            $depreciation->ledger_entry_id = $debitEntry->id;
            $depreciation->save();

            // Update account balances
            $depreciationExpenseAccount->updateBalance();
            $accumulatedDepreciationAccount->updateBalance();

            // Log audit trail
            AuditTrail::log('create', "Depreciation posted for {$asset->asset_code} - Year {$depreciation->year}", $depreciation);

            return $depreciation;
        });
    }

    /**
     * Run depreciation for all active assets for a specific year
     */
    public function runAnnualDepreciation(int $year)
    {
        $assets = Asset::where('status', 'active')
            ->whereYear('purchase_date', '<=', $year)
            ->get();

        $results = [
            'processed' => 0,
            'skipped' => 0,
            'depreciations' => [],
        ];

        foreach ($assets as $asset) {
            $depreciation = $this->calculateDepreciation($asset, $year);
            
            if ($depreciation) {
                $results['processed']++;
                $results['depreciations'][] = $depreciation;
            } else {
                $results['skipped']++;
            }
        }

        return $results;
    }

    /**
     * Assign an asset to an entity
     */
    public function assignAsset(Asset $asset, ?string $toType, ?int $toId, ?string $notes = null)
    {
        if (!$asset->canBeAssigned()) {
            throw new \Exception('Asset cannot be assigned in current state');
        }

        if ($asset->isDisposed()) {
            throw new \Exception('Cannot assign a disposed asset');
        }

        return DB::transaction(function () use ($asset, $toType, $toId, $notes) {
            // Record assignment history
            AssetAssignmentHistory::create([
                'asset_id' => $asset->id,
                'from_type' => $asset->assigned_type,
                'from_id' => $asset->assigned_id,
                'to_type' => $toType,
                'to_id' => $toId,
                'assigned_by' => Auth::id(),
                'assigned_at' => now(),
                'notes' => $notes,
            ]);

            // Update asset
            $asset->assigned_type = $toType;
            $asset->assigned_id = $toId;
            $asset->save();

            // Log audit trail
            AuditTrail::log('update', "Asset {$asset->asset_code} assigned", $asset);

            return $asset;
        });
    }

    /**
     * Unassign an asset
     */
    public function unassignAsset(Asset $asset, ?string $notes = null)
    {
        return $this->assignAsset($asset, null, null, $notes);
    }

    /**
     * Dispose an asset
     */
    public function disposeAsset(Asset $asset, string $reason, ?float $disposalValue = null)
    {
        if ($asset->isDisposed()) {
            throw new \Exception('Asset is already disposed');
        }

        return DB::transaction(function () use ($asset, $reason, $disposalValue) {
            // Unassign if currently assigned
            if ($asset->assigned_type && $asset->assigned_id) {
                AssetAssignmentHistory::create([
                    'asset_id' => $asset->id,
                    'from_type' => $asset->assigned_type,
                    'from_id' => $asset->assigned_id,
                    'to_type' => null,
                    'to_id' => null,
                    'assigned_by' => Auth::id(),
                    'assigned_at' => now(),
                    'notes' => 'Unassigned due to disposal',
                ]);
            }

            $asset->status = 'disposed';
            $asset->disposed_at = now();
            $asset->disposal_reason = $reason;
            $asset->disposal_value = $disposalValue ?? 0;
            $asset->assigned_type = null;
            $asset->assigned_id = null;
            $asset->save();

            // Post disposal to ledger
            $this->postDisposalToLedger($asset);

            // Log audit trail
            AuditTrail::log('update', "Asset {$asset->asset_code} disposed - Reason: {$reason}", $asset);

            return $asset;
        });
    }

    /**
     * Post asset disposal to ledger
     */
    protected function postDisposalToLedger(Asset $asset)
    {
        // Find accounts
        $fixedAssetsAccount = LedgerAccount::where('account_code', '1200')->first();
        $accumulatedDepreciationAccount = LedgerAccount::where('account_code', '1201')->first();
        
        if (!$fixedAssetsAccount || !$accumulatedDepreciationAccount) {
            return; // Accounts not set up
        }

        // Calculate accumulated depreciation
        $totalDepreciation = $asset->depreciations()->sum('depreciation_amount');
        $bookValue = $asset->purchase_cost - $totalDepreciation;
        $gainLoss = ($asset->disposal_value ?? 0) - $bookValue;

        $referenceNumber = LedgerEntry::generateReferenceNumber();

        // Remove from Fixed Assets (Credit)
        LedgerEntry::create([
            'entry_date' => now(),
            'term' => $this->getCurrentTerm(),
            'year' => now()->year,
            'reference_number' => $referenceNumber,
            'account_id' => $fixedAssetsAccount->id,
            'entry_type' => 'credit',
            'amount' => $asset->purchase_cost,
            'description' => "Asset disposal: {$asset->name} ({$asset->asset_code})",
            'created_by' => Auth::id() ?? 1,
            'notes' => 'Auto-posted from Asset Disposal',
        ]);

        // Remove Accumulated Depreciation (Debit)
        LedgerEntry::create([
            'entry_date' => now(),
            'term' => $this->getCurrentTerm(),
            'year' => now()->year,
            'reference_number' => $referenceNumber,
            'account_id' => $accumulatedDepreciationAccount->id,
            'entry_type' => 'debit',
            'amount' => $totalDepreciation,
            'description' => "Asset disposal: {$asset->name} ({$asset->asset_code})",
            'created_by' => Auth::id() ?? 1,
            'notes' => 'Auto-posted from Asset Disposal',
        ]);

        // Record gain/loss if any
        if ($gainLoss != 0) {
            $gainLossAccount = LedgerAccount::firstOrCreate(
                ['account_code' => $gainLoss > 0 ? '4010' : '5011'],
                [
                    'account_name' => $gainLoss > 0 ? 'Gain on Asset Disposal' : 'Loss on Asset Disposal',
                    'account_type' => $gainLoss > 0 ? 'income' : 'expense',
                    'category' => 'Other',
                    'description' => 'Gain or loss from asset disposal',
                    'opening_balance' => 0,
                    'current_balance' => 0,
                ]
            );

            LedgerEntry::create([
                'entry_date' => now(),
                'term' => $this->getCurrentTerm(),
                'year' => now()->year,
                'reference_number' => $referenceNumber,
                'account_id' => $gainLossAccount->id,
                'entry_type' => $gainLoss > 0 ? 'credit' : 'debit',
                'amount' => abs($gainLoss),
                'description' => "Gain/Loss on disposal: {$asset->name} ({$asset->asset_code})",
                'created_by' => Auth::id() ?? 1,
                'notes' => 'Auto-posted from Asset Disposal',
            ]);

            $gainLossAccount->updateBalance();
        }

        // If there's disposal proceeds
        if ($asset->disposal_value > 0) {
            $cashAccount = LedgerAccount::where('account_code', '1001')->first();
            if ($cashAccount) {
                LedgerEntry::create([
                    'entry_date' => now(),
                    'term' => $this->getCurrentTerm(),
                    'year' => now()->year,
                    'reference_number' => $referenceNumber,
                    'account_id' => $cashAccount->id,
                    'entry_type' => 'debit',
                    'amount' => $asset->disposal_value,
                    'description' => "Proceeds from asset disposal: {$asset->name} ({$asset->asset_code})",
                    'created_by' => Auth::id() ?? 1,
                    'notes' => 'Auto-posted from Asset Disposal',
                ]);
                $cashAccount->updateBalance();
            }
        }

        // Update account balances
        $fixedAssetsAccount->updateBalance();
        $accumulatedDepreciationAccount->updateBalance();
    }

    /**
     * Create a maintenance record
     */
    public function createMaintenance(Asset $asset, array $data)
    {
        return DB::transaction(function () use ($asset, $data) {
            $maintenance = AssetMaintenance::create([
                'asset_id' => $asset->id,
                'maintenance_type' => $data['maintenance_type'],
                'description' => $data['description'],
                'reported_date' => $data['reported_date'] ?? now(),
                'scheduled_date' => $data['scheduled_date'] ?? null,
                'cost' => $data['cost'] ?? 0,
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
            ]);

            // Update asset status if major maintenance
            if ($data['maintenance_type'] === 'repair') {
                $asset->status = 'under_maintenance';
                $asset->save();
            }

            // Log audit trail
            AuditTrail::log('create', "Maintenance record created for {$asset->asset_code}", $maintenance);

            return $maintenance;
        });
    }

    /**
     * Complete a maintenance record
     */
    public function completeMaintenance(AssetMaintenance $maintenance, array $data = [])
    {
        return DB::transaction(function () use ($maintenance, $data) {
            $maintenance->status = 'completed';
            $maintenance->completed_date = $data['completed_date'] ?? now();
            $maintenance->cost = $data['cost'] ?? $maintenance->cost;
            $maintenance->performed_by = $data['performed_by'] ?? Auth::id();
            $maintenance->notes = $data['notes'] ?? $maintenance->notes;
            $maintenance->save();

            // Restore asset to active if it was under maintenance
            $asset = $maintenance->asset;
            if ($asset->status === 'under_maintenance') {
                $asset->status = 'active';
                $asset->save();
            }

            // Update asset condition if specified
            if (isset($data['new_condition'])) {
                $asset->condition = $data['new_condition'];
                $asset->save();
            }

            // Log audit trail
            AuditTrail::log('update', "Maintenance completed for {$asset->asset_code}", $maintenance);

            return $maintenance;
        });
    }

    /**
     * Get current term (helper method)
     */
    protected function getCurrentTerm()
    {
        $month = now()->month;
        if ($month >= 1 && $month <= 4) return 1;
        if ($month >= 5 && $month <= 8) return 2;
        return 3;
    }

    /**
     * Get asset valuation summary
     */
    public function getAssetValuationSummary()
    {
        $assets = Asset::with('category')->where('status', 'active')->get();

        return [
            'total_purchase_cost' => $assets->sum('purchase_cost'),
            'total_current_value' => $assets->sum('current_value'),
            'total_depreciation' => $assets->sum('purchase_cost') - $assets->sum('current_value'),
            'asset_count' => $assets->count(),
            'by_category' => $assets->groupBy('category.name')->map(function ($categoryAssets) {
                return [
                    'count' => $categoryAssets->count(),
                    'purchase_cost' => $categoryAssets->sum('purchase_cost'),
                    'current_value' => $categoryAssets->sum('current_value'),
                ];
            }),
        ];
    }

    /**
     * Get depreciation schedule for an asset
     */
    public function getDepreciationSchedule(Asset $asset)
    {
        $category = $asset->category;
        if (!$category) {
            return [];
        }

        $schedule = [];
        $currentValue = $asset->purchase_cost;
        $purchaseYear = $asset->purchase_date->year;

        for ($i = 0; $i < $category->useful_life_years; $i++) {
            $year = $purchaseYear + $i;
            
            // Check for actual depreciation record
            $actualDepreciation = $asset->depreciations()->where('year', $year)->first();

            if ($actualDepreciation) {
                $schedule[] = [
                    'year' => $year,
                    'opening_value' => $actualDepreciation->opening_value,
                    'depreciation' => $actualDepreciation->depreciation_amount,
                    'closing_value' => $actualDepreciation->closing_value,
                    'posted' => $actualDepreciation->posted_to_ledger,
                    'actual' => true,
                ];
                $currentValue = $actualDepreciation->closing_value;
            } else {
                // Calculate projected depreciation
                if ($category->depreciation_method === 'straight_line') {
                    $depreciation = ($asset->purchase_cost - $asset->residual_value) / $category->useful_life_years;
                } else {
                    $rate = 2 / $category->useful_life_years;
                    $depreciation = $currentValue * $rate;
                }

                $closingValue = max($asset->residual_value, $currentValue - $depreciation);
                $actualDepreciation = $currentValue - $closingValue;

                $schedule[] = [
                    'year' => $year,
                    'opening_value' => $currentValue,
                    'depreciation' => $actualDepreciation,
                    'closing_value' => $closingValue,
                    'posted' => false,
                    'actual' => false,
                ];

                $currentValue = $closingValue;
            }

            if ($currentValue <= $asset->residual_value) {
                break;
            }
        }

        return $schedule;
    }
}
