@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 -m-6 p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center space-x-4 mb-8">
            <a href="{{ route('admin.notifications.index') }}" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border border-gray-100 hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Send Notification</h1>
                <p class="text-gray-500 mt-1">Compose and send announcements</p>
            </div>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <ul class="text-red-700 text-sm list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('admin.notifications.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Notification Title *</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                            placeholder="e.g., Important School Update">
                    </div>

                    <!-- Recipient Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Send To *</label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <label class="relative">
                                <input type="radio" name="recipient_type" value="all" {{ old('recipient_type') == 'all' ? 'checked' : '' }} class="peer sr-only" onchange="toggleRecipientFields()">
                                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-all text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400 peer-checked:text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Whole School</span>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="recipient_type" value="teachers" {{ old('recipient_type') == 'teachers' ? 'checked' : '' }} class="peer sr-only" onchange="toggleRecipientFields()">
                                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-all text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">All Teachers</span>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="recipient_type" value="students" {{ old('recipient_type') == 'students' ? 'checked' : '' }} class="peer sr-only" onchange="toggleRecipientFields()">
                                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-all text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">All Students</span>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="recipient_type" value="parents" {{ old('recipient_type') == 'parents' ? 'checked' : '' }} class="peer sr-only" onchange="toggleRecipientFields()">
                                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-all text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">All Parents</span>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="recipient_type" value="class" {{ old('recipient_type') == 'class' ? 'checked' : '' }} class="peer sr-only" onchange="toggleRecipientFields()">
                                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-all text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Specific Class</span>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="recipient_type" value="individual" {{ old('recipient_type') == 'individual' ? 'checked' : '' }} class="peer sr-only" onchange="toggleRecipientFields()">
                                <div class="p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-indigo-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-all text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Individual</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Class Selection (hidden by default) -->
                    <div id="class-field" class="hidden">
                        <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">Select Class *</label>
                        <select name="class_id" id="class_id" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Individual Selection (hidden by default) -->
                    <div id="individual-field" class="hidden">
                        <label for="recipient_id" class="block text-sm font-medium text-gray-700 mb-2">Select Recipient *</label>
                        <select name="recipient_id" id="recipient_id" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                            <option value="">-- Select Recipient --</option>
                            <optgroup label="Students">
                                @foreach($students as $student)
                                <option value="{{ $student->user->id ?? '' }}" {{ old('recipient_id') == ($student->user->id ?? '') ? 'selected' : '' }}>
                                    {{ $student->user->name ?? 'Unknown' }} ({{ $student->class->class_name ?? 'No Class' }})
                                </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Teachers">
                                @foreach($teachers as $teacher)
                                <option value="{{ $teacher->user->id ?? '' }}" {{ old('recipient_id') == ($teacher->user->id ?? '') ? 'selected' : '' }}>
                                    {{ $teacher->user->name ?? 'Unknown' }}
                                </option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Parents">
                                @foreach($parents as $parent)
                                <option value="{{ $parent->user->id ?? '' }}" {{ old('recipient_id') == ($parent->user->id ?? '') ? 'selected' : '' }}>
                                    {{ $parent->user->name ?? 'Unknown' }}
                                </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Priority *</label>
                        <div class="flex flex-wrap gap-3">
                            <label class="relative">
                                <input type="radio" name="priority" value="low" {{ old('priority') == 'low' ? 'checked' : '' }} class="peer sr-only">
                                <div class="px-4 py-2 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-300 peer-checked:border-gray-500 peer-checked:bg-gray-50 transition-all">
                                    <span class="text-sm font-medium text-gray-600">Low</span>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="priority" value="normal" {{ old('priority', 'normal') == 'normal' ? 'checked' : '' }} class="peer sr-only">
                                <div class="px-4 py-2 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50 transition-all">
                                    <span class="text-sm font-medium text-blue-700">Normal</span>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="priority" value="high" {{ old('priority') == 'high' ? 'checked' : '' }} class="peer sr-only">
                                <div class="px-4 py-2 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-orange-300 peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all">
                                    <span class="text-sm font-medium text-orange-700">High</span>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="priority" value="urgent" {{ old('priority') == 'urgent' ? 'checked' : '' }} class="peer sr-only">
                                <div class="px-4 py-2 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-300 peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                                    <span class="text-sm font-medium text-red-700">Urgent</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Message -->
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                        <textarea name="message" id="message" rows="6" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all resize-none"
                            placeholder="Type your notification message here...">{{ old('message') }}</textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('admin.notifications.index') }}" class="px-5 py-2.5 text-gray-600 hover:text-gray-800 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 shadow-lg">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Send Notification
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleRecipientFields() {
    const recipientType = document.querySelector('input[name="recipient_type"]:checked');
    const classField = document.getElementById('class-field');
    const individualField = document.getElementById('individual-field');
    
    classField.classList.add('hidden');
    individualField.classList.add('hidden');
    
    if (recipientType) {
        if (recipientType.value === 'class') {
            classField.classList.remove('hidden');
        } else if (recipientType.value === 'individual') {
            individualField.classList.remove('hidden');
        }
    }
}

document.addEventListener('DOMContentLoaded', toggleRecipientFields);
</script>
@endsection
