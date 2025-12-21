@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Assign Subjects to Class</h1>
                    <p class="mt-1 text-sm text-gray-500">Manage subject assignments for <span class="font-semibold text-violet-600">{{ $assigned->class_name }}</span></p>
                </div>
                <a href="{{ route('classes.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Classes
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Subject Assignment Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden sticky top-8">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-violet-500 to-purple-600">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                Select Subjects
                            </h3>
                        </div>
                        <form action="{{ route('store.class.assign.subject', $classid) }}" method="POST">
                            @csrf
                            <div class="px-6 py-6">
                                <p class="text-sm text-gray-500 mb-4">Choose subjects to assign to this class:</p>
                                <div class="space-y-2 max-h-96 overflow-y-auto">
                                    @foreach ($subjects as $subject)
                                        @php
                                            $isAssigned = $assigned->subjects->contains('id', $subject->id);
                                        @endphp
                                        <label class="flex items-center p-3 rounded-xl border-2 cursor-pointer transition-all duration-200 hover:border-violet-300 hover:bg-violet-50 {{ $isAssigned ? 'border-violet-500 bg-violet-50' : 'border-gray-200' }}">
                                            <input name="selectedsubjects[]" type="checkbox" value="{{ $subject->id }}"
                                                   class="w-5 h-5 text-violet-600 border-gray-300 rounded focus:ring-violet-500"
                                                   {{ $isAssigned ? 'checked' : '' }}>
                                            <div class="ml-3 flex-1">
                                                <p class="text-sm font-semibold text-gray-900">{{ $subject->name }}</p>
                                                <p class="text-xs text-gray-500">Code: {{ $subject->subject_code }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="px-6 pb-6">
                                <button type="submit" class="w-full px-6 py-3 rounded-lg bg-gradient-to-r from-violet-500 to-purple-600 text-white font-semibold shadow-lg hover:from-violet-600 hover:to-purple-700 hover:shadow-xl transition-all duration-200">
                                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Save Assignments
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Students List Card -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                Students in {{ $assigned->class_name }}
                                <span class="ml-2 px-2.5 py-0.5 rounded-full text-xs font-bold bg-violet-100 text-violet-800">{{ $assigned->students->count() }}</span>
                            </h3>
                        </div>
                        <div class="overflow-x-auto">
                            @if($assigned->students->count() > 0)
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Student</th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Phone</th>
                                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Parent</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-100">
                                        @foreach ($assigned->students as $student)
                                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                                                            {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm font-semibold text-gray-900">{{ $student->user->name }}</p>
                                                            <p class="text-xs text-gray-500">{{ $student->roll_number }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $student->user->email }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $student->phone }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $student->parent->user->name ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="px-6 py-16 text-center">
                                    <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-900 text-lg font-semibold">No students enrolled</p>
                                    <p class="text-gray-500 text-sm mt-1">This class doesn't have any students yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection