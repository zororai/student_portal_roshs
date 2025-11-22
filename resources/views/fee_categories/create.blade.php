@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Add Fee Category</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('fee_categories.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Fee Category Name:</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" name="amount" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="period">Time Period:</label>
            <select name="period" class="form-control" required>
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
                <option value="semester">Semester</option>
                <option value="annual">Annual</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Add Fee Category</button>
    </form>
</div>
@endsection