<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// FIX: Set "True" as correct answer for all true_false questions that have no correct option
echo "=== Fixing True/False Questions ===\n";

$trueFalseQuestions = App\ExerciseQuestion::where('question_type', 'true_false')->get();

foreach ($trueFalseQuestions as $question) {
    $hasCorrectOption = $question->options()->where('is_correct', true)->exists();
    
    if (!$hasCorrectOption) {
        // Default to "True" as correct - you can change this if needed
        $trueOption = $question->options()->where('option_text', 'True')->first();
        if ($trueOption) {
            $trueOption->update(['is_correct' => true]);
            echo "Fixed Question {$question->id}: Set 'True' as correct\n";
        }
    } else {
        echo "Question {$question->id}: Already has correct answer set\n";
    }
}

echo "\n=== Verification ===\n";
$questions = App\ExerciseQuestion::where('question_type', 'true_false')->with('options')->get();
foreach ($questions as $q) {
    echo "Q{$q->id}: ";
    foreach ($q->options as $o) {
        echo $o->option_text . ($o->is_correct ? '[CORRECT] ' : ' ');
    }
    echo "\n";
}
