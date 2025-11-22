@extends('layouts.app')

@section('content')
    <div class="readings">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-gray-700 uppercase font-bold">Readings</h2>
            </div>
            <div class="flex flex-wrap items-center">
                <a href="{{ route('readings.create') }}?subject_id={{ $subject_id }}" class="bg-green-500 text-white text-sm uppercase py-2 px-4 flex items-center rounded">
                    <svg class="w-3 h-3 fill-current" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus" class="svg-inline--fa fa-plus fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <path fill="currentColor" d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"></path>
                    </svg>
                    <span class="ml-2 text-xs font-semibold">Add New Reading</span>
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($readings->isEmpty())
            <p>No readings found for this subject.</p>
        @else
        <div class="w-full px-0 md:px-6 py-4">
            

            <div class="flex items-center bg-gray-600">
                    <div class="w-1/4 text-left text-white py-2 px-4 font-semibold">Name</div>
                    <div class="w-1/4 text-left text-white py-2 px-4 font-semibold">Description</div>
                    <div class="w-1/3 text-right text-white py-2 px-4 font-semibold">Action</div>
                    
                </div>

                @foreach($readings as $reading)
                <div class="flex items-center justify-between border border-gray-200 -mb-px">
                        <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium">{{ $reading->name }}</div>
                        <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium">{{ $reading->description }}</div>
                        <div class="w-1/4 text-left text-gray-600 py-2 px-4 font-medium">
                            <a href="{{ route('readings.download', $reading->id) }}" >
                              Download
                            </a>
                       
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection