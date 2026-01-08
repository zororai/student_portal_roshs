@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        
        <!-- Header Section -->
        <div class="mb-8">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 px-8 py-6">
                    <div class="flex items-center justify-between flex-wrap gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-2">Add Student Results</h1>
                            <p class="text-blue-100 text-sm flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                {{ $student->user->name }} - {{ $class->class_name }}
                            </p>
                        </div>
                        <div class="bg-gradient-to-br from-emerald-400 to-teal-500 rounded-xl px-6 py-3 shadow-lg border-2 border-white">
                            <p class="text-white text-sm font-bold">Class</p>
                            <p class="text-white text-xs mt-1 font-semibold">{{ $class->class_name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <form action="{{ route('teacher.results.store') }}" method="POST">
                @csrf
                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <input type="hidden" name="teacher_id" value="{{ auth()->user()->teacher->id }}">
                <input type="hidden" name="student_id" value="{{ $student->id }}">

                <!-- Term Selection -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-200">
                    <label for="result_period" class="block text-gray-800 font-bold mb-3 text-lg">
                        <svg class="w-5 h-5 inline mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        Select Result Period
                    </label>
                    <select name="result_period" id="result_period" required class="w-full md:w-1/2 px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-indigo-200 focus:border-indigo-500 transition-all text-gray-700 font-medium bg-white shadow-sm">
                        <option value="">Choose a Term</option>
                        <option value="first" {{ isset($lastRecord) && $lastRecord->result_period == 'first' ? 'selected' : '' }}>First Term</option>
                        <option value="second" {{ isset($lastRecord) && $lastRecord->result_period == 'second' ? 'selected' : '' }}>Second Term</option>
                        <option value="third" {{ isset($lastRecord) && $lastRecord->result_period == 'third' ? 'selected' : '' }}>Third Term</option>
                    </select>
                    @if(isset($lastRecord))
                        <p class="text-sm text-gray-600 mt-2">
                            <svg class="w-4 h-4 inline mr-1 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            Current period: <strong>{{ ucfirst($lastRecord->result_period) }} Term {{ $lastRecord->year }}</strong>
                        </p>
                    @endif
                </div>

                <!-- Results Table -->
                <div class="p-8">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <!-- Table Header -->
                            <thead>
                                <tr class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                                    <th class="px-6 py-4 text-left font-bold rounded-tl-xl">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            Subject
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left font-bold">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm9.707 5.707a1 1 0 00-1.414-1.414L9 12.586l-1.293-1.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            Score
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left font-bold">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                            </svg>
                                            Comment
                                        </div>
                                    </th>
                                    <th class="px-6 py-4 text-left font-bold rounded-tr-xl">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            Grade
                                        </div>
                                    </th>
                                </tr>
                            </thead>

                            <!-- Table Body -->
                            <tbody>
                                @foreach($class->subjects as $index => $subject)
                                    @php
                                        $existingResult = isset($existingResults) ? $existingResults->get($subject->id) : null;
                                    @endphp
                                    <tr class="border-b border-gray-200 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-colors {{ $index % 2 == 0 ? 'bg-gray-50' : 'bg-white' }}">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm mr-3">
                                                    {{ strtoupper(substr($subject->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <span class="font-semibold text-gray-800">{{ $subject->name }}</span>
                                                    @if($existingResult)
                                                        <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">Has Data</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="number" 
                                                   name="results[{{ $student->id }}][{{ $subject->id }}][marks]" 
                                                   value="{{ $existingResult ? $existingResult->marks : '' }}"
                                                   required 
                                                   min="0" 
                                                   max="100"
                                                   placeholder="0-100"
                                                   class="w-full px-4 py-2 border-2 {{ $existingResult ? 'border-green-300 bg-green-50' : 'border-gray-300' }} rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all font-medium">
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="text" 
                                                   name="results[{{ $student->id }}][{{ $subject->id }}][comment]" 
                                                   value="{{ $existingResult ? $existingResult->comment : '' }}"
                                                   placeholder="Optional comment"
                                                   class="w-full px-4 py-2 border-2 {{ $existingResult ? 'border-green-300 bg-green-50' : 'border-gray-300' }} rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all">
                                        </td>
                                        <td class="px-6 py-4">
                                            <input type="text" 
                                                   name="results[{{ $student->id }}][{{ $subject->id }}][mark_grade]" 
                                                   value="{{ $existingResult ? $existingResult->mark_grade : '' }}"
                                                   placeholder="A, B, C..."
                                                   class="w-full px-4 py-2 border-2 {{ $existingResult ? 'border-green-300 bg-green-50' : 'border-gray-300' }} rounded-lg focus:ring-4 focus:ring-blue-200 focus:border-blue-500 transition-all font-bold text-center">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-t border-gray-200 flex justify-between items-center flex-wrap gap-4">
                    <a href="{{ url()->previous() }}" 
                       class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl shadow-md hover:shadow-lg transition-all duration-200 border border-gray-300">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Results
                    </button>
                </div>
            </form>
        </div>

        <!-- Help Text -->
        <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-blue-800 font-semibold mb-1">Quick Tips</p>
                    <ul class="text-blue-700 text-sm space-y-1">
                        <li>• Enter scores between 0-100 for each subject</li>
                        <li>• Comments are optional but recommended for detailed feedback</li>
                        <li>• Grade field accepts letter grades (A, B, C, etc.)</li>
                        <li>• All fields with scores are required before saving</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection