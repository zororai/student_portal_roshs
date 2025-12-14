<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StudentApplication;
use App\Subject;

class ApplicationController extends Controller
{
    public function index()
    {
        $subjects = Subject::orderBy('name')->get();
        return view('website.application-form', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'school_applying_for' => 'nullable|string|max:255',
            'previous_school' => 'nullable|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'required|date',
            'applying_for_form' => 'required|string|max:50',
            'religion' => 'nullable|string|max:255',
            'street_address' => 'nullable|string|max:255',
            'residential_area' => 'nullable|string|max:255',
            'subjects_of_interest' => 'nullable|array',
            'guardian_full_name' => 'required|string|max:255',
            'guardian_phone' => 'required|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_relationship' => 'required|string|max:100',
            'birth_entry_number' => 'nullable|string|max:100',
            'dream_job' => 'nullable|string|max:255',
            'expected_start_date' => 'nullable|date',
            'documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        // Handle file uploads
        $documents = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $index => $file) {
                $documentName = $request->input('document_names')[$index] ?? 'Document ' . ($index + 1);
                $path = $file->store('applications/documents', 'public');
                $documents[] = [
                    'name' => $documentName,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName()
                ];
            }
        }

        $application = StudentApplication::create([
            'school_applying_for' => $validated['school_applying_for'] ?? null,
            'previous_school' => $validated['previous_school'] ?? null,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'gender' => $validated['gender'],
            'date_of_birth' => $validated['date_of_birth'],
            'applying_for_form' => $validated['applying_for_form'],
            'religion' => $validated['religion'] ?? null,
            'street_address' => $validated['street_address'] ?? null,
            'residential_area' => $validated['residential_area'] ?? null,
            'subjects_of_interest' => $validated['subjects_of_interest'] ?? null,
            'guardian_full_name' => $validated['guardian_full_name'],
            'guardian_phone' => $validated['guardian_phone'],
            'guardian_email' => $validated['guardian_email'] ?? null,
            'guardian_relationship' => $validated['guardian_relationship'],
            'birth_entry_number' => $validated['birth_entry_number'] ?? null,
            'dream_job' => $validated['dream_job'] ?? null,
            'expected_start_date' => $validated['expected_start_date'] ?? null,
            'documents' => !empty($documents) ? $documents : null,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Your application has been submitted successfully! We will review it and contact you shortly.');
    }

    public function success()
    {
        return view('website.application-success');
    }
}
