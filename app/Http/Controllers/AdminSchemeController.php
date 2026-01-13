<?php

namespace App\Http\Controllers;

use App\SchemeOfWork;
use App\SyllabusTopic;
use App\Teacher;
use App\Assessment;
use Illuminate\Http\Request;

class AdminSchemeController extends Controller
{
    /**
     * Display all teacher schemes with progress overview
     */
    public function index(Request $request)
    {
        $query = SchemeOfWork::with(['teacher.user', 'subject', 'class', 'schemeTopics'])
            ->orderBy('created_at', 'desc');

        // Filter by teacher
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by term
        if ($request->filled('term')) {
            $query->where('term', $request->term);
        }

        $schemes = $query->paginate(15);

        // Calculate stats for each scheme
        foreach ($schemes as $scheme) {
            $syllabusTopicIds = $scheme->schemeTopics->pluck('syllabus_topic_id')->filter()->toArray();
            $scheme->assessments_count = Assessment::where('class_id', $scheme->class_id)
                ->where('subject_id', $scheme->subject_id)
                ->whereIn('syllabus_topic_id', $syllabusTopicIds)
                ->count();
        }

        // Get teachers for filter dropdown
        $teachers = Teacher::with('user')->whereHas('user')->get();

        // Summary stats
        $totalSchemes = SchemeOfWork::count();
        $activeSchemes = SchemeOfWork::where('status', 'active')->count();
        $draftSchemes = SchemeOfWork::where('status', 'draft')->count();
        $completedSchemes = SchemeOfWork::where('status', 'completed')->count();

        return view('backend.admin.schemes.index', compact(
            'schemes',
            'teachers',
            'totalSchemes',
            'activeSchemes',
            'draftSchemes',
            'completedSchemes'
        ));
    }

    /**
     * Display a specific scheme details
     */
    public function show($id)
    {
        $scheme = SchemeOfWork::with([
            'teacher.user',
            'subject',
            'class',
            'schemeTopics.syllabusTopic'
        ])->findOrFail($id);

        // Get assessments linked to this scheme's topics
        $syllabusTopicIds = $scheme->schemeTopics->pluck('syllabus_topic_id')->filter()->toArray();
        $assessments = Assessment::where('class_id', $scheme->class_id)
            ->where('subject_id', $scheme->subject_id)
            ->whereIn('syllabus_topic_id', $syllabusTopicIds)
            ->with(['syllabusTopic', 'marks'])
            ->get();

        return view('backend.admin.schemes.show', compact('scheme', 'assessments'));
    }

    /**
     * Display syllabus topics overview for all teachers
     */
    public function syllabusIndex(Request $request)
    {
        $query = SyllabusTopic::with(['subject'])
            ->orderBy('subject_id')
            ->orderBy('term')
            ->orderBy('order_index');

        // Filter by subject
        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by term
        if ($request->filled('term')) {
            $query->where('term', $request->term);
        }

        // Filter by active status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true');
        }

        $topics = $query->paginate(20);

        // Get subjects for filter
        $subjects = \App\Subject::orderBy('name')->get();

        // Stats
        $totalTopics = SyllabusTopic::count();
        $activeTopics = SyllabusTopic::where('is_active', true)->count();
        $topicsWithAssessments = SyllabusTopic::whereHas('assessments')->count();

        return view('backend.admin.schemes.syllabus-index', compact(
            'topics',
            'subjects',
            'totalTopics',
            'activeTopics',
            'topicsWithAssessments'
        ));
    }

    /**
     * Display schemes for a specific teacher
     */
    public function teacherSchemes($teacherId)
    {
        $teacher = Teacher::with('user')->findOrFail($teacherId);

        $schemes = SchemeOfWork::where('teacher_id', $teacherId)
            ->with(['subject', 'class', 'schemeTopics'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate assessments count for each scheme
        foreach ($schemes as $scheme) {
            $syllabusTopicIds = $scheme->schemeTopics->pluck('syllabus_topic_id')->filter()->toArray();
            $scheme->assessments_count = Assessment::where('class_id', $scheme->class_id)
                ->where('subject_id', $scheme->subject_id)
                ->whereIn('syllabus_topic_id', $syllabusTopicIds)
                ->count();
        }

        // Get syllabus topics created by subjects this teacher teaches
        $subjectIds = $teacher->subjects->pluck('id');
        $syllabusTopics = SyllabusTopic::whereIn('subject_id', $subjectIds)
            ->with('subject')
            ->orderBy('subject_id')
            ->orderBy('order_index')
            ->get();

        return view('backend.admin.schemes.teacher', compact('teacher', 'schemes', 'syllabusTopics'));
    }
}
