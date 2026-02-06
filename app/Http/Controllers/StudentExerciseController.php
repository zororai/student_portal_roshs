<?php

namespace App\Http\Controllers;

use App\Exercise;
use App\ExerciseSubmission;
use App\ExerciseAnswer;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentExerciseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $student = Student::where('user_id', Auth::id())->first();
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student profile not found.');
        }

        $exercises = Exercise::with(['subject', 'teacher.user'])
            ->where('class_id', $student->class_id)
            ->where('is_published', true)
            ->orderBy('due_date', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get submissions for this student
        $submissions = ExerciseSubmission::where('student_id', $student->id)
            ->whereIn('exercise_id', $exercises->pluck('id'))
            ->get()
            ->keyBy('exercise_id');

        return view('backend.student-exercises.index', compact('exercises', 'submissions', 'student'));
    }

    public function show(Exercise $exercise)
    {
        $student = Student::where('user_id', Auth::id())->first();
        $this->authorizeStudent($exercise, $student);

        $submission = $exercise->getSubmissionForStudent($student->id);

        return view('backend.student-exercises.show', compact('exercise', 'submission', 'student'));
    }

    public function attempt(Exercise $exercise)
    {
        $student = Student::where('user_id', Auth::id())->first();
        $this->authorizeStudent($exercise, $student);

        // Check if already submitted
        $submission = $exercise->getSubmissionForStudent($student->id);
        if ($submission && in_array($submission->status, ['submitted', 'marked'])) {
            return redirect()->route('student.exercises.results', $exercise->id)
                ->with('info', 'You have already submitted this exercise.');
        }

        // Check if overdue
        if ($exercise->isOverdue()) {
            return redirect()->route('student.exercises.show', $exercise->id)
                ->with('error', 'This exercise is past its due date.');
        }

        // Create or get submission
        if (!$submission) {
            $submission = ExerciseSubmission::create([
                'exercise_id' => $exercise->id,
                'student_id' => $student->id,
                'started_at' => now(),
                'status' => 'in_progress',
            ]);
        } elseif ($submission->status === 'not_started') {
            $submission->update([
                'started_at' => now(),
                'status' => 'in_progress',
            ]);
        }

        $exercise->load(['questions.options']);
        $submission->load('answers');

        return view('backend.student-exercises.attempt', compact('exercise', 'submission', 'student'));
    }

    public function saveAnswer(Request $request, Exercise $exercise)
    {
        $student = Student::where('user_id', Auth::id())->first();
        $this->authorizeStudent($exercise, $student);

        $submission = $exercise->getSubmissionForStudent($student->id);
        if (!$submission || $submission->status !== 'in_progress') {
            return response()->json(['error' => 'Invalid submission state'], 400);
        }

        $request->validate([
            'question_id' => 'required|exists:exercise_questions,id',
            'answer_text' => 'nullable|string',
            'selected_option_id' => 'nullable|exists:exercise_question_options,id',
        ]);

        $answer = ExerciseAnswer::updateOrCreate(
            [
                'submission_id' => $submission->id,
                'question_id' => $request->question_id,
            ],
            [
                'answer_text' => $request->answer_text,
                'selected_option_id' => $request->selected_option_id,
            ]
        );

        return response()->json(['success' => true, 'answer_id' => $answer->id]);
    }

    public function uploadFile(Request $request, Exercise $exercise)
    {
        $student = Student::where('user_id', Auth::id())->first();
        $this->authorizeStudent($exercise, $student);

        $submission = $exercise->getSubmissionForStudent($student->id);
        if (!$submission || $submission->status !== 'in_progress') {
            return response()->json(['error' => 'Invalid submission state'], 400);
        }

        $request->validate([
            'question_id' => 'required|exists:exercise_questions,id',
            'file' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx',
        ]);

        $path = $request->file('file')->store('exercise_uploads/' . $student->id, 'public');

        $answer = ExerciseAnswer::updateOrCreate(
            [
                'submission_id' => $submission->id,
                'question_id' => $request->question_id,
            ],
            [
                'file_path' => $path,
            ]
        );

        return response()->json([
            'success' => true,
            'answer_id' => $answer->id,
            'file_url' => Storage::url($path),
        ]);
    }

    public function submit(Request $request, Exercise $exercise)
    {
        $student = Student::where('user_id', Auth::id())->first();
        $this->authorizeStudent($exercise, $student);

        $submission = $exercise->getSubmissionForStudent($student->id);
        if (!$submission) {
            return redirect()->route('student.exercises.show', $exercise->id)
                ->with('error', 'No submission found.');
        }

        if ($submission->status === 'submitted' || $submission->status === 'marked') {
            return redirect()->route('student.exercises.results', $exercise->id)
                ->with('info', 'Already submitted.');
        }

        // Process all answers from form submission
        if ($request->has('answers')) {
            foreach ($request->answers as $questionId => $answerData) {
                $data = [
                    'submission_id' => $submission->id,
                    'question_id' => $questionId,
                ];

                if (isset($answerData['selected_option_id'])) {
                    $data['selected_option_id'] = $answerData['selected_option_id'];
                }
                if (isset($answerData['answer_text'])) {
                    $data['answer_text'] = $answerData['answer_text'];
                }

                ExerciseAnswer::updateOrCreate(
                    [
                        'submission_id' => $submission->id,
                        'question_id' => $questionId,
                    ],
                    $data
                );
            }
        }

        // Handle file uploads
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $questionId => $file) {
                $path = $file->store('exercise_uploads/' . $student->id, 'public');
                
                ExerciseAnswer::updateOrCreate(
                    [
                        'submission_id' => $submission->id,
                        'question_id' => $questionId,
                    ],
                    [
                        'file_path' => $path,
                    ]
                );
            }
        }

        // Auto-mark MCQ and True/False questions
        $submission->load(['answers.question.options']);
        $autoScore = $submission->calculateAutoMarks();

        $submission->update([
            'submitted_at' => now(),
            'status' => 'submitted',
            'total_score' => $autoScore,
        ]);

        return redirect()->route('student.exercises.results', $exercise->id)
            ->with('success', 'Exercise submitted successfully!');
    }

    public function saveAndExit(Request $request, Exercise $exercise)
    {
        $student = Student::where('user_id', Auth::id())->first();
        $this->authorizeStudent($exercise, $student);

        $submission = $exercise->getSubmissionForStudent($student->id);
        if (!$submission || $submission->status !== 'in_progress') {
            return redirect()->route('student.exercises.index')
                ->with('error', 'Invalid submission state.');
        }

        // Save all answers
        if ($request->has('answers')) {
            foreach ($request->answers as $questionId => $answerData) {
                $updateData = [];

                if (isset($answerData['selected_option_id'])) {
                    $updateData['selected_option_id'] = $answerData['selected_option_id'];
                }
                if (isset($answerData['answer_text'])) {
                    $updateData['answer_text'] = $answerData['answer_text'];
                }

                if (!empty($updateData)) {
                    ExerciseAnswer::updateOrCreate(
                        [
                            'submission_id' => $submission->id,
                            'question_id' => $questionId,
                        ],
                        $updateData
                    );
                }
            }
        }

        // Save remaining time
        $timeRemaining = $request->input('time_remaining_seconds');
        if ($timeRemaining !== null && $timeRemaining !== '' && is_numeric($timeRemaining)) {
            $submission->time_remaining_seconds = max(0, (int)$timeRemaining);
            $submission->save();
        }

        return redirect()->route('student.exercises.index')
            ->with('success', 'Progress saved! You can continue later.');
    }

    public function results(Exercise $exercise)
    {
        $student = Student::where('user_id', Auth::id())->first();
        $this->authorizeStudent($exercise, $student);

        $submission = $exercise->getSubmissionForStudent($student->id);
        
        if (!$submission || $submission->status === 'not_started') {
            return redirect()->route('student.exercises.show', $exercise->id)
                ->with('error', 'You have not attempted this exercise yet.');
        }

        if ($submission->status === 'in_progress') {
            return redirect()->route('student.exercises.attempt', $exercise->id);
        }

        // Only show results if teacher has enabled it or exercise is marked
        if (!$exercise->show_results && $submission->status !== 'marked') {
            return view('backend.student-exercises.pending', compact('exercise', 'submission', 'student'));
        }

        $submission->load(['answers.question.options', 'answers.selectedOption']);
        $exercise->load(['questions.options']);

        return view('backend.student-exercises.results', compact('exercise', 'submission', 'student'));
    }

    private function authorizeStudent(Exercise $exercise, $student)
    {
        if (!$student) {
            abort(403, 'Student profile not found.');
        }

        if (!$exercise->is_published) {
            abort(404, 'Exercise not found.');
        }

        if ($exercise->class_id !== $student->class_id) {
            abort(403, 'This exercise is not assigned to your class.');
        }
    }
}
