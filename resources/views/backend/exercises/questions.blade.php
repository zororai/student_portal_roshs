@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ route('exercises.show', $exercise->id) }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Exercise
                </a>
                <h1 class="mt-4 text-3xl font-bold text-gray-900">{{ $exercise->title }}</h1>
                <p class="text-gray-600">Manage questions for this exercise</p>
            </div>
            <div class="flex items-center space-x-3">
                @if($exercise->questions->count() > 0)
                    @if(!$exercise->is_published)
                        <form action="{{ route('exercises.toggle-publish', $exercise->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-5 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Done - Publish Exercise
                            </button>
                        </form>
                    @else
                        <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-800 text-sm font-semibold rounded-lg">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Published
                        </span>
                    @endif
                @endif
                <a href="{{ route('exercises.show', $exercise->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg transition-colors">
                    View Exercise Details
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Questions ({{ $exercise->questions->count() }})</h2>
                <p class="text-sm text-gray-500 mb-6">Total Marks: {{ $exercise->total_marks }}</p>

                @forelse($exercise->questions as $index => $question)
                    <div class="border border-gray-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="text-sm font-medium text-gray-500">Q{{ $index + 1 }}.</span>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700">
                                        {{ $question->getQuestionTypeLabel() }}
                                    </span>
                                    <span class="text-xs text-gray-500">({{ $question->marks }} marks)</span>
                                </div>
                                <p class="text-gray-900">{!! nl2br(e($question->question_text)) !!}</p>
                                
                                @if($question->question_image)
                                    <img src="{{ Storage::url($question->question_image) }}" alt="Question Image" class="mt-2 max-w-xs rounded-lg">
                                @endif

                                @if($question->question_type == 'multiple_choice' || $question->question_type == 'true_false')
                                    <div class="mt-3 space-y-2">
                                        @foreach($question->options as $option)
                                            <div class="flex items-center space-x-2">
                                                <span class="w-6 h-6 flex items-center justify-center rounded-full text-xs {{ $option->is_correct ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                                    {{ chr(65 + $loop->index) }}
                                                </span>
                                                <span class="{{ $option->is_correct ? 'text-green-700 font-medium' : 'text-gray-700' }}">
                                                    {{ $option->option_text }}
                                                </span>
                                                @if($option->is_correct)
                                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($question->question_type == 'short_answer' && $question->correct_answer)
                                    <p class="mt-2 text-sm text-green-600">Expected answer: {{ $question->correct_answer }}</p>
                                @endif
                            </div>
                            <form action="{{ route('exercises.questions.destroy', [$exercise->id, $question->id]) }}" method="POST" 
                                onsubmit="return confirm('Delete this question?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-2">No questions added yet. Add your first question using the form.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6 sticky top-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Add New Question</h2>
                
                <form action="{{ route('exercises.questions.store', $exercise->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Question Type *</label>
                            <select name="question_type" id="question_type" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="short_answer">Short Answer</option>
                                <option value="file_upload">File Upload</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Question *</label>
                            <textarea name="question_text" rows="3" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Enter your question..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Marks *</label>
                            <input type="number" name="marks" value="1" min="1" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Image (optional)</label>
                            <input type="file" name="question_image" accept="image/*"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <div id="mcq-options" class="space-y-3">
                            <label class="block text-sm font-medium text-gray-700">Options</label>
                            <div class="space-y-2">
                                @for($i = 0; $i < 4; $i++)
                                    <div class="flex items-center space-x-2">
                                        <input type="radio" name="correct_option" value="{{ $i }}" class="text-blue-600">
                                        <input type="text" name="options[]" placeholder="Option {{ chr(65 + $i) }}"
                                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                                    </div>
                                @endfor
                            </div>
                            <p class="text-xs text-gray-500">Select the correct answer</p>
                        </div>

                        <div id="true-false-options" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
                            <select name="correct_answer" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="true">True</option>
                                <option value="false">False</option>
                            </select>
                        </div>

                        <div id="short-answer-field" class="hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expected Answer (optional)</label>
                            <input type="text" name="short_answer_correct" id="short_answer_input"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg"
                                placeholder="For reference only">
                        </div>

                        <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Add Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('question_type').addEventListener('change', function() {
    const mcqOptions = document.getElementById('mcq-options');
    const tfOptions = document.getElementById('true-false-options');
    const shortAnswer = document.getElementById('short-answer-field');
    
    mcqOptions.classList.add('hidden');
    tfOptions.classList.add('hidden');
    shortAnswer.classList.add('hidden');
    
    if (this.value === 'multiple_choice') {
        mcqOptions.classList.remove('hidden');
    } else if (this.value === 'true_false') {
        tfOptions.classList.remove('hidden');
    } else if (this.value === 'short_answer') {
        shortAnswer.classList.remove('hidden');
    }
});
</script>
@endsection
