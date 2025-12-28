<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BankAccount;
use App\BankTransaction;
use App\CashBookEntry;
use Carbon\Carbon;

class BankReconciliationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $accounts = BankAccount::where('is_active', true)->get();
        return view('backend.admin.finance.reconciliation.index', compact('accounts'));
    }

    public function accounts()
    {
        $accounts = BankAccount::withCount('transactions')->get();
        return view('backend.admin.finance.reconciliation.accounts', compact('accounts'));
    }

    public function createAccount()
    {
        return view('backend.admin.finance.reconciliation.create-account');
    }

    public function storeAccount(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:50',
            'opening_balance' => 'required|numeric|min:0',
        ]);

        BankAccount::create([
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
            'opening_balance' => $request->opening_balance,
            'current_balance' => $request->opening_balance,
        ]);

        return redirect()->route('admin.finance.reconciliation.accounts')
            ->with('success', 'Bank account created successfully.');
    }

    public function transactions($accountId)
    {
        $account = BankAccount::findOrFail($accountId);
        $transactions = BankTransaction::where('bank_account_id', $accountId)
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);
        
        return view('backend.admin.finance.reconciliation.transactions', compact('account', 'transactions'));
    }

    public function importStatement(Request $request, $accountId)
    {
        $request->validate([
            'statement_file' => 'required|file|mimes:csv,txt',
        ]);

        $account = BankAccount::findOrFail($accountId);
        $file = $request->file('statement_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $header = fgetcsv($handle);
        $imported = 0;
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 4) {
                $lastTransaction = BankTransaction::where('bank_account_id', $accountId)
                    ->orderBy('id', 'desc')
                    ->first();
                $balance = $lastTransaction ? $lastTransaction->balance_after : $account->opening_balance;
                
                $amount = floatval(str_replace(',', '', $row[2]));
                $type = $amount >= 0 ? 'deposit' : 'withdrawal';
                $amount = abs($amount);
                $newBalance = $type == 'deposit' ? $balance + $amount : $balance - $amount;

                BankTransaction::create([
                    'bank_account_id' => $accountId,
                    'transaction_date' => Carbon::parse($row[0]),
                    'reference_number' => $row[1] ?? null,
                    'transaction_type' => $type,
                    'amount' => $amount,
                    'balance_after' => $newBalance,
                    'description' => $row[3] ?? '',
                ]);
                $imported++;
            }
        }
        fclose($handle);
        
        $account->updateBalance();

        return redirect()->route('admin.finance.reconciliation.transactions', $accountId)
            ->with('success', "Imported $imported transactions.");
    }

    public function reconcile($accountId)
    {
        $account = BankAccount::findOrFail($accountId);
        $unreconciledBank = BankTransaction::where('bank_account_id', $accountId)
            ->where('is_reconciled', false)
            ->orderBy('transaction_date', 'desc')
            ->get();
        
        $unreconciledCashBook = CashBookEntry::whereNull('id')
            ->orWhereNotIn('id', function($query) {
                $query->select('reconciled_with')->from('bank_transactions')->whereNotNull('reconciled_with');
            })
            ->orderBy('entry_date', 'desc')
            ->get();

        return view('backend.admin.finance.reconciliation.reconcile', compact('account', 'unreconciledBank', 'unreconciledCashBook'));
    }

    public function matchTransactions(Request $request)
    {
        $request->validate([
            'bank_transaction_id' => 'required|exists:bank_transactions,id',
            'cash_book_entry_id' => 'required|exists:cash_book_entries,id',
        ]);

        $bankTransaction = BankTransaction::findOrFail($request->bank_transaction_id);
        $bankTransaction->update([
            'is_reconciled' => true,
            'reconciled_with' => $request->cash_book_entry_id,
            'reconciled_at' => now(),
            'reconciled_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'message' => 'Transactions matched successfully.']);
    }

    public function unmatch($transactionId)
    {
        $transaction = BankTransaction::findOrFail($transactionId);
        $transaction->update([
            'is_reconciled' => false,
            'reconciled_with' => null,
            'reconciled_at' => null,
            'reconciled_by' => null,
        ]);

        return back()->with('success', 'Transaction unmatched.');
    }
}
