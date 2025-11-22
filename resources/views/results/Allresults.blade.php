@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">View Class Results Status</h1>

    <form action="{{ route('adminresults.classresults') }}" method="POST">
        @csrf
        <input type="hidden" name="class_id" value="{{ $class}}">

        <div class="mb-4">
            <label for="result_year" class="block text-sm font-medium text-gray-700">Result Year</label>
            <select name="year" id="result_period" class="border rounded px-2 py-1">
                <option value="">Select a Year</option>
                @foreach($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>
  

        <div class="mb-4">
            <label for="result_period" class="block text-gray-700 font-bold mb-2">Select Result Period:</label>
            <select name="result_period" id="result_period" class="border rounded px-2 py-1">
                <option value="">Select a Term</option>
                <option value="first">First Term</option>
                <option value="second">Second Term</option>
                <option value="third">Third Term</option>
            </select>
        </div>

       

        <button type="submit" class="mt-4 bg-blue-500 text-white rounded px-4 py-2">Save</button>
    </form>
</div>
@endsection