@extends('layouts.app')

@section('content')
<div class="container newsletters">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-gray-700 uppercase font-bold">Newsletters</h2>
        <a href="{{ route('newsletters.create') }}" class="bg-green-500 text-white text-sm uppercase py-2 px-4 flex items-center rounded">
            <svg class="w-3 h-3 fill-current" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="plus" class="svg-inline--fa fa-plus fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"></path></svg>
            <span class="ml-2 text-xs font-semibold">Create Newsletter</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <div class="mt-8 bg-white rounded border-b-4 border-gray-300">
        <div class="flex flex-wrap items-center uppercase text-sm font-semibold bg-gray-600 text-white rounded-tl rounded-tr">
            <div class="w-3/12 px-4 py-3">Title</div>
            <div class="w-3/12 px-4 py-3">Published</div>
            <div class="w-3/12 px-4 py-3">Image</div>
            <div class="w-3/12 px-4 py-3 text-right">Actions</div>
        </div>
        @foreach($newsletters as $newsletter)
            <div class="flex flex-wrap items-center text-gray-700 border-t-2 border-l-4 border-r-4 border-gray-300">
                <div class="w-3/12 px-4 py-3 text-sm font-semibold text-gray-600 tracking-tight">{{ $newsletter->title }}</div>
                <div class="w-3/12 px-4 py-3 text-sm font-semibold text-gray-600 tracking-tight">{{ $newsletter->is_published ? 'Yes' : 'No' }}</div>
                <div class="w-3/12 px-4 py-3 text-sm font-semibold text-gray-600 tracking-tight">
                    @if($newsletter->image_path)
                        <img src="{{ asset('storage/' . $newsletter->image_path) }}" width="50" alt="Newsletter Image">
                    @else
                        No Image
                    @endif
                </div>
                <div class="w-3/12 flex items-center justify-end px-3">
                    <a href="{{ route('newsletters.edit', $newsletter->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('newsletters.destroy', $newsletter->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger ml-1" onclick="return confirm('Are you sure?');">Delete</button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-8">
        {{ $newsletters->links() }} <!-- Pagination -->
    </div>
</div>
@endsection