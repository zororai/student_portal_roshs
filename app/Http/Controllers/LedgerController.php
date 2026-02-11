<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LedgerAccount;
use App\LedgerEntry;
use Carbon\Carbon;

class LedgerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index()
    {
        $accounts = LedgerAccount::where('is_active', true)
            ->orderBy('account_code')
            ->get()
            ->groupBy('account_type');

        $accountTypes = LedgerAccount::getAccountTypes();

        // Summary by account type
        $summary = [];
        foreach ($accountTypes as $type => $label) {
            $summary[$type] = LedgerAccount::where('account_type', $type)
                ->where('is_active', true)
                ->sum('current_balance');
        }

        return view('backend.admin.finance.ledger.index', compact('accounts', 'accountTypes', 'summary'));
    }

    public function createAccount()
    {
        $accountTypes = LedgerAccount::getAccountTypes();
        return view('backend.admin.finance.ledger.create-account', compact('accountTypes'));
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:asset,liability,equity,income,expense',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'opening_balance' => 'nullable|numeric',
        ]);

        $accountCode = LedgerAccount::generateAccountCode($request->account_type);

        LedgerAccount::create([
            'account_code' => $accountCode,
            'account_name' => $request->account_name,
            'account_type' => $request->account_type,
            'category' => $request->category,
            'description' => $request->description,
            'opening_balance' => $request->opening_balance ?? 0,
            'current_balance' => $request->opening_balance ?? 0,
        ]);

        return redirect()->route('admin.finance.ledger.index')
            ->with('success', 'Ledger account created successfully.');
    }

    public function showAccount($id)
    {
        $account = LedgerAccount::findOrFail($id);
        $entries = LedgerEntry::where('account_id', $id)
            ->with('creator')
            ->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('backend.admin.finance.ledger.show-account', compact('account', 'entries'));
    }

    public function editAccount($id)
    {
        $account = LedgerAccount::findOrFail($id);
        $accountTypes = LedgerAccount::getAccountTypes();
        return view('backend.admin.finance.ledger.edit-account', compact('account', 'accountTypes'));
    }

    public function updateAccount(Request $request, $id)
    {
        $account = LedgerAccount::findOrFail($id);

        $request->validate([
            'account_name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $account->update([
            'account_name' => $request->account_name,
            'category' => $request->category,
            'description' => $request->description,
        ]);

        return redirect()->route('admin.finance.ledger.index')
            ->with('success', 'Ledger account updated successfully.');
    }

    public function entries(Request $request)
    {
        // Year and term filter setup
        $years = range(date('Y'), date('Y') - 5);
        $terms = ['first' => 'First Term', 'second' => 'Second Term', 'third' => 'Third Term'];
        
        $selectedYear = $request->year;
        $selectedTerm = $request->term;
        
        $query = LedgerEntry::with(['account', 'creator']);

        // Apply year/term filter using term/year fields
        if ($selectedYear) {
            $query->where('year', $selectedYear);
        }
        if ($selectedTerm) {
            $query->where('term', $selectedTerm);
        }

        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('entry_type')) {
            $query->where('entry_type', $request->entry_type);
        }

        $entries = $query->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->appends($request->query());

        $accounts = LedgerAccount::where('is_active', true)
            ->orderBy('account_code')
            ->get();

        return view('backend.admin.finance.ledger.entries', compact('entries', 'accounts', 'years', 'terms', 'selectedYear', 'selectedTerm'));
    }

    public function createEntry()
    {
        $accounts = LedgerAccount::where('is_active', true)
            ->orderBy('account_code')
            ->get();

        return view('backend.admin.finance.ledger.create-entry', compact('accounts'));
    }

    /**
     * Store a ledger entry - DISABLED for governance compliance
     * Single-sided entries violate double-entry accounting principles.
     * Use Journal Entries for manual ledger postings instead.
     */
    public function storeEntry(Request $request)
    {
        // GOVERNANCE COMPLIANCE: Direct ledger entries are not allowed
        // All ledger entries must go through LedgerPostingService to ensure
        // double-entry integrity (Debit = Credit)
        // 
        // Redirect users to use Journal Entries for manual postings
        return redirect()->route('finance.journals.create')
            ->with('warning', 'Direct ledger entries are disabled for compliance. Please use Journal Entries for manual postings to ensure double-entry integrity.');
    }

    public function trialBalance(Request $request)
    {
        $asOfDate = $request->get('as_of_date', Carbon::now()->toDateString());

        $accounts = LedgerAccount::where('is_active', true)
            ->orderBy('account_code')
            ->get()
            ->map(function ($account) use ($asOfDate) {
                $debits = LedgerEntry::where('account_id', $account->id)
                    ->where('entry_type', 'debit')
                    ->whereDate('entry_date', '<=', $asOfDate)
                    ->sum('amount');

                $credits = LedgerEntry::where('account_id', $account->id)
                    ->where('entry_type', 'credit')
                    ->whereDate('entry_date', '<=', $asOfDate)
                    ->sum('amount');

                $account->period_debits = $debits;
                $account->period_credits = $credits;

                // Calculate balance based on account type
                if (in_array($account->account_type, ['asset', 'expense'])) {
                    $account->balance = $account->opening_balance + $debits - $credits;
                } else {
                    $account->balance = $account->opening_balance + $credits - $debits;
                }

                return $account;
            });

        $totalDebits = $accounts->sum('period_debits');
        $totalCredits = $accounts->sum('period_credits');

        return view('backend.admin.finance.ledger.trial-balance', compact(
            'accounts', 'asOfDate', 'totalDebits', 'totalCredits'
        ));
    }
}
