@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Double-Entry Accounting & Ledger Guide</h1>
            <p class="text-gray-600">Implementation documentation for the Dzidzo Student Portal</p>
        </div>
        <a href="{{ route('admin.finance.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Table of Contents -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-4 sticky top-4">
                <h3 class="font-semibold text-gray-800 mb-3">Contents</h3>
                <nav class="space-y-2 text-sm">
                    <a href="#purpose" class="block text-blue-600 hover:text-blue-800">1. Purpose</a>
                    <a href="#core-rule" class="block text-blue-600 hover:text-blue-800">2. Core Accounting Rule</a>
                    <a href="#chart-of-accounts" class="block text-blue-600 hover:text-blue-800">3. Chart of Accounts</a>
                    <a href="#ledger-entries" class="block text-blue-600 hover:text-blue-800">4. Ledger Entries</a>
                    <a href="#journal-batches" class="block text-blue-600 hover:text-blue-800">5. Journal Batches</a>
                    <a href="#balance-calculation" class="block text-blue-600 hover:text-blue-800">6. Balance Calculation</a>
                    <a href="#transaction-examples" class="block text-blue-600 hover:text-blue-800">7. Transaction Examples</a>
                    <a href="#integration" class="block text-blue-600 hover:text-blue-800">8. Module Integration</a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Purpose -->
            <div id="purpose" class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <span class="bg-blue-100 text-blue-800 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm font-bold">1</span>
                    Purpose
                </h2>
                <p class="text-gray-700 mb-4">
                    This document defines how <strong>double-entry accounting</strong> is implemented in the Dzidzo Student Portal.
                </p>
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                    <p class="font-medium text-blue-800 mb-2">Double-entry accounting ensures:</p>
                    <ul class="list-disc list-inside text-blue-700 space-y-1">
                        <li>Financial accuracy</li>
                        <li>Audit compliance</li>
                        <li>Traceability of all transactions</li>
                        <li>Proper integration with Assets, Payroll, Fees, and Expenses</li>
                    </ul>
                </div>
            </div>

            <!-- Core Rule -->
            <div id="core-rule" class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <span class="bg-blue-100 text-blue-800 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm font-bold">2</span>
                    Core Accounting Rule (Non-Negotiable)
                </h2>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded">
                    <p class="font-bold text-red-800 mb-2">Every financial transaction MUST generate at least two ledger entries:</p>
                    <ul class="list-disc list-inside text-red-700 space-y-1">
                        <li>One Debit</li>
                        <li>One Credit</li>
                        <li><strong>Total Debits MUST equal Total Credits</strong></li>
                    </ul>
                    <p class="mt-3 text-red-800 font-medium">If this rule is violated, the transaction MUST fail.</p>
                </div>
            </div>

            <!-- Chart of Accounts -->
            <div id="chart-of-accounts" class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <span class="bg-blue-100 text-blue-800 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm font-bold">3</span>
                    Chart of Accounts (ledger_accounts)
                </h2>
                
                <h3 class="font-semibold text-gray-700 mb-3">Table Structure</h3>
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Field</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <tr><td class="px-4 py-2 font-mono text-blue-600">id</td><td class="px-4 py-2">Primary key</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">account_code</td><td class="px-4 py-2">Unique account code</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">account_name</td><td class="px-4 py-2">Account name</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">account_type</td><td class="px-4 py-2">asset, liability, equity, income, expense</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">category</td><td class="px-4 py-2">Optional grouping</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">opening_balance</td><td class="px-4 py-2">Opening balance</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">current_balance</td><td class="px-4 py-2">System-calculated balance</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">is_active</td><td class="px-4 py-2">Account status</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">timestamps</td><td class="px-4 py-2">Laravel timestamps</td></tr>
                        </tbody>
                    </table>
                </div>

                <h3 class="font-semibold text-gray-700 mb-3">Account Types & Examples</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-green-800">Assets</h4>
                        <ul class="text-sm text-green-700 mt-2 space-y-1">
                            <li>• Cash (1001)</li>
                            <li>• Fixed Assets (1200)</li>
                            <li>• Accounts Receivable</li>
                        </ul>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-red-800">Liabilities</h4>
                        <ul class="text-sm text-red-700 mt-2 space-y-1">
                            <li>• Accounts Payable</li>
                            <li>• Deferred Revenue</li>
                            <li>• Loans Payable</li>
                        </ul>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-purple-800">Equity</h4>
                        <ul class="text-sm text-purple-700 mt-2 space-y-1">
                            <li>• Retained Earnings</li>
                            <li>• Owner's Capital</li>
                        </ul>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-blue-800">Income</h4>
                        <ul class="text-sm text-blue-700 mt-2 space-y-1">
                            <li>• School Fees Income (4001)</li>
                            <li>• Registration Fees (4002)</li>
                            <li>• Other Income</li>
                        </ul>
                    </div>
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-orange-800">Expenses</h4>
                        <ul class="text-sm text-orange-700 mt-2 space-y-1">
                            <li>• Salary Expense (5001)</li>
                            <li>• Depreciation Expense (5010)</li>
                            <li>• Utilities Expense</li>
                        </ul>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-800">Contra Accounts</h4>
                        <ul class="text-sm text-gray-700 mt-2 space-y-1">
                            <li>• Accumulated Depreciation (1201)</li>
                            <li>• Allowance for Bad Debts</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Ledger Entries -->
            <div id="ledger-entries" class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <span class="bg-blue-100 text-blue-800 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm font-bold">4</span>
                    Ledger Entries (ledger_entries)
                </h2>
                
                <p class="text-gray-700 mb-4">Each row represents <strong>one side</strong> of a transaction.</p>
                
                <div class="overflow-x-auto mb-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Field</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <tr><td class="px-4 py-2 font-mono text-blue-600">id</td><td class="px-4 py-2">Primary key</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">ledger_account_id</td><td class="px-4 py-2">FK → ledger_accounts</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">transaction_date</td><td class="px-4 py-2">Posting date</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">reference_type</td><td class="px-4 py-2">Source model (e.g., App\Asset)</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">reference_id</td><td class="px-4 py-2">Source record ID</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">description</td><td class="px-4 py-2">Entry description</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">debit_amount</td><td class="px-4 py-2">Debit value (0 if none)</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">credit_amount</td><td class="px-4 py-2">Credit value (0 if none)</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">created_by</td><td class="px-4 py-2">User who posted</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">timestamps</td><td class="px-4 py-2">Laravel timestamps</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded">
                    <p class="text-yellow-800 font-medium">
                        <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        A ledger entry MUST contain either a debit or a credit, never both.
                    </p>
                </div>
            </div>

            <!-- Journal Batches -->
            <div id="journal-batches" class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <span class="bg-blue-100 text-blue-800 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm font-bold">5</span>
                    Journal Batches (Optional but Recommended)
                </h2>
                
                <p class="text-gray-700 mb-4">Groups ledger entries into a single atomic transaction.</p>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Field</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <tr><td class="px-4 py-2 font-mono text-blue-600">id</td><td class="px-4 py-2">Primary key</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">reference_type</td><td class="px-4 py-2">Source model</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">reference_id</td><td class="px-4 py-2">Source ID</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">description</td><td class="px-4 py-2">Transaction summary</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">total_debit</td><td class="px-4 py-2">Sum of debits</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">total_credit</td><td class="px-4 py-2">Sum of credits</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">posted_by</td><td class="px-4 py-2">User</td></tr>
                            <tr><td class="px-4 py-2 font-mono text-blue-600">posted_at</td><td class="px-4 py-2">Timestamp</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Balance Calculation -->
            <div id="balance-calculation" class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <span class="bg-blue-100 text-blue-800 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm font-bold">6</span>
                    LedgerAccount Model - Balance Calculation
                </h2>

                <h3 class="font-semibold text-gray-700 mb-3">Balance Calculation Rules</h3>
                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance Formula</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <tr class="bg-green-50"><td class="px-4 py-2 font-semibold text-green-800">Asset</td><td class="px-4 py-2 font-mono">opening + debits − credits</td></tr>
                            <tr class="bg-orange-50"><td class="px-4 py-2 font-semibold text-orange-800">Expense</td><td class="px-4 py-2 font-mono">opening + debits − credits</td></tr>
                            <tr class="bg-red-50"><td class="px-4 py-2 font-semibold text-red-800">Liability</td><td class="px-4 py-2 font-mono">opening + credits − debits</td></tr>
                            <tr class="bg-blue-50"><td class="px-4 py-2 font-semibold text-blue-800">Income</td><td class="px-4 py-2 font-mono">opening + credits − debits</td></tr>
                            <tr class="bg-purple-50"><td class="px-4 py-2 font-semibold text-purple-800">Equity</td><td class="px-4 py-2 font-mono">opening + credits − debits</td></tr>
                        </tbody>
                    </table>
                </div>

                <h3 class="font-semibold text-gray-700 mb-3">Required Method</h3>
                <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                    <pre class="text-sm text-green-400"><code>public function updateBalance()
{
    $debits = $this->entries()->sum('debit_amount');
    $credits = $this->entries()->sum('credit_amount');

    if (in_array($this->account_type, ['asset', 'expense'])) {
        $this->current_balance = $this->opening_balance + $debits - $credits;
    } else {
        $this->current_balance = $this->opening_balance + $credits - $debits;
    }

    $this->save();
}</code></pre>
                </div>
            </div>

            <!-- Transaction Examples -->
            <div id="transaction-examples" class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <span class="bg-blue-100 text-blue-800 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm font-bold">7</span>
                    Transaction Examples
                </h2>

                <div class="space-y-6">
                    <!-- Fee Payment -->
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-3">Student Fee Payment ($500)</h4>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div class="font-medium text-gray-600">Account</div>
                            <div class="font-medium text-gray-600 text-right">Debit</div>
                            <div class="font-medium text-gray-600 text-right">Credit</div>
                            
                            <div>Cash (1001)</div>
                            <div class="text-right text-green-600 font-mono">$500.00</div>
                            <div class="text-right">-</div>
                            
                            <div>School Fees Income (4001)</div>
                            <div class="text-right">-</div>
                            <div class="text-right text-blue-600 font-mono">$500.00</div>
                            
                            <div class="border-t pt-2 font-semibold">Total</div>
                            <div class="border-t pt-2 text-right font-mono font-semibold">$500.00</div>
                            <div class="border-t pt-2 text-right font-mono font-semibold">$500.00</div>
                        </div>
                    </div>

                    <!-- Asset Purchase -->
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-3">Asset Purchase - Computer ($2,000)</h4>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div class="font-medium text-gray-600">Account</div>
                            <div class="font-medium text-gray-600 text-right">Debit</div>
                            <div class="font-medium text-gray-600 text-right">Credit</div>
                            
                            <div>Fixed Assets (1200)</div>
                            <div class="text-right text-green-600 font-mono">$2,000.00</div>
                            <div class="text-right">-</div>
                            
                            <div>Cash (1001)</div>
                            <div class="text-right">-</div>
                            <div class="text-right text-blue-600 font-mono">$2,000.00</div>
                            
                            <div class="border-t pt-2 font-semibold">Total</div>
                            <div class="border-t pt-2 text-right font-mono font-semibold">$2,000.00</div>
                            <div class="border-t pt-2 text-right font-mono font-semibold">$2,000.00</div>
                        </div>
                    </div>

                    <!-- Depreciation -->
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-3">Monthly Depreciation ($150)</h4>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div class="font-medium text-gray-600">Account</div>
                            <div class="font-medium text-gray-600 text-right">Debit</div>
                            <div class="font-medium text-gray-600 text-right">Credit</div>
                            
                            <div>Depreciation Expense (5010)</div>
                            <div class="text-right text-green-600 font-mono">$150.00</div>
                            <div class="text-right">-</div>
                            
                            <div>Accumulated Depreciation (1201)</div>
                            <div class="text-right">-</div>
                            <div class="text-right text-blue-600 font-mono">$150.00</div>
                            
                            <div class="border-t pt-2 font-semibold">Total</div>
                            <div class="border-t pt-2 text-right font-mono font-semibold">$150.00</div>
                            <div class="border-t pt-2 text-right font-mono font-semibold">$150.00</div>
                        </div>
                    </div>

                    <!-- Salary Payment -->
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold text-gray-800 mb-3">Salary Payment ($3,500)</h4>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div class="font-medium text-gray-600">Account</div>
                            <div class="font-medium text-gray-600 text-right">Debit</div>
                            <div class="font-medium text-gray-600 text-right">Credit</div>
                            
                            <div>Salary Expense (5001)</div>
                            <div class="text-right text-green-600 font-mono">$3,500.00</div>
                            <div class="text-right">-</div>
                            
                            <div>Cash (1001)</div>
                            <div class="text-right">-</div>
                            <div class="text-right text-blue-600 font-mono">$3,500.00</div>
                            
                            <div class="border-t pt-2 font-semibold">Total</div>
                            <div class="border-t pt-2 text-right font-mono font-semibold">$3,500.00</div>
                            <div class="border-t pt-2 text-right font-mono font-semibold">$3,500.00</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Integration -->
            <div id="integration" class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <span class="bg-blue-100 text-blue-800 rounded-full w-8 h-8 flex items-center justify-center mr-3 text-sm font-bold">8</span>
                    Module Integration
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold text-blue-800 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Cash Book
                        </h4>
                        <p class="text-sm text-gray-600">All receipts and payments auto-post to ledger via <code class="bg-gray-100 px-1 rounded">postToLedger()</code> method.</p>
                    </div>

                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold text-green-800 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Asset Management
                        </h4>
                        <p class="text-sm text-gray-600">Asset purchases, depreciation, and disposals create ledger entries automatically.</p>
                    </div>

                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold text-purple-800 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Payroll
                        </h4>
                        <p class="text-sm text-gray-600">Salary payments and deductions post to respective expense and liability accounts.</p>
                    </div>

                    <div class="border rounded-lg p-4">
                        <h4 class="font-semibold text-orange-800 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Student Fees
                        </h4>
                        <p class="text-sm text-gray-600">Fee payments credit income accounts and debit cash or receivables.</p>
                    </div>
                </div>

                <div class="mt-6 bg-green-50 border-l-4 border-green-500 p-4 rounded">
                    <p class="text-green-800 font-medium">
                        <svg class="w-5 h-5 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        All modules use the same LedgerAccount and LedgerEntry models, ensuring consistency across the entire financial system.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
