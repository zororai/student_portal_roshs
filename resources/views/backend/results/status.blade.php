@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Update Class Results Status</h1>
    <p>The results of year {{ date('Y') }}</p>
    <form action="{{ route('results.addstatus') }}" method="POST">
        @csrf
        <input type="hidden" name="class_id" value="{{ $results}}">
        <input type="hidden" name="year" value="{{ date('Y') }}">

        <div class="mb-4">
            <label for="result_period" class="block text-gray-700 font-bold mb-2">Select Result Period:</label>
            <select name="result_period" id="result_period" class="border rounded px-2 py-1">
                <option value=""></option>
                <option value="first">First Term</option>
                <option value="second">Second Term</option>
                <option value="third">Third Term</option>
            </select>
        </div>

       

        <button type="submit" class="mt-4 bg-blue-500 text-white rounded px-4 py-2">Save</button>
    </form>
</div>
@endsection