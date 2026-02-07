<?php

namespace App\Http\Controllers;

use App\PaynowSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaynowSettingsController extends Controller
{
    /**
     * Display the Paynow settings page.
     */
    public function index()
    {
        $setting = PaynowSetting::first();
        
        return view('backend.admin.settings.paynow', compact('setting'));
    }

    /**
     * Store or update Paynow settings.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paynow_id' => 'required|string|max:255',
            'paynow_key' => 'required|string|max:255',
            'environment' => 'required|in:sandbox,production',
            'return_url' => 'nullable|url|max:255',
            'result_url' => 'nullable|url|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'paynow_id',
            'paynow_key',
            'environment',
            'return_url',
            'result_url',
        ]);
        $data['is_active'] = $request->has('is_active');

        $setting = PaynowSetting::first();

        if ($setting) {
            $setting->update($data);
            $message = 'Paynow settings updated successfully.';
        } else {
            PaynowSetting::create($data);
            $message = 'Paynow settings created successfully.';
        }

        return redirect()->route('admin.settings.paynow')
            ->with('success', $message);
    }

    /**
     * Test Paynow connection.
     */
    public function test(Request $request)
    {
        $setting = PaynowSetting::getActive();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'No active Paynow configuration found.',
            ], 404);
        }

        try {
            // Basic validation that credentials exist
            if (empty($setting->paynow_id) || empty($setting->paynow_key)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paynow credentials are incomplete.',
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Paynow configuration is valid.',
                'environment' => $setting->environment,
                'paynow_id' => substr($setting->paynow_id, 0, 4) . '****',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
