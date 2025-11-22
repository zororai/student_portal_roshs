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
             
                    
                       <a href="{{ route('adminresults.yearsubject', $student->id) }}" class="bg-blue-500 text-white px-4 py-1 rounded">
            
                            👁️ View Student Results
                        </a>
                        
           
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection