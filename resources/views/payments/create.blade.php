@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Pay Fees for {{ $student->name }}</h2>

    <form action="{{ route('payments.store') }}" method="POST">
        @csrf
        <input type="hidden" name="student_id" value="{{ $student->id }}">

        <div class="form-group">
            <label for="fee_category_id">Fee Category:</label>
            <select name="fee_category_id" class="form-control" required>
                @foreach($feeCategories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }} - ${{ $category->amount }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" name="amount" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Make Payment</button>
    </form>
</div>
@endsection