@extends('layouts.app')

@section('content')

<h2>Uploaded Files</h2>

<h1 class="text-2xl font-bold mb-4">{{ $reading->name }}</h1>
<p class="mb-4">{{ $reading->description }}</p>

@if(isset($reading))
    <h1 class="text-2xl font-bold mb-4">{{ $reading->name }}</h1>
    <p class="mb-4">{{ $reading->description }}</p>
@else
    <p>No reading found.</p>
@endif

@endsection