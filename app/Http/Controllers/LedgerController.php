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
        $termDateRanges = [
            'first' => ['01-01', '04-30'],
            'second' => ['05-01', '08-31'],
            'third' => ['09-01', '12-31'],
        ];
        
        $selectedYear = $request->year;
        $selectedTerm = $request->term;
        
        $query = LedgerEntry::with(['account', 'creator']);

        // Apply year/term filter
        if ($selectedYear && $selectedTerm && isset($termDateRanges[$selectedTerm])) {
            $dateFrom = $selectedYear . '-' . $termDateRanges[$selectedTerm][0];
            $dateTo = $selectedYear . '-' . $termDateRanges[$selectedTerm][1];
            $query->whereBetween('entry_date', [$dateFrom, $dateTo]);
        } elseif ($selectedYear) {
            $query->whereYear('entry_date', $selectedYear);
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

    public function storeEntry(Request $request)
    {
        $request->validate([
            'entry_date' => 'required|date',
            'account_id' => 'required|exists:ledger_accounts,id',
            'entry_type' => 'required|in:debit,credit',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string|max:500',
            'notes' => 'nullable|string',
        ]);

        $entry = LedgerEntry::create([
            'entry_date' => $request->entry_date,
            'reference_number' => LedgerEntry::generateReferenceNumber(),
            'account_id' => $request->account_id,
            'entry_type' => $request->entry_type,
            'amount' => $request->amount,
            'description' => $request->description,
            'created_by' => auth()->id(),
            'notes' => $request->notes,
        ]);

        // Update account balance
        $account = LedgerAccount::find($request->account_id);
        $account->updateBalance();

        return redirect()->route('admin.finance.ledger.entries')
            ->with('success', 'Ledger entry created successfully.');
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
