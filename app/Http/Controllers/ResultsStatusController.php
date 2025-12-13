<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ResultsStatus;
use App\FeeType;
use App\TermFee;

class ResultsStatusController extends Controller
{
    // Method to show the form for creating a new record
    public function create()
    {
        $feeTypes = FeeType::where('is_active', true)->get();
        return view('results_status.create', compact('feeTypes'));
    }
    public function index()
    {
        $resultsStatuses = ResultsStatus::all();
        return view('results_status.index', compact('resultsStatuses'));
    }
    // Method to store a new record
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'year' => 'required|integer',
            'result_period' => 'required|string',
            'fees' => 'required|array',
            'fees.*.fee_type_id' => 'required|exists:fee_types,id',
            'fees.*.amount' => 'required|numeric|min:0',
        ]);
    
        // Attempt to create a new record only if it doesn't already exist
        $existingRecord = ResultsStatus::where('year', $validatedData['year'])
                                        ->where('result_period', $validatedData['result_period'])
                                        ->exists();
    
        if ($existingRecord) {
            return redirect()->back()->withErrors(['duplicate' => 'A record with the same year and result period already exists.']);
        }
    
        // Calculate total fees
        $totalFees = array_sum(array_column($validatedData['fees'], 'amount'));
        
        // Create a new ResultsStatus record
        $resultsStatus = ResultsStatus::create([
            'year' => $validatedData['year'],
            'result_period' => $validatedData['result_period'],
            'total_fees' => $totalFees
        ]);
        
        // Create term fees
        foreach ($validatedData['fees'] as $fee) {
            TermFee::create([
                'results_status_id' => $resultsStatus->id,
                'fee_type_id' => $fee['fee_type_id'],
                'amount' => $fee['amount']
            ]);
        }
    
        return redirect()->route('results_status.index')->with('success', 'Term and fees created successfully. Total fees: $' . number_format($totalFees, 2));
    }

    public function destroy($id)
    {
        // Find the record by ID
        $resultStatus = ResultsStatus::findOrFail($id);
        
        // Delete the record
        $resultStatus->delete();

        // Redirect back to the index with a success message
        return redirect()->route('results_status.index')->with('success', 'Record deleted successfully.');
    }
    

    // Method to show the edit form for a specific record
    public function edit($id)
    {
        $resultStatus = ResultsStatus::findOrFail($id);
        return view('results_status.edit', compact('resultStatus'));
    }

    // Method to update an existing record
    public function update(Request $request, $id)
    {
        $request->validate([
            'year' => 'required|integer',
            'result_period' => 'required|string',
        ]);

        $resultStatus = ResultsStatus::findOrFail($id);

        if ($resultStatus->year !== $request->year) {
            $existingRecord = ResultsStatus::where('year', $request->year)->first();
            if ($existingRecord) {
                return redirect()->back()->withErrors(['year' => 'The year has already been taken.']);
            }
        }

        $resultStatus->update($request->only('result_period'));

        return redirect()->route('results_status.index')->with('success', 'Record updated successfully.');
    }
}