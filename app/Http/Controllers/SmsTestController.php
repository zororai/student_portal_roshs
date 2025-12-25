<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\SmsHelper;

class SmsTestController extends Controller
{
    public function index(Request $request)
    {
        $phone = $request->query('phone');
        $autoSubmit = $request->query('auto', false);
        
        return view('sms-test.index', compact('phone', 'autoSubmit'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|regex:/^\+\d{10,15}$/',
            'message' => 'required|string|max:500'
        ]);

        $result = SmsHelper::sendSms($request->phone, $request->message);

        if ($result['success']) {
            return redirect()->back()->with('success', 'SMS sent successfully! Response: ' . json_encode($result['response']));
        } else {
            return redirect()->back()->with('error', 'Failed to send SMS: ' . ($result['message'] ?? 'Unknown error'));
        }
    }
}
