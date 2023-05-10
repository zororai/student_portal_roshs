<?php

namespace App\Http\Controllers;


use App\Payment; // Ensure the model namespace is correct
use ConsoleTVs\Charts\Classes\Chartjs\Chart; // Correct class

class ReportController extends Controller
{
    public function index()
    {
        // Fetch data for reports
        $totalCollected = Payment::where('status', 'paid')->sum('amount');
        $totalOutstanding = Payment::where('status', 'outstanding')->sum('amount');
        
        $paymentTrends = Payment::selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Create a chart for payment trends
        $chart = new Chart;
        $chart->labels($paymentTrends->pluck('date')->toArray());
        $chart->dataset('Payment Trends', 'line', $paymentTrends->pluck('total')->toArray());

        return view('reports.index', compact('totalCollected', 'totalOutstanding', 'chart'));
    }
}
