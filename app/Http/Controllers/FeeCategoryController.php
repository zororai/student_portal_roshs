<?php

namespace App\Http\Controllers;

use App\FeeCategory;
use Illuminate\Http\Request;

class FeeCategoryController extends Controller
{
    // Show the form to create a new fee category
    public function create()
    {
        return view('fee_categories.create');
    }

    // Store a new fee category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:fee_categories',
            'amount' => 'required|numeric',
            'period' => 'required|string',
        ]);

        FeeCategory::create($request->all());

        return redirect()->route('fee_categories.index')->with('success', 'Fee category added successfully!');
    }

    // List all fee categories
    public function index()
    {
        $feeCategories = FeeCategory::all();
        return view('fee_categories.index', compact('feeCategories'));
    }

    // Show the form to edit a fee category
    public function edit($id)
    {
        $feeCategory = FeeCategory::findOrFail($id);
        return view('fee_categories.edit', compact('feeCategory'));
    }

    // Update an existing fee category
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
            'amount' => 'required|numeric',
            'period' => 'required|string',
        ]);

        $feeCategory = FeeCategory::findOrFail($id);
        $feeCategory->update($request->all());

        return redirect()->route('fee_categories.index')->with('success', 'Fee category updated successfully!');
    }

    // Delete a fee category
    public function destroy($id)
    {
        $feeCategory = FeeCategory::findOrFail($id);
        $feeCategory->delete();

        return redirect()->route('fee_categories.index')->with('success', 'Fee category deleted successfully!');
    }
}