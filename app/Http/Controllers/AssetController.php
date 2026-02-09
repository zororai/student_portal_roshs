<?php

namespace App\Http\Controllers;

use App\Asset;
use App\AssetCategory;
use App\AssetLocation;
use App\AssetMaintenance;
use App\AssetDepreciation;
use App\AssetAssignmentHistory;
use App\AuditTrail;
use App\User;
use App\Teacher;
use App\Student;
use App\Grade;
use App\Services\AssetManagementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AssetController extends Controller
{
    protected $assetService;

    public function __construct(AssetManagementService $assetService)
    {
        $this->assetService = $assetService;
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Asset::with(['category', 'location', 'creator']);

        // Filters
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('asset_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        $assets = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = AssetCategory::where('is_active', true)->get();
        $locations = AssetLocation::where('is_active', true)->get();

        return view('assets.index', compact('assets', 'categories', 'locations'));
    }

    public function create()
    {
        $categories = AssetCategory::where('is_active', true)->get();
        $locations = AssetLocation::where('is_active', true)->get();

        return view('assets.create', compact('categories', 'locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'serial_number' => 'nullable|string|max:255',
            'purchase_date' => 'required|date',
            'purchase_cost' => 'required|numeric|min:0',
            'residual_value' => 'nullable|numeric|min:0',
            'condition' => 'required|in:new,good,fair,damaged',
            'location_id' => 'nullable|exists:asset_locations,id',
            'notes' => 'nullable|string',
        ]);

        try {
            $asset = $this->assetService->createAsset($validated);

            return redirect()->route('assets.show', $asset)
                ->with('success', "Asset {$asset->asset_code} created successfully.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create asset: ' . $e->getMessage());
        }
    }

    public function show(Asset $asset)
    {
        $asset->load([
            'category',
            'location',
            'creator',
            'purchaseOrder',
            'maintenances' => function ($q) {
                $q->orderBy('reported_date', 'desc');
            },
            'depreciations' => function ($q) {
                $q->orderBy('year', 'desc');
            },
            'assignmentHistories' => function ($q) {
                $q->orderBy('assigned_at', 'desc');
            },
        ]);

        $depreciationSchedule = $this->assetService->getDepreciationSchedule($asset);

        return view('assets.show', compact('asset', 'depreciationSchedule'));
    }

    public function edit(Asset $asset)
    {
        if ($asset->isDisposed()) {
            return back()->with('error', 'Cannot edit a disposed asset.');
        }

        $categories = AssetCategory::where('is_active', true)->get();
        $locations = AssetLocation::where('is_active', true)->get();

        return view('assets.edit', compact('asset', 'categories', 'locations'));
    }

    public function update(Request $request, Asset $asset)
    {
        if ($asset->isDisposed()) {
            return back()->with('error', 'Cannot update a disposed asset.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:asset_categories,id',
            'serial_number' => 'nullable|string|max:255',
            'condition' => 'required|in:new,good,fair,damaged',
            'location_id' => 'nullable|exists:asset_locations,id',
            'notes' => 'nullable|string',
        ]);

        $oldValues = $asset->toArray();
        $asset->update($validated);

        AuditTrail::log('update', "Asset {$asset->asset_code} updated", $asset, $oldValues, $asset->toArray());

        return redirect()->route('assets.show', $asset)
            ->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->isDisposed()) {
            return back()->with('error', 'Asset is already disposed.');
        }

        // Soft delete - mark as disposed without reason
        $asset->status = 'disposed';
        $asset->disposed_at = now();
        $asset->save();

        AuditTrail::log('delete', "Asset {$asset->asset_code} deleted", $asset);

        return redirect()->route('assets.index')
            ->with('success', 'Asset deleted successfully.');
    }

    public function assign(Request $request, Asset $asset)
    {
        if (!$asset->canBeAssigned()) {
            return back()->with('error', 'Asset cannot be assigned in its current state.');
        }

        $validated = $request->validate([
            'assigned_type' => 'required|in:user,teacher,student,class',
            'assigned_id' => 'required|integer',
            'notes' => 'nullable|string',
        ]);

        try {
            $this->assetService->assignAsset(
                $asset,
                $validated['assigned_type'],
                $validated['assigned_id'],
                $validated['notes'] ?? null
            );

            return redirect()->route('assets.show', $asset)
                ->with('success', 'Asset assigned successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to assign asset: ' . $e->getMessage());
        }
    }

    public function unassign(Asset $asset)
    {
        try {
            $this->assetService->unassignAsset($asset);

            return redirect()->route('assets.show', $asset)
                ->with('success', 'Asset unassigned successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to unassign asset: ' . $e->getMessage());
        }
    }

    public function showAssignForm(Asset $asset)
    {
        if (!$asset->canBeAssigned()) {
            return back()->with('error', 'Asset cannot be assigned in its current state.');
        }

        $users = User::where('is_active', true)->get();
        $teachers = Teacher::all();
        $students = Student::where('is_transferred', false)->get();
        $classes = Grade::all();

        return view('assets.assign', compact('asset', 'users', 'teachers', 'students', 'classes'));
    }

    public function showDisposeForm(Asset $asset)
    {
        if ($asset->isDisposed()) {
            return back()->with('error', 'Asset is already disposed.');
        }

        return view('assets.dispose', compact('asset'));
    }

    public function dispose(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'disposal_reason' => 'required|string|max:500',
            'disposal_value' => 'nullable|numeric|min:0',
        ]);

        try {
            $this->assetService->disposeAsset(
                $asset,
                $validated['disposal_reason'],
                $validated['disposal_value'] ?? 0
            );

            return redirect()->route('assets.show', $asset)
                ->with('success', 'Asset disposed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to dispose asset: ' . $e->getMessage());
        }
    }

    public function categories()
    {
        $categories = AssetCategory::withCount('assets')->get();
        return view('assets.categories.index', compact('categories'));
    }

    public function createCategory()
    {
        return view('assets.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:20|unique:asset_categories,code',
            'description' => 'nullable|string',
            'useful_life_years' => 'required|integer|min:1|max:50',
            'depreciation_method' => 'required|in:straight_line,reducing_balance',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = AssetCategory::generateCode($validated['name']);
        }

        $category = AssetCategory::create($validated);

        AuditTrail::log('create', "Asset category {$category->name} created", $category);

        return redirect()->route('assets.categories')
            ->with('success', 'Category created successfully.');
    }

    public function editCategory(AssetCategory $category)
    {
        return view('assets.categories.edit', compact('category'));
    }

    public function updateCategory(Request $request, AssetCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'useful_life_years' => 'required|integer|min:1|max:50',
            'depreciation_method' => 'required|in:straight_line,reducing_balance',
            'is_active' => 'boolean',
        ]);

        $oldValues = $category->toArray();
        $category->update($validated);

        AuditTrail::log('update', "Asset category {$category->name} updated", $category, $oldValues, $category->toArray());

        return redirect()->route('assets.categories')
            ->with('success', 'Category updated successfully.');
    }

    public function locations()
    {
        $locations = AssetLocation::withCount('assets')->get();
        return view('assets.locations.index', compact('locations'));
    }

    public function createLocation()
    {
        return view('assets.locations.create');
    }

    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $location = AssetLocation::create($validated);

        AuditTrail::log('create', "Asset location {$location->name} created", $location);

        return redirect()->route('assets.locations')
            ->with('success', 'Location created successfully.');
    }

    public function editLocation(AssetLocation $location)
    {
        return view('assets.locations.edit', compact('location'));
    }

    public function updateLocation(Request $request, AssetLocation $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'building' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $oldValues = $location->toArray();
        $location->update($validated);

        AuditTrail::log('update', "Asset location {$location->name} updated", $location, $oldValues, $location->toArray());

        return redirect()->route('assets.locations')
            ->with('success', 'Location updated successfully.');
    }

    public function maintenance(Request $request)
    {
        $query = AssetMaintenance::with(['asset', 'performer', 'approver']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('maintenance_type', $request->type);
        }
        if ($request->filled('asset_id')) {
            $query->where('asset_id', $request->asset_id);
        }

        $maintenances = $query->orderBy('reported_date', 'desc')->paginate(20);

        return view('assets.maintenance.index', compact('maintenances'));
    }

    public function createMaintenance(Asset $asset)
    {
        return view('assets.maintenance.create', compact('asset'));
    }

    public function storeMaintenance(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'maintenance_type' => 'required|in:repair,service,inspection',
            'description' => 'required|string',
            'reported_date' => 'required|date',
            'scheduled_date' => 'nullable|date|after_or_equal:reported_date',
            'cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        try {
            $maintenance = $this->assetService->createMaintenance($asset, $validated);

            return redirect()->route('assets.show', $asset)
                ->with('success', 'Maintenance record created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to create maintenance record: ' . $e->getMessage());
        }
    }

    public function completeMaintenance(Request $request, AssetMaintenance $maintenance)
    {
        $validated = $request->validate([
            'completed_date' => 'required|date',
            'cost' => 'nullable|numeric|min:0',
            'new_condition' => 'nullable|in:new,good,fair,damaged',
            'notes' => 'nullable|string',
        ]);

        try {
            $this->assetService->completeMaintenance($maintenance, $validated);

            return redirect()->route('assets.show', $maintenance->asset)
                ->with('success', 'Maintenance completed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to complete maintenance: ' . $e->getMessage());
        }
    }

    public function depreciation(Request $request)
    {
        $year = $request->input('year', now()->year);
        
        $depreciations = AssetDepreciation::with(['asset.category'])
            ->where('year', $year)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $years = AssetDepreciation::selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        $summary = [
            'total_depreciation' => AssetDepreciation::where('year', $year)->sum('depreciation_amount'),
            'posted_count' => AssetDepreciation::where('year', $year)->where('posted_to_ledger', true)->count(),
            'pending_count' => AssetDepreciation::where('year', $year)->where('posted_to_ledger', false)->count(),
        ];

        return view('assets.depreciation.index', compact('depreciations', 'year', 'years', 'summary'));
    }

    public function runDepreciation(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer|min:2000|max:' . (now()->year + 1),
        ]);

        try {
            $results = $this->assetService->runAnnualDepreciation($validated['year']);

            return redirect()->route('assets.depreciation', ['year' => $validated['year']])
                ->with('success', "Depreciation calculated for {$results['processed']} assets. {$results['skipped']} skipped.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to run depreciation: ' . $e->getMessage());
        }
    }

    public function postDepreciation(AssetDepreciation $depreciation)
    {
        try {
            $this->assetService->postDepreciationToLedger($depreciation);

            return back()->with('success', 'Depreciation posted to ledger successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to post depreciation: ' . $e->getMessage());
        }
    }

    public function postAllDepreciation(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|integer',
        ]);

        $depreciations = AssetDepreciation::where('year', $validated['year'])
            ->where('posted_to_ledger', false)
            ->get();

        $posted = 0;
        $errors = 0;

        foreach ($depreciations as $depreciation) {
            try {
                $this->assetService->postDepreciationToLedger($depreciation);
                $posted++;
            } catch (\Exception $e) {
                $errors++;
            }
        }

        return redirect()->route('assets.depreciation', ['year' => $validated['year']])
            ->with('success', "{$posted} depreciation records posted. {$errors} errors.");
    }

    public function reports()
    {
        $summary = $this->assetService->getAssetValuationSummary();
        
        return view('assets.reports.index', compact('summary'));
    }

    public function assetRegister(Request $request)
    {
        $query = Asset::with(['category', 'location']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $assets = $query->orderBy('asset_code')->get();
        $categories = AssetCategory::all();

        return view('assets.reports.register', compact('assets', 'categories'));
    }

    public function depreciationScheduleReport(Request $request)
    {
        $year = $request->input('year', now()->year);
        
        $assets = Asset::with(['category', 'depreciations'])
            ->where('status', '!=', 'disposed')
            ->get()
            ->map(function ($asset) {
                $asset->depreciation_schedule = $this->assetService->getDepreciationSchedule($asset);
                return $asset;
            });

        return view('assets.reports.depreciation-schedule', compact('assets', 'year'));
    }

    public function maintenanceCostReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        $maintenances = AssetMaintenance::with(['asset.category'])
            ->whereBetween('completed_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $summary = [
            'total_cost' => $maintenances->sum('cost'),
            'by_type' => $maintenances->groupBy('maintenance_type')->map->sum('cost'),
            'by_category' => $maintenances->groupBy('asset.category.name')->map->sum('cost'),
        ];

        return view('assets.reports.maintenance-cost', compact('maintenances', 'summary', 'startDate', 'endDate'));
    }

    public function disposedAssetsReport()
    {
        $disposedAssets = Asset::with(['category', 'location'])
            ->where('status', 'disposed')
            ->orderBy('disposed_at', 'desc')
            ->get();

        $summary = [
            'total_count' => $disposedAssets->count(),
            'total_purchase_cost' => $disposedAssets->sum('purchase_cost'),
            'total_disposal_value' => $disposedAssets->sum('disposal_value'),
        ];

        return view('assets.reports.disposed', compact('disposedAssets', 'summary'));
    }

    public function assetsByLocation()
    {
        $locations = AssetLocation::with(['assets' => function ($q) {
            $q->where('status', 'active')->with('category');
        }])->get();

        return view('assets.reports.by-location', compact('locations'));
    }
}
