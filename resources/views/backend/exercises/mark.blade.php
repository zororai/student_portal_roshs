@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('exercises.submissions', $exercise->id) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Submissions
        </a>
        <h1 class="mt-4 text-3xl font-bold text-gray-900">Mark Submission</h1>
        <p class="text-gray-600">{{ $submission->student->user->name ?? 'Student' }} - {{ $exercise->title }}</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <span class="text-sm text-gray-500">Submitted:</span>
                <span class="ml-2 font-medium">{{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y H:i') : 'N/A' }}</span>
            </div>
            <div>
                <span class="text-sm text-gray-500">Current Score:</span>
                <span class="ml-2 font-semibold text-lg">{{ $submission->total_score ?? 0 }}/{{ $exercise->total_marks }}</span>
            </div>
        </div>
    </div>

    <form action="{{ route('exercises.submissions.save-marks', [$exercise->id, $submission->id]) }}" method="POST">
        @csrf

        <div class="space-y-6">
            @foreach($exercise->questions as $index => $question)
                @php
                    $answer = $submission->answers->where('question_id', $question->id)->first();
                @endphp
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Question {{ $index + 1 }}</span>
                            <span class="ml-2 px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                                {{ $question->getQuestionTypeLabel() }}
                            </span>
                        </div>
                        <span class="text-sm text-gray-500">Max: {{ $question->marks }} marks</span>
                    </div>

                    <p class="text-gray-900 font-medium mb-4">{{ $question->question_text }}</p>

                    @if($question->question_image)
                        <img src="{{ Storage::url($question->question_image) }}" alt="Question Image" class="mb-4 max-w-md rounded-lg">
                    @endif

                    @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                        <div class="mb-4 space-y-2">
                            @foreach($question->options as $option)
                                <div class="flex items-center space-x-3 p-3 rounded-lg {{ $option->is_correct ? 'bg-green-50 border border-green-200' : 'bg-gray-50' }}">
                                    <span class="w-6 h-6 flex items-center justify-center rounded-full text-xs {{ $option->is_correct ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                        {{ chr(65 + $loop->index) }}
                                    </span>
                                    <span class="{{ $option->is_correct ? 'text-green-700 font-medium' : 'text-gray-700' }}">
                                        {{ $option->option_text }}
                                    </span>
                                    @if($answer && $answer->selected_option_id == $option->id)
                                        <span class="ml-auto px-2 py-1 text-xs rounded {{ $option->is_correct ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            Student's Answer
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @elseif($question->question_type == 'short_answer')
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Student's Answer:</p>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                {{ $answer->answer_text ?? 'No answer provided' }}
                            </div>
                            @if($question->correct_answer)
                                <p class="mt-2 text-sm text-green-600">Expected: {{ $question->correct_answer }}</p>
                            @endif
                        </div>
                    @elseif($question->question_type == 'file_upload')
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Uploaded File:</p>
                            @if($answer && $answer->file_path)
                                <a href="{{ Storage::url($answer->file_path) }}" target="_blank" 
                                    class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    View File
                                </a>
                            @else
                                <span class="text-gray-400">No file uploaded</span>
                            @endif
                        </div>
                    @endif

                    <div class="border-t border-gray-200 pt-4 mt-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Marks Awarded</label>
                                <input type="number" name="marks[{{ $answer->id ?? 'new_'.$question->id }}]" 
                                    value="{{ $answer->marks_awarded ?? '' }}" min="0" max="{{ $question->marks }}" step="0.5"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="0 - {{ $question->marks }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Feedback (optional)</label>
                                <input type="text" name="feedback[{{ $answer->id ?? 'new_'.$question->id }}]" 
                                    value="{{ $answer->feedback ?? '' }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Brief feedback...">
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Overall Feedback</label>
            <textarea name="teacher_feedback" rows="3"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="General feedback for the student...">{{ $submission->teacher_feedback }}</textarea>
        </div>

        <div class="mt-6 flex items-center justify-end space-x-4">
            <a href="{{ route('exercises.submissions', $exercise->id) }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Save Marks
            </button>
        </div>
    </form>
</div>
@endsection
