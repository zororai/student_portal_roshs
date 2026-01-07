<?php

namespace App\Http\Controllers;

use App\OnboardSubject;
use Illuminate\Http\Request;

class OnboardSubjectController extends Controller
{
    public function index()
    {
        $onboardSubjects = OnboardSubject::orderBy('name')->paginate(15);
        return view('backend.onboard_subjects.index', compact('onboardSubjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:onboard_subjects,name',
        ]);

        OnboardSubject::create([
            'name' => $request->name,
        ]);

        return redirect()->route('admin.onboard-subjects.index')
            ->with('success', 'Subject "' . $request->name . '" added to onboard list.');
    }

    public function destroy($id)
    {
        $subject = OnboardSubject::findOrFail($id);
        $name = $subject->name;
        $subject->delete();

        return redirect()->route('admin.onboard-subjects.index')
            ->with('success', 'Subject "' . $name . '" removed from onboard list.');
    }

    public function getSubjects()
    {
        return response()->json(OnboardSubject::orderBy('name')->get());
    }
}
