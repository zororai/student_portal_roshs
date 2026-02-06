@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $exercise->title }}</h1>
                <p class="text-gray-600">{{ $exercise->subject->subject_name ?? 'Subject' }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Total Marks: {{ $exercise->total_marks }}</p>
                @if($exercise->duration_minutes)
                    <p class="text-sm text-orange-600" id="timer">Time: {{ $exercise->duration_minutes }}:00</p>
                @endif
            </div>
        </div>
    </div>

    @if($exercise->instructions)
        <div class="bg-blue-50 rounded-lg p-4 mb-6">
            <h3 class="font-medium text-blue-900 mb-2">Instructions</h3>
            <p class="text-blue-800 text-sm">{!! nl2br(e($exercise->instructions)) !!}</p>
        </div>
    @endif

    <form action="{{ route('student.exercises.submit', $exercise->id) }}" method="POST" enctype="multipart/form-data" id="exerciseForm">
        @csrf

        <div class="space-y-6">
            @foreach($exercise->questions as $index => $question)
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Question {{ $index + 1 }} of {{ $exercise->questions->count() }}</span>
                            <span class="ml-2 px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                                {{ $question->marks }} mark{{ $question->marks > 1 ? 's' : '' }}
                            </span>
                        </div>
                    </div>

                    <p class="text-lg text-gray-900 mb-4">{{ $question->question_text }}</p>

                    @if($question->question_image)
                        <img src="{{ Storage::url($question->question_image) }}" alt="Question Image" class="mb-4 max-w-md rounded-lg">
                    @endif

                    @if($question->question_type == 'multiple_choice')
                        <div class="space-y-3">
                            @foreach($question->options as $option)
                                <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" name="answers[{{ $question->id }}][selected_option_id]" value="{{ $option->id }}"
                                        class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-3 text-gray-700">{{ $option->option_text }}</span>
                                </label>
                            @endforeach
                        </div>

                    @elseif($question->question_type == 'true_false')
                        <div class="space-y-3">
                            @foreach($question->options as $option)
                                <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                    <input type="radio" name="answers[{{ $question->id }}][selected_option_id]" value="{{ $option->id }}"
                                        class="w-5 h-5 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    <span class="ml-3 text-gray-700">{{ $option->option_text }}</span>
                                </label>
                            @endforeach
                        </div>

                    @elseif($question->question_type == 'short_answer')
                        <textarea name="answers[{{ $question->id }}][answer_text]" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Type your answer here..."></textarea>

                    @elseif($question->question_type == 'file_upload')
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <div class="mt-4">
                                <label class="cursor-pointer">
                                    <span class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Choose File</span>
                                    <input type="file" name="files[{{ $question->id }}]" class="hidden" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                </label>
                            </div>
                            <p class="mt-2 text-sm text-gray-500">PDF, Images, or Word documents up to 10MB</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex items-center justify-between">
            <a href="{{ route('student.exercises.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50"
                onclick="return confirm('Are you sure you want to leave? Your progress may be lost.')">
                Save & Exit
            </a>
            <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                onclick="return confirm('Are you sure you want to submit? You cannot change your answers after submission.')">
                Submit Exercise
            </button>
        </div>
    </form>
</div>

@if($exercise->duration_minutes)
<script>
    let timeLeft = {{ $exercise->duration_minutes }} * 60;
    const timerDisplay = document.getElementById('timer');
    
    const countdown = setInterval(function() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        
        timerDisplay.textContent = `Time: ${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        if (timeLeft <= 300) {
            timerDisplay.classList.add('text-red-600', 'font-bold');
        }
        
        if (timeLeft <= 0) {
            clearInterval(countdown);
            alert('Time is up! Your exercise will be submitted automatically.');
            document.getElementById('exerciseForm').submit();
        }
        
        timeLeft--;
    }, 1000);
</script>
@endif
@endsection
