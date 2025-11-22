@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="text-lg font-bold text-gray-700">Active Student Results for Class {{ $classes->class_name }}</h2>

   

@foreach ($classes->students as $student)
 
    <div class="w-full sm:w-1/2 ml-2 mb-6 border border-gray-300 rounded p-4">
        <h3 class="text-gray-700 font-bold mb-2">Student Name : {{ $student->user->name }}</h3>
        
        <div class="flex items-center justify-between">
            <div>
             
                @if(!$exists) 
                <p class="text-red-500">❌ Student have outstanding fee balance.</p>
                <br>
                    {{-- If no result exists for this student but $exists is true, allow adding results --}}
                    <a href="{{ route('viewstudentstatus.results', $student->id) }}" class="bg-blue-500 text-white px-4 py-1 rounded">
                        Active student results
                    </a>
                    
                @else
                <p class="text-green-500">✅ Have  been cleared with fee balance.</p>
                    <a href="{{ route('viewstudent.results', $student->id) }}" class="text-green-500">
                        👁️ View Student Results
                    </a>
             
                @endif
            </div>
        </div>
    </div>
@endforeach

</div>
@endsection