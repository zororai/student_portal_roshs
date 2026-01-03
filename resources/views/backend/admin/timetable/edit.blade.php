@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                Edit {{ $class->class_name }} Timetable
            </h1>
            <p class="text-gray-500 mt-1 ml-13">Assign subjects and teachers to time slots</p>
        </div>
        <a href="{{ route('admin.timetable.show', $class->id) }}" class="mt-4 md:mt-0 inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to View
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('warning'))
        <div class="bg-amber-100 border-l-4 border-amber-500 text-amber-700 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('warning') }}
            </div>
        </div>
    @endif

    <form action="{{ route('admin.timetable.update', $class->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Timetable Grid -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px]">
                    <thead>
                        <tr class="bg-gradient-to-r from-amber-500 to-orange-600">
                            <th class="px-4 py-4 text-left text-xs font-semibold text-white uppercase tracking-wider w-24">Time</th>
                            @foreach($days as $day)
                                <th class="px-4 py-4 text-center text-xs font-semibold text-white uppercase tracking-wider">{{ substr($day, 0, 3) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @php
                            $maxSlots = 0;
                            foreach($days as $day) {
                                if(isset($timetable[$day]) && $timetable[$day]->count() > $maxSlots) {
                                    $maxSlots = $timetable[$day]->count();
                                }
                            }
                            $slotIndex = 0;
                        @endphp

                        @for($i = 0; $i < $maxSlots; $i++)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-500">
                                    @if(isset($timetable['Monday'][$i]))
                                        {{ \Carbon\Carbon::parse($timetable['Monday'][$i]->start_time)->format('H:i') }}
                                        <br>
                                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($timetable['Monday'][$i]->end_time)->format('H:i') }}</span>
                                    @endif
                                </td>
                                @foreach($days as $day)
                                    <td class="px-2 py-2">
                                        @if(isset($timetable[$day][$i]))
                                            @php $slot = $timetable[$day][$i]; @endphp
                                            
                                            @if($slot->slot_type == 'break')
                                                <div class="bg-amber-100 border border-amber-200 rounded-xl p-3 text-center">
                                                    <p class="font-semibold text-amber-700">☕ Break</p>
                                                </div>
                                            @elseif($slot->slot_type == 'lunch')
                                                <div class="bg-orange-100 border border-orange-200 rounded-xl p-3 text-center">
                                                    <p class="font-semibold text-orange-700">🍽️ Lunch</p>
                                                </div>
                                            @elseif($slot->slot_type == 'subject')
                                                <div class="bg-white border-2 border-gray-200 rounded-xl p-3 space-y-2">
                                                    <input type="hidden" name="slots[{{ $slotIndex }}][id]" value="{{ $slot->id }}">
                                                    
                                                    <select name="slots[{{ $slotIndex }}][subject_id]" 
                                                            class="w-full px-2 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                                                        <option value="">Select Subject</option>
                                                        @foreach($subjects as $subject)
                                                            <option value="{{ $subject->id }}" {{ $slot->subject_id == $subject->id ? 'selected' : '' }}>
                                                                {{ $subject->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    
                                                    <select name="slots[{{ $slotIndex }}][teacher_id]" 
                                                            class="w-full px-2 py-1.5 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent teacher-select"
                                                            data-day="{{ $day }}"
                                                            data-start="{{ $slot->start_time }}"
                                                            data-end="{{ $slot->end_time }}"
                                                            data-slot-id="{{ $slot->id }}">
                                                        <option value="">Select Teacher</option>
                                                        @foreach($teachers as $teacher)
                                                            <option value="{{ $teacher->id }}" {{ $slot->teacher_id == $teacher->id ? 'selected' : '' }}>
                                                                {{ $teacher->user->name ?? 'Unknown' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @php $slotIndex++; @endphp
                                            @else
                                                <div class="bg-gray-50 border border-gray-200 rounded-xl p-3 text-center">
                                                    <p class="text-gray-400 text-sm">Free</p>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Conflict Warning -->
        <div id="conflictWarning" class="hidden bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg shadow-lg animate-pulse">
            <div class="flex items-center">
                <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-bold text-lg">⚠️ Scheduling Conflict Detected!</p>
                    <p id="conflictMessage" class="mt-1">Teacher has a conflict at this time!</p>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-r-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-semibold">Smart Scheduling Enabled</p>
                    <p class="text-sm mt-1">The system will automatically check for teacher conflicts across all classes. Teachers cannot be assigned to multiple classes at the same time and day.</p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end gap-4">
            <a href="{{ route('admin.timetable.show', $class->id) }}" 
               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" 
                    class="px-8 py-3 bg-gradient-to-r from-amber-500 to-orange-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all">
                Save Changes
            </button>
        </div>
    </form>

    <!-- Delete Timetable -->
    <div class="mt-8 pt-6 border-t border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-red-600">Danger Zone</h3>
                <p class="text-sm text-gray-500">Delete this timetable permanently</p>
            </div>
            <form action="{{ route('admin.timetable.destroy', $class->id) }}" method="POST" 
                  onsubmit="return confirm('Are you sure you want to delete this timetable? This action cannot be undone.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-100 text-red-600 rounded-xl font-semibold hover:bg-red-200 transition-colors">
                    Delete Timetable
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Check for teacher conflicts when selecting a teacher
    document.querySelectorAll('.teacher-select').forEach(select => {
        select.addEventListener('change', function() {
            const teacherId = this.value;
            const day = this.dataset.day;
            const startTime = this.dataset.start;
            const endTime = this.dataset.end;
            const slotId = this.dataset.slotId;
            const selectElement = this;

            // Remove any existing conflict styling
            selectElement.classList.remove('border-red-500', 'bg-red-50');
            selectElement.parentElement.classList.remove('ring-2', 'ring-red-500');

            if (!teacherId) {
                document.getElementById('conflictWarning').classList.add('hidden');
                return;
            }

            // Check for conflicts with other slots in the same form (current timetable)
            let hasLocalConflict = false;
            document.querySelectorAll('.teacher-select').forEach(otherSelect => {
                if (otherSelect !== selectElement && 
                    otherSelect.value === teacherId && 
                    otherSelect.dataset.day === day &&
                    otherSelect.dataset.start === startTime) {
                    hasLocalConflict = true;
                }
            });

            if (hasLocalConflict) {
                selectElement.classList.add('border-red-500', 'bg-red-50');
                selectElement.parentElement.classList.add('ring-2', 'ring-red-500');
                document.getElementById('conflictWarning').classList.remove('hidden');
                document.getElementById('conflictMessage').textContent = 
                    'Warning: This teacher is already assigned at ' + startTime + ' on ' + day + ' in this timetable!';
                return;
            }

            // Check for conflicts with other classes via AJAX
            fetch('{{ route("admin.timetable.check-conflicts") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    teacher_id: teacherId,
                    day: day,
                    start_time: startTime,
                    end_time: endTime,
                    exclude_id: slotId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.has_conflict) {
                    selectElement.classList.add('border-red-500', 'bg-red-50');
                    selectElement.parentElement.classList.add('ring-2', 'ring-red-500');
                    document.getElementById('conflictWarning').classList.remove('hidden');
                    document.getElementById('conflictMessage').textContent = 
                        'Warning: This teacher is already assigned to another class at this time on ' + day + '!';
                } else {
                    document.getElementById('conflictWarning').classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error checking conflicts:', error);
            });
        });
    });

    // Prevent form submission if there are conflicts
    document.querySelector('form').addEventListener('submit', function(e) {
        const conflictWarning = document.getElementById('conflictWarning');
        if (!conflictWarning.classList.contains('hidden')) {
            e.preventDefault();
            alert('Please resolve teacher conflicts before saving the timetable.');
            return false;
        }

        // Check for any red-bordered selects (visual conflict indicators)
        const conflictedSelects = document.querySelectorAll('.teacher-select.border-red-500');
        if (conflictedSelects.length > 0) {
            e.preventDefault();
            alert('Please resolve all teacher scheduling conflicts before saving.');
            return false;
        }
    });
</script>
@endpush
@endsection
