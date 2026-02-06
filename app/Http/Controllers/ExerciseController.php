<?php

namespace App\Http\Controllers;

use App\Exercise;
use App\ExerciseQuestion;
use App\ExerciseQuestionOption;
use App\ExerciseSubmission;
use App\ExerciseAnswer;
use App\Grade;
use App\Subject;
use App\Student;
use App\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExerciseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $teacher = Teacher::where('user_id', Auth::id())->first();
        
        $exercises = Exercise::with(['class', 'subject', 'submissions'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('backend.exercises.index', compact('exercises'));
    }

    public function create()
    {
        $teacher = Teacher::where('user_id', Auth::id())->first();
        $classes = Grade::orderBy('class_name')->get();
        $subjects = Subject::where('teacher_id', $teacher->id)->orderBy('subject_name')->get();
        
        return view('backend.exercises.create', compact('classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:grades,id',
            'subject_id' => 'required|exists:subjects,id',
            'type' => 'required|in:quiz,classwork,homework',
            'instructions' => 'nullable|string',
            'total_marks' => 'required|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:1',
            'due_date' => 'nullable|date',
        ]);

        $teacher = Teacher::where('user_id', Auth::id())->first();

        $exercise = Exercise::create([
            'teacher_id' => $teacher->id,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'instructions' => $request->instructions,
            'type' => $request->type,
            'total_marks' => $request->total_marks,
            'duration_minutes' => $request->duration_minutes,
            'due_date' => $request->due_date,
            'term' => session('current_term', 'Term 1'),
            'academic_year' => session('current_academic_year', date('Y')),
        ]);

        return redirect()->route('exercises.questions.edit', $exercise->id)
            ->with('success', 'Exercise created. Now add questions.');
    }

    public function show(Exercise $exercise)
    {
        $this->authorizeExercise($exercise);
        
        $exercise->load(['class', 'subject', 'questions.options', 'submissions.student.user']);
        
        $submissionStats = [
            'total_students' => Student::where('class_id', $exercise->class_id)->count(),
            'submitted' => $exercise->submissions()->whereIn('status', ['submitted', 'marked'])->count(),
            'marked' => $exercise->submissions()->where('status', 'marked')->count(),
        ];

        return view('backend.exercises.show', compact('exercise', 'submissionStats'));
    }

    public function edit(Exercise $exercise)
    {
        $this->authorizeExercise($exercise);
        
        $teacher = Teacher::where('user_id', Auth::id())->first();
        $classes = Grade::orderBy('class_name')->get();
        $subjects = Subject::where('teacher_id', $teacher->id)->orderBy('subject_name')->get();
        
        return view('backend.exercises.edit', compact('exercise', 'classes', 'subjects'));
    }

    public function update(Request $request, Exercise $exercise)
    {
        $this->authorizeExercise($exercise);

        $request->validate([
            'title' => 'required|string|max:255',
            'class_id' => 'required|exists:grades,id',
            'subject_id' => 'required|exists:subjects,id',
            'type' => 'required|in:quiz,classwork,homework',
            'instructions' => 'nullable|string',
            'total_marks' => 'required|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:1',
            'due_date' => 'nullable|date',
        ]);

        $exercise->update($request->only([
            'title', 'class_id', 'subject_id', 'type', 'instructions',
            'total_marks', 'duration_minutes', 'due_date'
        ]));

        return redirect()->route('exercises.show', $exercise->id)
            ->with('success', 'Exercise updated successfully.');
    }

    public function destroy(Exercise $exercise)
    {
        $this->authorizeExercise($exercise);
        
        $exercise->delete();

        return redirect()->route('exercises.index')
            ->with('success', 'Exercise deleted successfully.');
    }

    public function editQuestions(Exercise $exercise)
    {
        $this->authorizeExercise($exercise);
        
        $exercise->load(['questions.options']);
        
        return view('backend.exercises.questions', compact('exercise'));
    }

    public function storeQuestion(Request $request, Exercise $exercise)
    {
        $this->authorizeExercise($exercise);

        $request->validate([
            'question_type' => 'required|in:multiple_choice,true_false,short_answer,file_upload',
            'question_text' => 'required|string',
            'marks' => 'required|integer|min:1',
            'correct_answer' => 'nullable|string',
            'question_image' => 'nullable|image|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('question_image')) {
            $imagePath = $request->file('question_image')->store('exercise_images', 'public');
        }

        $maxOrder = $exercise->questions()->max('order') ?? 0;

        $question = ExerciseQuestion::create([
            'exercise_id' => $exercise->id,
            'question_type' => $request->question_type,
            'question_text' => $request->question_text,
            'question_image' => $imagePath,
            'marks' => $request->marks,
            'order' => $maxOrder + 1,
            'correct_answer' => $request->correct_answer,
        ]);

        // Handle MCQ options
        if (in_array($request->question_type, ['multiple_choice', 'true_false'])) {
            if ($request->question_type === 'true_false') {
                ExerciseQuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => 'True',
                    'is_correct' => $request->correct_answer === 'true',
                    'order' => 1,
                ]);
                ExerciseQuestionOption::create([
                    'question_id' => $question->id,
                    'option_text' => 'False',
                    'is_correct' => $request->correct_answer === 'false',
                    'order' => 2,
                ]);
            } elseif ($request->has('options')) {
                foreach ($request->options as $index => $optionText) {
                    if (!empty($optionText)) {
                        ExerciseQuestionOption::create([
                            'question_id' => $question->id,
                            'option_text' => $optionText,
                            'is_correct' => $request->correct_option == $index,
                            'order' => $index + 1,
                        ]);
                    }
                }
            }
        }

        // Update exercise total marks
        $this->recalculateTotalMarks($exercise);

        return redirect()->route('exercises.questions.edit', $exercise->id)
            ->with('success', 'Question added successfully.');
    }

    public function updateQuestion(Request $request, Exercise $exercise, ExerciseQuestion $question)
    {
        $this->authorizeExercise($exercise);

        $request->validate([
            'question_text' => 'required|string',
            'marks' => 'required|integer|min:1',
            'correct_answer' => 'nullable|string',
        ]);

        $question->update([
            'question_text' => $request->question_text,
            'marks' => $request->marks,
            'correct_answer' => $request->correct_answer,
        ]);

        // Update options for MCQ
        if (in_array($question->question_type, ['multiple_choice']) && $request->has('options')) {
            $question->options()->delete();
            foreach ($request->options as $index => $optionText) {
                if (!empty($optionText)) {
                    ExerciseQuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => $request->correct_option == $index,
                        'order' => $index + 1,
                    ]);
                }
            }
        }

        // Update True/False correct answer
        if ($question->question_type === 'true_false') {
            $question->options()->update(['is_correct' => false]);
            $question->options()
                ->where('option_text', $request->correct_answer === 'true' ? 'True' : 'False')
                ->update(['is_correct' => true]);
        }

        $this->recalculateTotalMarks($exercise);

        return redirect()->route('exercises.questions.edit', $exercise->id)
            ->with('success', 'Question updated successfully.');
    }

    public function destroyQuestion(Exercise $exercise, ExerciseQuestion $question)
    {
        $this->authorizeExercise($exercise);
        
        $question->delete();
        $this->recalculateTotalMarks($exercise);

        return redirect()->route('exercises.questions.edit', $exercise->id)
            ->with('success', 'Question deleted successfully.');
    }

    public function togglePublish(Exercise $exercise)
    {
        $this->authorizeExercise($exercise);
        
        $exercise->update(['is_published' => !$exercise->is_published]);

        $status = $exercise->is_published ? 'published' : 'unpublished';
        return redirect()->back()->with('success', "Exercise {$status} successfully.");
    }

    public function toggleResults(Exercise $exercise)
    {
        $this->authorizeExercise($exercise);
        
        $exercise->update(['show_results' => !$exercise->show_results]);

        $status = $exercise->show_results ? 'visible' : 'hidden';
        return redirect()->back()->with('success', "Results are now {$status} to students.");
    }

    public function submissions(Exercise $exercise)
    {
        $this->authorizeExercise($exercise);
        
        $exercise->load(['class', 'subject']);
        
        $students = Student::with(['user'])
            ->where('class_id', $exercise->class_id)
            ->get();

        $submissions = $exercise->submissions()
            ->with(['student.user', 'answers'])
            ->get()
            ->keyBy('student_id');

        return view('backend.exercises.submissions', compact('exercise', 'students', 'submissions'));
    }

    public function markSubmission(Exercise $exercise, ExerciseSubmission $submission)
    {
        $this->authorizeExercise($exercise);
        
        $submission->load(['student.user', 'answers.question.options', 'answers.selectedOption']);
        $exercise->load(['questions.options']);

        return view('backend.exercises.mark', compact('exercise', 'submission'));
    }

    public function saveMarks(Request $request, Exercise $exercise, ExerciseSubmission $submission)
    {
        $this->authorizeExercise($exercise);

        $request->validate([
            'marks' => 'required|array',
            'marks.*' => 'nullable|numeric|min:0',
            'feedback' => 'nullable|array',
            'teacher_feedback' => 'nullable|string',
        ]);

        $totalScore = 0;

        foreach ($request->marks as $answerId => $marks) {
            $answer = ExerciseAnswer::find($answerId);
            if ($answer && $answer->submission_id == $submission->id) {
                $maxMarks = $answer->question->marks;
                $awardedMarks = min($marks ?? 0, $maxMarks);
                
                $answer->update([
                    'marks_awarded' => $awardedMarks,
                    'feedback' => $request->feedback[$answerId] ?? null,
                ]);
                
                $totalScore += $awardedMarks;
            }
        }

        $submission->update([
            'total_score' => $totalScore,
            'status' => 'marked',
            'teacher_feedback' => $request->teacher_feedback,
        ]);

        return redirect()->route('exercises.submissions', $exercise->id)
            ->with('success', 'Marks saved successfully.');
    }

    public function autoMark(Exercise $exercise)
    {
        $this->authorizeExercise($exercise);

        $submissions = $exercise->submissions()
            ->where('status', 'submitted')
            ->with(['answers.question.options'])
            ->get();

        $count = 0;
        foreach ($submissions as $submission) {
            $totalScore = $submission->calculateAutoMarks();
            
            // Check if all questions are auto-markable
            $allAutoMarkable = $exercise->questions->every(function ($q) {
                return $q->isAutoMarkable();
            });

            if ($allAutoMarkable) {
                $submission->update([
                    'total_score' => $totalScore,
                    'status' => 'marked',
                ]);
            } else {
                // Partial auto-marking, still needs manual review
                $submission->update([
                    'total_score' => $totalScore,
                ]);
            }
            $count++;
        }

        return redirect()->back()
            ->with('success', "{$count} submission(s) auto-marked successfully.");
    }

    private function authorizeExercise(Exercise $exercise)
    {
        $teacher = Teacher::where('user_id', Auth::id())->first();
        if ($exercise->teacher_id !== $teacher->id) {
            abort(403, 'Unauthorized access.');
        }
    }

    private function recalculateTotalMarks(Exercise $exercise)
    {
        $totalMarks = $exercise->questions()->sum('marks');
        $exercise->update(['total_marks' => $totalMarks]);
    }
}
