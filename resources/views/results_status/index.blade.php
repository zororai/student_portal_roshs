@extends('layouts.app')

@section('content')
    <div class="results-status">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-gray-700 uppercase font-bold">Results Status Records</h2>
            </div>
            <div class="flex flex-wrap items-center">
                <a href="{{ route('results_status.create') }}" class="bg-green-500 text-white text-sm uppercase py-2 px-4 mr-2 flex items-center rounded">
                    <svg class="w-3 h-3 fill-current" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <path fill="currentColor" d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"></path>
                    </svg>
                    <span class="ml-2 text-xs font-semibold">Create New Record</span>
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="mt-8 bg-white rounded border-b-4 border-gray-300">
            <div class="flex flex-wrap items-center uppercase text-sm font-semibold bg-gray-600 text-white rounded-tl rounded-tr">
                <div class="w-4/12 px-4 py-3">Year</div>
                <div class="w-4/12 px-4 py-3">Result Period</div>
                <div class="w-1/12 px-4 py-3 text-right">Actions</div>
            </div>
            @foreach ($resultsStatuses as $resultStatus)
                <div class="flex flex-wrap items-center text-gray-700 border-t-2 border-l-4 border-r-4 border-gray-300">
                    <div class="w-4/12 px-4 py-3 text-sm">{{ $resultStatus->year }}</div>
                    <div class="w-4/12 px-4 py-3 text-sm">{{ $resultStatus->result_period }}</div>
                    <div class="w-1/12 flex justify-end px-3">
                        <a href="{{ route('results_status.edit', $resultStatus->id) }}">
                            <svg class="h-6 w-6 fill-current text-green-600" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="pen-square" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                <path fill="currentColor" d="M400 480H48c-26.5 0-48-21.5-48-48V80c0-26.5 21.5-48 48-48h352c26.5 0 48 21.5 48 48v352c0 26.5-21.5 48-48 48zM238.1 177.9L102.4 313.6l-6.3 57.1c-.8 7.6 5.6 14.1 13.3 13.3l57.1-6.3L302.2 242c2.3-2.3 2.3-6.1 0-8.5L246.7 178c-2.5-2.4-6.3-2.4-8.6-.1zM345 165.1L314.9 135c-9.4-9.4-24.6-9.4-33.9 0l-23.1 23.1c-2.3 2.3-2.3 6.1 0 8.5l55.5 55.5c2.3 2.3 6.1 2.3 8.5 0L345 199c9.3-9.3 9.3-24.5 0-33.9z"></path>
                            </svg>
                        </a>
                        <form action="{{ route('results_status.destroy', $resultStatus->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this record?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <svg class="h-6 w-6 fill-current" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="trash" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                    <path fill="currentColor" d="M135.3 29.3L95.7 68.9c-6.3 6.3-9.7 14.9-9.7 23.5v8H32c-17.7 0-32 14.3-32 32s14.3 32 32 32h5.3L51.3 432c2.1 23.5 21.6 40 45.7 40h247.7c24.1 0 43.6-16.5 45.7-40L416 129.7h5.3c17.7 0 32-14.3 32-32s-14.3-32-32-32h-53.7v-8c0-8.6-3.4-17.2-9.7-23.5L312.7 29.3c-6.3-6.3-14.9-9.7-23.5-9.7h-128c-8.6 0-17.2 3.4-23.5 9.7zM224 32h32v32h-32V32zm64 0h32v32h-32V32zm-64 80h32v32h-32v-32zm-64 0h32v32h-32v-32zm64 80h32v32h-32v-32zm-64 0h32v32h-32v-32zm64 80h32v32h-32v-32zm-64 0h32v32h-32v-32zm192 0H224v32h160v-32zm-224 0v32h128v-32H192z"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection