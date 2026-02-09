<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\JournalBatch;
use App\JournalEntry;
use App\LedgerAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    /**
     * Display a listing of journal batches
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        
        $query = JournalBatch::with(['creator', 'entries'])
            ->orderBy('created_at', 'desc');
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $batches = $query->paginate(20);
        
        return view('backend.finance.journals.index', compact('batches', 'status'));
    }

    /**
     * Show the form for creating a new journal batch
     */
    public function create()
    {
        $accounts = LedgerAccount::where('is_active', true)
            ->orderBy('account_code')
            ->get();
        
        return view('backend.finance.journals.create', compact('accounts'));
    }

    /**
     * Store a newly created journal batch
     */
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:ledger_accounts,id',
            'entries.*.debit' => 'nullable|numeric|min:0',
            'entries.*.credit' => 'nullable|numeric|min:0',
            'entries.*.narration' => 'required|string',
        ]);

        DB::beginTransaction();
        
        try {
            // Create journal batch
            $batch = JournalBatch::create([
                'reference' => JournalBatch::generateReference(),
                'description' => $request->description,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            // Create journal entries
            foreach ($request->entries as $entryData) {
                $debit = $entryData['debit'] ?? 0;
                $credit = $entryData['credit'] ?? 0;
                
                // Skip if both are zero
                if ($debit == 0 && $credit == 0) {
                    continue;
                }
                
                JournalEntry::create([
                    'journal_batch_id' => $batch->id,
                    'ledger_account_id' => $entryData['account_id'],
                    'debit_amount' => $debit,
                    'credit_amount' => $credit,
                    'narration' => $entryData['narration'],
                ]);
            }

            // Calculate totals
            $batch->calculateTotals();

            DB::commit();

            return redirect()->route('finance.journals.show', $batch->id)
                ->with('success', 'Journal batch created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create journal batch: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified journal batch
     */
    public function show($id)
    {
        $batch = JournalBatch::with(['entries.ledgerAccount', 'creator', 'approver', 'poster'])
            ->findOrFail($id);
        
        return view('backend.finance.journals.show', compact('batch'));
    }

    /**
     * Show the form for editing the specified journal batch
     */
    public function edit($id)
    {
        $batch = JournalBatch::with('entries.ledgerAccount')->findOrFail($id);
        
        if (!$batch->canEdit()) {
            return redirect()->route('finance.journals.show', $batch->id)
                ->with('error', 'Only draft journals can be edited');
        }
        
        $accounts = LedgerAccount::where('is_active', true)
            ->orderBy('account_code')
            ->get();
        
        return view('backend.finance.journals.edit', compact('batch', 'accounts'));
    }

    /**
     * Update the specified journal batch
     */
    public function update(Request $request, $id)
    {
        $batch = JournalBatch::findOrFail($id);
        
        if (!$batch->canEdit()) {
            return redirect()->route('finance.journals.show', $batch->id)
                ->with('error', 'Only draft journals can be edited');
        }

        $request->validate([
            'description' => 'required|string',
            'entries' => 'required|array|min:2',
            'entries.*.account_id' => 'required|exists:ledger_accounts,id',
            'entries.*.debit' => 'nullable|numeric|min:0',
            'entries.*.credit' => 'nullable|numeric|min:0',
            'entries.*.narration' => 'required|string',
        ]);

        DB::beginTransaction();
        
        try {
            // Update batch description
            $batch->update([
                'description' => $request->description,
            ]);

            // Delete existing entries
            $batch->entries()->delete();

            // Create new entries
            foreach ($request->entries as $entryData) {
                $debit = $entryData['debit'] ?? 0;
                $credit = $entryData['credit'] ?? 0;
                
                if ($debit == 0 && $credit == 0) {
                    continue;
                }
                
                JournalEntry::create([
                    'journal_batch_id' => $batch->id,
                    'ledger_account_id' => $entryData['account_id'],
                    'debit_amount' => $debit,
                    'credit_amount' => $credit,
                    'narration' => $entryData['narration'],
                ]);
            }

            // Recalculate totals
            $batch->calculateTotals();

            DB::commit();

            return redirect()->route('finance.journals.show', $batch->id)
                ->with('success', 'Journal batch updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to update journal batch: ' . $e->getMessage());
        }
    }

    /**
     * Approve a journal batch
     */
    public function approve($id)
    {
        $batch = JournalBatch::findOrFail($id);
        
        try {
            $batch->approve(Auth::id());
            
            return redirect()->route('finance.journals.show', $batch->id)
                ->with('success', 'Journal batch approved successfully');
                
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Post a journal batch to ledger
     */
    public function post($id)
    {
        $batch = JournalBatch::with('entries.ledgerAccount')->findOrFail($id);
        
        try {
            $batch->post(Auth::id());
            
            return redirect()->route('finance.journals.show', $batch->id)
                ->with('success', 'Journal batch posted to ledger successfully');
                
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a journal batch
     */
    public function destroy($id)
    {
        $batch = JournalBatch::findOrFail($id);
        
        if (!$batch->canEdit()) {
            return back()->with('error', 'Only draft journals can be deleted');
        }
        
        $batch->delete();
        
        return redirect()->route('finance.journals.index')
            ->with('success', 'Journal batch deleted successfully');
    }
}
