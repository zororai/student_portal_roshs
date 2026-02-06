@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('student.exercises.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Exercises
        </a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900">{{ $exercise->title }}</h1>
        <p class="text-gray-600">{{ $exercise->subject->subject_name ?? 'Subject' }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Your Score</p>
                <p class="text-4xl font-bold text-gray-900">
                    {{ $submission->total_score ?? 0 }}<span class="text-2xl text-gray-500">/{{ $exercise->total_marks }}</span>
                </p>
                @if($submission->total_score !== null)
                    <p class="text-lg text-{{ $submission->getPercentageScore() >= 50 ? 'green' : 'red' }}-600">
                        {{ $submission->getPercentageScore() }}%
                    </p>
                @endif
            </div>
            <div class="text-right">
                {!! $submission->status_badge !!}
                <p class="mt-2 text-sm text-gray-500">
                    Submitted: {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y H:i') : 'N/A' }}
                </p>
            </div>
        </div>

        @if($submission->teacher_feedback)
            <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                <h3 class="font-medium text-blue-900 mb-2">Teacher's Feedback</h3>
                <p class="text-blue-800">{{ $submission->teacher_feedback }}</p>
            </div>
        @endif
    </div>

    @if($exercise->show_results || $submission->status == 'marked')
        <div class="space-y-6">
            @foreach($exercise->questions as $index => $question)
                @php
                    $answer = $submission->answers->where('question_id', $question->id)->first();
                    $isCorrect = $answer && $answer->is_correct;
                @endphp
                <div class="bg-white rounded-xl shadow-lg p-6 {{ $answer ? ($isCorrect ? 'border-l-4 border-green-500' : 'border-l-4 border-red-500') : 'border-l-4 border-gray-300' }}">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Question {{ $index + 1 }}</span>
                            <span class="ml-2 text-sm text-gray-400">({{ $question->marks }} marks)</span>
                        </div>
                        @if($answer && $answer->marks_awarded !== null)
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $answer->marks_awarded == $question->marks ? 'bg-green-100 text-green-700' : ($answer->marks_awarded > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                {{ $answer->marks_awarded }}/{{ $question->marks }}
                            </span>
                        @endif
                    </div>

                    <p class="text-lg text-gray-900 mb-4">{{ $question->question_text }}</p>

                    @if($question->question_image)
                        <img src="{{ Storage::url($question->question_image) }}" alt="Question Image" class="mb-4 max-w-md rounded-lg">
                    @endif

                    @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                        <div class="space-y-2">
                            @foreach($question->options as $option)
                                <div class="flex items-center p-3 rounded-lg {{ $option->is_correct ? 'bg-green-50 border border-green-200' : ($answer && $answer->selected_option_id == $option->id ? 'bg-red-50 border border-red-200' : 'bg-gray-50') }}">
                                    <span class="w-6 h-6 flex items-center justify-center rounded-full text-xs {{ $option->is_correct ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                        {{ chr(65 + $loop->index) }}
                                    </span>
                                    <span class="ml-3 {{ $option->is_correct ? 'text-green-700 font-medium' : 'text-gray-700' }}">
                                        {{ $option->option_text }}
                                    </span>
                                    @if($answer && $answer->selected_option_id == $option->id)
                                        <span class="ml-auto text-xs px-2 py-1 rounded {{ $option->is_correct ? 'bg-green-200 text-green-700' : 'bg-red-200 text-red-700' }}">
                                            Your Answer
                                        </span>
                                    @endif
                                    @if($option->is_correct)
                                        <svg class="ml-2 w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                    @elseif($question->question_type == 'short_answer')
                        <div class="p-4 bg-gray-50 rounded-lg mb-2">
                            <p class="text-sm text-gray-500 mb-1">Your Answer:</p>
                            <p class="text-gray-900">{{ $answer->answer_text ?? 'No answer provided' }}</p>
                        </div>
                        @if($question->correct_answer)
                            <div class="p-4 bg-green-50 rounded-lg">
                                <p class="text-sm text-green-600 mb-1">Expected Answer:</p>
                                <p class="text-green-800">{{ $question->correct_answer }}</p>
                            </div>
                        @endif

                    @elseif($question->question_type == 'file_upload')
                        @if($answer && $answer->file_path)
                            <a href="{{ Storage::url($answer->file_path) }}" target="_blank" 
                                class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                View Your Submitted File
                            </a>
                        @else
                            <p class="text-gray-400">No file was uploaded</p>
                        @endif
                    @endif

                    @if($answer && $answer->feedback)
                        <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-600 mb-1">Feedback:</p>
                            <p class="text-blue-800">{{ $answer->feedback }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-yellow-50 rounded-xl p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-yellow-800">Results Not Yet Available</h3>
            <p class="mt-2 text-yellow-700">Your teacher is still reviewing submissions. Check back later to see your detailed results.</p>
        </div>
    @endif
</div>
@endsection
