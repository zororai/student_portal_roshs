@extends('layouts.app')

@section('content')
    <div class="create-results-status">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-gray-700 uppercase font-bold">Create Results Status</h2>
            </div>
        </div>

        <form action="{{ route('results_status.store') }}" method="POST">
            @csrf
            <div class="mt-4 bg-white rounded border border-gray-300 p-6">
                <div class="form-group mb-4">
                    <label for="year" class="block text-sm font-semibold text-gray-700">Year</label>
                    
                   <div class="mb-4">
                        <label for="result_period" class="block text-gray-700 font-bold mb-2">Select Result Period:</label>
                        <select name="year"  id="year" class="border rounded px-2 py-1">
                            <option value="">Select year</option>
                            <option value="2025">2025</option>
                            <option value="2026">2026</option>
                            <option value="2027">2027</option>
                            <option value="2028">2028</option>
                            <option value="2029">2029</option>
                            <option value="2030">2030</option>
                            
                        </select>
                    </div>
                    
                    @error('year')
                        <div class="text-danger text-red-500 text-sm">{{ $message }}</div>
                    @enderror
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
             
                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary bg-blue-500 text-white py-2 px-4 rounded">Submit</button>
                    <a href="{{ route('results_status.index') }}" class="btn btn-secondary bg-gray-300 text-gray-700 py-2 px-4 rounded ml-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection