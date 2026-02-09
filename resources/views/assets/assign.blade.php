@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('finance.assets.show', $asset) }}" class="text-gray-600 hover:text-gray-800 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Assign Asset</h1>
            <p class="text-gray-600">{{ $asset->asset_code }} - {{ $asset->name }}</p>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Current Assignment</h2>
            <p class="text-gray-700">{{ $asset->assigned_to_name }}</p>
            
            @if($asset->assigned_type)
            <form action="{{ route('finance.assets.unassign', $asset) }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Unassign</button>
            </form>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">New Assignment</h2>
            <form action="{{ route('finance.assets.assign', $asset) }}" method="POST">
                @csrf
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign To *</label>
                        <select name="assigned_type" id="assigned_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" onchange="updateOptions()">
                            <option value="">Select Type</option>
                            <option value="user">Staff User</option>
                            <option value="teacher">Teacher</option>
                            <option value="student">Student</option>
                            <option value="class">Class</option>
                        </select>
                    </div>

                    <div id="user_select" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select User</label>
                        <select name="assigned_id_user" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select User</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="teacher_select" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Teacher</label>
                        <select name="assigned_id_teacher" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name ?? $teacher->user->name ?? 'Teacher #'.$teacher->id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="student_select" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Student</label>
                        <select name="assigned_id_student" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Student</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->name ?? $student->user->name ?? 'Student #'.$student->id }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="class_select" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Class</label>
                        <select name="assigned_id_class" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Class</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" name="assigned_id" id="assigned_id">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Optional notes about this assignment"></textarea>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="w-full px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Assign Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateOptions() {
    const type = document.getElementById('assigned_type').value;
    
    document.getElementById('user_select').classList.add('hidden');
    document.getElementById('teacher_select').classList.add('hidden');
    document.getElementById('student_select').classList.add('hidden');
    document.getElementById('class_select').classList.add('hidden');
    
    if (type) {
        document.getElementById(type + '_select').classList.remove('hidden');
    }
}

document.querySelector('form').addEventListener('submit', function(e) {
    const type = document.getElementById('assigned_type').value;
    const selectName = 'assigned_id_' + type;
    const select = document.querySelector('[name="' + selectName + '"]');
    if (select) {
        document.getElementById('assigned_id').value = select.value;
    }
});
</script>
@endsection
