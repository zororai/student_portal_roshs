@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-lg font-bold text-gray-700">Student Results for {{ $classes->class_name }}</h2>

   

@foreach ($classes->students as $student)
    @php
        $studentResult = $results->where('student_id', $student->id)->first();
    @endphp
    
    <div class="w-full sm:w-1/2 ml-2 mb-6 border border-gray-300 rounded p-4">
        <h3 class="text-gray-700 font-bold mb-2">{{ $student->user->name }}</h3>
        
        <div class="flex items-center justify-between">
            <div>
                @if($exists)
                <p class="text-green-500">✅ Results exist for the latest period.</p>
                <br>
            @else
                <p class="text-red-500">❌ No results found for the latest period.</p>
                <br>
            @endif
                @if(!$exists) 
                    {{-- If no result exists for this student but $exists is true, allow adding results --}}
                    <a href="{{ route('results.studentsubject', $student->id) }}" class="bg-blue-500 text-white px-4 py-1 rounded">
                        Add Result
                    </a>
                @elseif($studentResult)
                    <span class="text-green-500">✔ Entered</span>
                    <a href="{{ route('student.results', $student->id) }}" class="text-green-500">
                        👁️ View Student Results
                    </a>
                @else
                    <span class="text-gray-500">Results not available yet.</span>
                @endif
            </div>
        </div>
    </div>
@endforeach

</div>
@endsection