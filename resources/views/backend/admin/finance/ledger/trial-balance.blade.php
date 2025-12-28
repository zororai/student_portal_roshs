@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Trial Balance</h1>
        <a href="{{ route('admin.finance.ledger.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">As of Date</label><input type="date" name="as_of_date" value="{{ $asOfDate }}" class="border rounded-lg px-3 py-2"></div>
            <div class="flex items-end"><button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Generate</button></div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($accounts as $account)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $account->account_code }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->account_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ ucfirst($account->account_type) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-blue-600">${{ number_format($account->period_debits, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">${{ number_format($account->period_credits, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-100">
                <tr>
                    <td colspan="3" class="px-6 py-4 text-right font-bold">Totals:</td>
                    <td class="px-6 py-4 text-right font-bold text-blue-600">${{ number_format($totalDebits, 2) }}</td>
                    <td class="px-6 py-4 text-right font-bold text-green-600">${{ number_format($totalCredits, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    @if($totalDebits != $totalCredits)
    <div class="mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <strong>Warning:</strong> Trial balance does not balance. Difference: ${{ number_format(abs($totalDebits - $totalCredits), 2) }}
    </div>
    @else
    <div class="mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
        Trial balance is balanced.
    </div>
    @endif
</div>
@endsection
