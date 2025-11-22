@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Fee Categories</h2>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('fee_categories.create') }}" class="btn btn-primary">Add New Fee Category</a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Name</th>
                <th>Amount</th>
                <th>Period</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($feeCategories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->amount }}</td>
                    <td>{{ $category->period }}</td>
                    <td>
                        <a href="{{ route('fee_categories.edit', $category->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('fee_categories.destroy', $category->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection