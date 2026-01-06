<?php

namespace App\Http\Controllers;

use App\FeeType;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $feeTypes = FeeType::latest()->get();
        return view('fee_types.index', compact('feeTypes'));
    }

    public function create()
    {
        return view('fee_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:fee_types',
            'description' => 'nullable|string|max:500',
        ]);

        FeeType::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->route('fee_types.index')->with('success', 'Fee type created successfully.');
    }

    public function edit($id)
    {
        $feeType = FeeType::findOrFail($id);
        return view('fee_types.edit', compact('feeType'));
    }

    public function update(Request $request, $id)
    {
        $feeType = FeeType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:fee_types,name,' . $id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $feeType->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('fee_types.index')->with('success', 'Fee type updated successfully.');
    }

    public function destroy($id)
    {
        $feeType = FeeType::findOrFail($id);
        
        // Check if fee type is in use
        if ($feeType->termFees()->count() > 0) {
            return redirect()->route('fee_types.index')->with('error', 'Cannot delete fee type that is in use.');
        }

        $feeType->delete();

        return redirect()->route('fee_types.index')->with('success', 'Fee type deleted successfully.');
    }
}
