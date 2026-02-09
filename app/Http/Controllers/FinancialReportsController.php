<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LedgerPostingService;
use App\LedgerAccount;
use App\LedgerEntry;
use Carbon\Carbon;
use DB;

class FinancialReportsController extends Controller
{
    protected $ledgerService;

    public function __construct(LedgerPostingService $ledgerService)
    {
        $this->ledgerService = $ledgerService;
    }

    /**
     * Display Trial Balance
     */
    public function trialBalance(Request $request)
    {
        $asOfDate = $request->input('as_of_date', now()->toDateString());
        
        $trialBalance = $this->ledgerService->getTrialBalance($asOfDate);
        
        return view('backend.finance.reports.trial-balance', compact('trialBalance', 'asOfDate'));
    }

    /**
     * Display Profit & Loss Statement
     */
    public function profitAndLoss(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfYear()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        // Get income accounts
        $incomeAccounts = LedgerAccount::where('account_type', 'income')
            ->where('is_active', true)
            ->with(['entries' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('entry_date', [$startDate, $endDate]);
            }])
            ->get();
        
        // Get expense accounts
        $expenseAccounts = LedgerAccount::where('account_type', 'expense')
            ->where('is_active', true)
            ->with(['entries' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('entry_date', [$startDate, $endDate]);
            }])
            ->get();
        
        // Calculate income
        $incomeByCategory = [];
        $totalIncome = 0;
        
        foreach ($incomeAccounts as $account) {
            $credits = $account->entries->where('entry_type', 'credit')->sum('amount');
            $debits = $account->entries->where('entry_type', 'debit')->sum('amount');
            $amount = $credits - $debits;
            
            if ($amount != 0) {
                if (!isset($incomeByCategory[$account->category])) {
                    $incomeByCategory[$account->category] = [
                        'category' => $account->category,
                        'accounts' => [],
                        'total' => 0,
                    ];
                }
                
                $incomeByCategory[$account->category]['accounts'][] = [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'amount' => $amount,
                ];
                
                $incomeByCategory[$account->category]['total'] += $amount;
                $totalIncome += $amount;
            }
        }
        
        // Calculate expenses
        $expensesByCategory = [];
        $totalExpenses = 0;
        
        foreach ($expenseAccounts as $account) {
            $debits = $account->entries->where('entry_type', 'debit')->sum('amount');
            $credits = $account->entries->where('entry_type', 'credit')->sum('amount');
            $amount = $debits - $credits;
            
            if ($amount != 0) {
                if (!isset($expensesByCategory[$account->category])) {
                    $expensesByCategory[$account->category] = [
                        'category' => $account->category,
                        'accounts' => [],
                        'total' => 0,
                    ];
                }
                
                $expensesByCategory[$account->category]['accounts'][] = [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'amount' => $amount,
                ];
                
                $expensesByCategory[$account->category]['total'] += $amount;
                $totalExpenses += $amount;
            }
        }
        
        $netProfit = $totalIncome - $totalExpenses;
        
        return view('backend.finance.reports.profit-loss', compact(
            'incomeByCategory',
            'expensesByCategory',
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Display Balance Sheet
     */
    public function balanceSheet(Request $request)
    {
        $asOfDate = $request->input('as_of_date', now()->toDateString());
        
        // Get asset accounts
        $assetAccounts = LedgerAccount::where('account_type', 'asset')
            ->where('is_active', true)
            ->with(['entries' => function ($q) use ($asOfDate) {
                $q->where('entry_date', '<=', $asOfDate);
            }])
            ->get();
        
        // Get liability accounts
        $liabilityAccounts = LedgerAccount::where('account_type', 'liability')
            ->where('is_active', true)
            ->with(['entries' => function ($q) use ($asOfDate) {
                $q->where('entry_date', '<=', $asOfDate);
            }])
            ->get();
        
        // Get equity accounts
        $equityAccounts = LedgerAccount::where('account_type', 'equity')
            ->where('is_active', true)
            ->with(['entries' => function ($q) use ($asOfDate) {
                $q->where('entry_date', '<=', $asOfDate);
            }])
            ->get();
        
        // Calculate assets
        $assetsByCategory = [];
        $totalAssets = 0;
        
        foreach ($assetAccounts as $account) {
            $debits = $account->entries->where('entry_type', 'debit')->sum('amount');
            $credits = $account->entries->where('entry_type', 'credit')->sum('amount');
            $balance = $account->opening_balance + $debits - $credits;
            
            if ($balance != 0) {
                if (!isset($assetsByCategory[$account->category])) {
                    $assetsByCategory[$account->category] = [
                        'category' => $account->category,
                        'accounts' => [],
                        'total' => 0,
                    ];
                }
                
                $assetsByCategory[$account->category]['accounts'][] = [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $balance,
                ];
                
                $assetsByCategory[$account->category]['total'] += $balance;
                $totalAssets += $balance;
            }
        }
        
        // Calculate liabilities
        $liabilitiesByCategory = [];
        $totalLiabilities = 0;
        
        foreach ($liabilityAccounts as $account) {
            $credits = $account->entries->where('entry_type', 'credit')->sum('amount');
            $debits = $account->entries->where('entry_type', 'debit')->sum('amount');
            $balance = $account->opening_balance + $credits - $debits;
            
            if ($balance != 0) {
                if (!isset($liabilitiesByCategory[$account->category])) {
                    $liabilitiesByCategory[$account->category] = [
                        'category' => $account->category,
                        'accounts' => [],
                        'total' => 0,
                    ];
                }
                
                $liabilitiesByCategory[$account->category]['accounts'][] = [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $balance,
                ];
                
                $liabilitiesByCategory[$account->category]['total'] += $balance;
                $totalLiabilities += $balance;
            }
        }
        
        // Calculate equity
        $equityByCategory = [];
        $totalEquity = 0;
        
        foreach ($equityAccounts as $account) {
            $credits = $account->entries->where('entry_type', 'credit')->sum('amount');
            $debits = $account->entries->where('entry_type', 'debit')->sum('amount');
            $balance = $account->opening_balance + $credits - $debits;
            
            if ($balance != 0) {
                if (!isset($equityByCategory[$account->category])) {
                    $equityByCategory[$account->category] = [
                        'category' => $account->category,
                        'accounts' => [],
                        'total' => 0,
                    ];
                }
                
                $equityByCategory[$account->category]['accounts'][] = [
                    'code' => $account->account_code,
                    'name' => $account->account_name,
                    'balance' => $balance,
                ];
                
                $equityByCategory[$account->category]['total'] += $balance;
                $totalEquity += $balance;
            }
        }
        
        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;
        
        return view('backend.finance.reports.balance-sheet', compact(
            'assetsByCategory',
            'liabilitiesByCategory',
            'equityByCategory',
            'totalAssets',
            'totalLiabilities',
            'totalEquity',
            'totalLiabilitiesAndEquity',
            'asOfDate'
        ));
    }

    /**
     * Display General Ledger
     */
    public function generalLedger(Request $request)
    {
        $accountCode = $request->input('account_code');
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->toDateString());
        
        $accounts = LedgerAccount::where('is_active', true)
            ->orderBy('account_code')
            ->get();
        
        $ledgerData = null;
        
        if ($accountCode) {
            $account = LedgerAccount::where('account_code', $accountCode)->first();
            
            if ($account) {
                $entries = LedgerEntry::where('account_id', $account->id)
                    ->whereBetween('entry_date', [$startDate, $endDate])
                    ->orderBy('entry_date')
                    ->orderBy('id')
                    ->get();
                
                $runningBalance = $account->opening_balance;
                $ledgerEntries = [];
                
                foreach ($entries as $entry) {
                    if (in_array($account->account_type, ['asset', 'expense'])) {
                        $runningBalance += ($entry->entry_type === 'debit' ? $entry->amount : -$entry->amount);
                    } else {
                        $runningBalance += ($entry->entry_type === 'credit' ? $entry->amount : -$entry->amount);
                    }
                    
                    $ledgerEntries[] = [
                        'date' => $entry->entry_date,
                        'reference' => $entry->reference_number,
                        'description' => $entry->description,
                        'debit' => $entry->entry_type === 'debit' ? $entry->amount : 0,
                        'credit' => $entry->entry_type === 'credit' ? $entry->amount : 0,
                        'balance' => $runningBalance,
                    ];
                }
                
                $ledgerData = [
                    'account' => $account,
                    'entries' => $ledgerEntries,
                    'opening_balance' => $account->opening_balance,
                    'closing_balance' => $runningBalance,
                ];
            }
        }
        
        return view('backend.finance.reports.general-ledger', compact(
            'accounts',
            'ledgerData',
            'accountCode',
            'startDate',
            'endDate'
        ));
    }
}
