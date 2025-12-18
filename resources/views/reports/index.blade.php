@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Student Assessments</h1>

    @if(isset($error))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg">
            <p class="font-medium">{{ $error }}</p>
        </div>
    @endif

    <!-- Print Button -->
    <button onclick="printResults()" class="bg-blue-500 text-white px-4 py-2 rounded mb-4">
        Print Results
    </button>

    <div id="printArea">
        <center>
            <img src="{{ asset('images/logo.png') }}" width="200" height="300" viewBox="0 0 640 512">
        </center>
        <br>

        @if(isset($students) && $students->count() > 0)
            @foreach($students as $student)
                <div class="mb-8 bg-white rounded-lg shadow-md p-4">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                        {{ $student->user->name ?? 'Unknown Student' }} 
                        <span class="text-sm font-normal text-gray-500">
                            ({{ $student->class->class_name ?? 'No Class' }})
                        </span>
                    </h2>

                    @php
                        $studentResults = $results->where('student_id', $student->id);
                        $groupedResults = $studentResults->groupBy(function($item) {
                            return $item->year . ' - ' . $item->result_period;
                        });
                    @endphp

                    @if($studentResults->count() > 0)
                        @foreach($groupedResults as $period => $periodResults)
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-blue-600 mb-2">{{ $period }}</h3>
                                
                                <div class="overflow-x-auto">
                                    <table class="min-w-full bg-white border border-gray-200">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="text-left py-2 px-4 font-semibold text-gray-600 border-b">Subject</th>
                                                <th class="text-left py-2 px-4 font-semibold text-gray-600 border-b">Score</th>
                                                <th class="text-left py-2 px-4 font-semibold text-gray-600 border-b">Grade</th>
                                                <th class="text-left py-2 px-4 font-semibold text-gray-600 border-b">Comment</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($periodResults as $result)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="py-2 px-4 border-b">{{ $result->subject->name ?? 'N/A' }}</td>
                                                    <td class="py-2 px-4 border-b">{{ $result->marks }}</td>
                                                    <td class="py-2 px-4 border-b">
                                                        <span class="px-2 py-1 rounded text-sm 
                                                            @if($result->mark_grade == 'A') bg-green-100 text-green-800
                                                            @elseif($result->mark_grade == 'B') bg-blue-100 text-blue-800
                                                            @elseif($result->mark_grade == 'C') bg-yellow-100 text-yellow-800
                                                            @else bg-gray-100 text-gray-800
                                                            @endif">
                                                            {{ $result->mark_grade ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td class="py-2 px-4 border-b">{{ $result->comment ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-gray-500 italic">No assessments found for this student.</p>
                    @endif
                </div>
            @endforeach
        @else
            @forelse($results as $studentResult)
                <div class="flex items-center justify-between border border-gray-200 mb-2 p-2">
                    <div class="w-1/6 text-left text-gray-600 py-2 px-4 font-medium">
                        {{ $studentResult->subject->name ?? 'N/A' }}  
                    </div>
                    <div class="w-1/6 text-left text-gray-600 py-2 px-4 font-medium">
                        {{ $studentResult->marks }}
                    </div>
                    <div class="w-1/6 text-left text-gray-600 py-2 px-4 font-medium">
                        {{ $studentResult->comment ?? 'N/A' }}
                    </div>
                    <div class="w-1/6 text-left text-gray-600 py-2 px-4 font-medium">
                        {{ $studentResult->mark_grade ?? 'N/A' }}
                    </div>
                    <div class="w-1/6 text-left text-gray-600 py-2 px-4 font-medium">
                        {{ $studentResult->result_period ?? 'N/A' }}
                    </div>
                    <div class="w-1/6 text-left text-gray-600 py-2 px-4 font-medium">
                        {{ $studentResult->year ?? 'N/A' }}
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No results found.</p>
            @endforelse
        @endif
    </div>
</div>

<!-- JavaScript for Printing -->
<script>
    function printResults() {
        var printContents = document.getElementById("printArea").innerHTML;
        var originalContents = document.body.innerHTML;
        
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }
</script>

@endsection
