<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ResultsStatus;
use App\FeeType;
use App\TermFee;
use App\SchoolSetting;
use Carbon\Carbon;

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
        $resultsStatuses = ResultsStatus::with(['termFees.feeType'])->orderBy('year', 'desc')->orderBy('result_period', 'desc')->get();
        return view('results_status.index', compact('resultsStatuses'));
    }
    // Method to store a new record
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'year' => 'required|integer',
            'result_period' => 'required|string',
            'day_fees' => 'required|array',
            'day_fees.*.fee_type_id' => 'required|exists:fee_types,id',
            'day_fees.*.amount' => 'required|numeric|min:0',
            'boarding_fees' => 'required|array',
            'boarding_fees.*.fee_type_id' => 'required|exists:fee_types,id',
            'boarding_fees.*.amount' => 'required|numeric|min:0',
        ]);
    
        // Attempt to create a new record only if it doesn't already exist
        $existingRecord = ResultsStatus::where('year', $validatedData['year'])
                                        ->where('result_period', $validatedData['result_period'])
                                        ->exists();
    
        if ($existingRecord) {
            return redirect()->back()->withErrors(['duplicate' => 'A record with the same year and result period already exists.']);
        }
    
        // Calculate total fees for day and boarding separately
        $totalDayFees = array_sum(array_column($validatedData['day_fees'], 'amount'));
        $totalBoardingFees = array_sum(array_column($validatedData['boarding_fees'], 'amount'));
        $totalFees = $totalDayFees + $totalBoardingFees;
        
        // Create a new ResultsStatus record
        $resultsStatus = ResultsStatus::create([
            'year' => $validatedData['year'],
            'result_period' => $validatedData['result_period'],
            'total_fees' => $totalFees,
            'total_day_fees' => $totalDayFees,
            'total_boarding_fees' => $totalBoardingFees
        ]);
        
        // Create day fees
        foreach ($validatedData['day_fees'] as $fee) {
            TermFee::create([
                'results_status_id' => $resultsStatus->id,
                'fee_type_id' => $fee['fee_type_id'],
                'student_type' => 'day',
                'amount' => $fee['amount']
            ]);
        }
        
        // Create boarding fees
        foreach ($validatedData['boarding_fees'] as $fee) {
            TermFee::create([
                'results_status_id' => $resultsStatus->id,
                'fee_type_id' => $fee['fee_type_id'],
                'student_type' => 'boarding',
                'amount' => $fee['amount']
            ]);
        }
        
        // Save attendance settings
        $sessionMode = $request->session_mode ?? 'single';
        SchoolSetting::set('attendance_session_mode', $sessionMode, 'text', 'Attendance session mode (single or dual)');
        SchoolSetting::set('attendance_check_in_time', $request->check_in_time ?? '07:30', 'time', 'Morning session check-in time');
        SchoolSetting::set('attendance_check_out_time', $request->check_out_time ?? '16:30', 'time', 'Morning session check-out time');
        SchoolSetting::set('attendance_late_grace_minutes', $request->late_grace_minutes ?? 0, 'number', 'Grace period in minutes before marking as late');
        
        // Calculate and save work hours
        $checkIn = Carbon::parse($request->check_in_time ?? '07:30');
        $checkOut = Carbon::parse($request->check_out_time ?? '16:30');
        $workHours = $checkIn->diffInHours($checkOut);
        SchoolSetting::set('attendance_work_hours', $workHours, 'number', 'Morning session work hours');
        
        // Save afternoon session if dual mode
        if ($sessionMode === 'dual') {
            SchoolSetting::set('attendance_afternoon_check_in_time', $request->afternoon_check_in_time ?? '12:30', 'time', 'Afternoon session check-in time');
            SchoolSetting::set('attendance_afternoon_check_out_time', $request->afternoon_check_out_time ?? '17:30', 'time', 'Afternoon session check-out time');
            
            $afternoonIn = Carbon::parse($request->afternoon_check_in_time ?? '12:30');
            $afternoonOut = Carbon::parse($request->afternoon_check_out_time ?? '17:30');
            $afternoonHours = $afternoonIn->diffInHours($afternoonOut);
            SchoolSetting::set('attendance_afternoon_work_hours', $afternoonHours, 'number', 'Afternoon session work hours');
        }
    
        return redirect()->route('results_status.index')->with('success', 'Term created with attendance settings! Day fees: $' . number_format($totalDayFees, 2) . ' | Boarding fees: $' . number_format($totalBoardingFees, 2));
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
        $resultStatus = ResultsStatus::with(['termFees.feeType'])->findOrFail($id);
        $feeTypes = FeeType::where('is_active', true)->get();
        
        // Separate day and boarding fees
        $dayFees = $resultStatus->termFees->where('student_type', 'day')->values();
        $boardingFees = $resultStatus->termFees->where('student_type', 'boarding')->values();
        
        return view('results_status.edit', compact('resultStatus', 'feeTypes', 'dayFees', 'boardingFees'));
    }

    // Method to update an existing record
    public function update(Request $request, $id)
    {
        $request->validate([
            'year' => 'required|integer',
            'result_period' => 'required|string',
            'day_fees' => 'required|array',
            'day_fees.*.fee_type_id' => 'required|exists:fee_types,id',
            'day_fees.*.amount' => 'required|numeric|min:0',
            'boarding_fees' => 'required|array',
            'boarding_fees.*.fee_type_id' => 'required|exists:fee_types,id',
            'boarding_fees.*.amount' => 'required|numeric|min:0',
        ]);

        $resultStatus = ResultsStatus::findOrFail($id);

        // Check for duplicate year+result_period combination (excluding current record)
        $existingRecord = ResultsStatus::where('year', $request->year)
            ->where('result_period', $request->result_period)
            ->where('id', '!=', $id)
            ->first();
            
        if ($existingRecord) {
            return redirect()->back()->withErrors(['year' => 'A record with this year and result period already exists.']);
        }

        // Calculate total fees for day and boarding separately
        $totalDayFees = array_sum(array_column($request->day_fees, 'amount'));
        $totalBoardingFees = array_sum(array_column($request->boarding_fees, 'amount'));
        $totalFees = $totalDayFees + $totalBoardingFees;

        // Update the results status
        $resultStatus->update([
            'year' => $request->year,
            'result_period' => $request->result_period,
            'total_fees' => $totalFees,
            'total_day_fees' => $totalDayFees,
            'total_boarding_fees' => $totalBoardingFees
        ]);

        // Delete existing term fees and recreate them
        $resultStatus->termFees()->delete();

        // Create day fees
        foreach ($request->day_fees as $fee) {
            TermFee::create([
                'results_status_id' => $resultStatus->id,
                'fee_type_id' => $fee['fee_type_id'],
                'student_type' => 'day',
                'amount' => $fee['amount']
            ]);
        }

        // Create boarding fees
        foreach ($request->boarding_fees as $fee) {
            TermFee::create([
                'results_status_id' => $resultStatus->id,
                'fee_type_id' => $fee['fee_type_id'],
                'student_type' => 'boarding',
                'amount' => $fee['amount']
            ]);
        }

        return redirect()->route('results_status.index')->with('success', 'Term updated! Day fees: $' . number_format($totalDayFees, 2) . ' | Boarding fees: $' . number_format($totalBoardingFees, 2));
    }
}