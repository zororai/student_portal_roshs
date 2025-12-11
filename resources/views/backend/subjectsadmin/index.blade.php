@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Subjects Management</h1>
                    <p class="mt-2 text-sm text-gray-600">Manage academic subjects and teacher assignments</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.subjects.create') }}" class="inline-flex items-center px-5 py-3 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 448 512">
                            <path d="M416 208H272V64c0-17.67-14.33-32-32-32h-32c-17.67 0-32 14.33-32 32v144H32c-17.67 0-32 14.33-32 32v32c0 17.67 14.33 32 32 32h144v144c0 17.67 14.33 32 32 32h32c17.67 0 32-14.33 32-32V304h144c17.67 0 32-14.33 32-32v-32c0-17.67-14.33-32-32-32z"/>
                        </svg>
                        Add New Subject
                    </a>
                </div>
            </div>
        </div>

        <!-- Subject List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <h3 class="text-gray-700 uppercase font-bold mb-2 px-6 pt-6">Subject List</h3>
            
            <!-- Header Row -->
            <div class="flex items-center bg-gray-600 mx-6 rounded-tl rounded-tr">
                <div class="w-1/5 text-left text-white py-2 px-4 font-semibold">Code</div>
                <div class="w-1/5 text-left text-white py-2 px-4 font-semibold">Subject</div>
                <div class="w-1/5 text-left text-white py-2 px-4 font-semibold">Teacher</div>
                <div class="w-1/5 text-center text-white py-2 px-4 font-semibold">Materials</div>
                <div class="w-1/5 text-center text-white py-2 px-4 font-semibold">Actions</div>
            </div>
            
            <!-- Subject Rows -->
            <div class="px-6 pb-6">
                @forelse ($subjects as $subject)
                    <div class="flex items-center justify-between border border-gray-200 hover:bg-gray-50 transition-colors">
                        <div class="w-1/5 text-left text-gray-600 py-2 px-4 font-medium">{{ $subject->subject_code }}</div>
                        <div class="w-1/5 text-left text-gray-600 py-2 px-4 font-medium">{{ $subject->name }}</div>
                        <div class="w-1/5 text-left text-gray-600 py-2 px-4 font-medium">{{ $subject->teacher->user->name ?? 'Not Assigned' }}</div>
                        <div class="w-1/5 text-center text-gray-600 py-2 px-4 font-medium">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                {{ $subject->readings_count ?? 0 }}
                            </span>
                        </div>
                        <div class="w-1/5 text-center text-gray-600 py-2 px-4 font-medium">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="inline-flex items-center p-2 bg-green-100 hover:bg-green-200 text-green-600 rounded-lg transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 512 512">
                                        <path d="M471.6 21.7c-21.9-21.9-57.3-21.9-79.2 0L362.3 51.7l97.9 97.9 30.1-30.1c21.9-21.9 21.9-57.3 0-79.2L471.6 21.7zm-299.2 220c-6.1 6.1-10.8 13.6-13.5 21.9l-29.6 88.8c-2.9 8.6-.6 18.1 5.8 24.6s15.9 8.7 24.6 5.8l88.8-29.6c8.2-2.7 15.7-7.4 21.9-13.5L437.7 172.3 339.7 74.3 172.4 241.7zM96 64C43 64 0 107 0 160V416c0 53 43 96 96 96H352c53 0 96-43 96-96V320c0-17.7-14.3-32-32-32s-32 14.3-32 32v96c0 17.7-14.3 32-32 32H96c-17.7 0-32-14.3-32-32V160c0-17.7 14.3-32 32-32h96c17.7 0 32-14.3 32-32s-14.3-32-32-32H96z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.subjects.destroy', $subject->id) }}" method="POST" class="inline-flex" onsubmit="return confirm('Are you sure you want to delete this subject?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center p-2 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg transition-colors" title="Delete">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512">
                                            <path d="M432 32H312l-9.4-18.7A24 24 0 0 0 281.1 0H166.8a23.72 23.72 0 0 0-21.4 13.3L136 32H16A16 16 0 0 0 0 48v32a16 16 0 0 0 16 16h416a16 16 0 0 0 16-16V48a16 16 0 0 0-16-16zM53.2 467a48 48 0 0 0 47.9 45h245.8a48 48 0 0 0 47.9-45L416 128H32z"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="border border-gray-200 py-12 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <p class="text-gray-500 text-lg font-medium">No subjects found</p>
                            <p class="text-gray-400 text-sm mt-1">Get started by adding your first subject</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $subjects->links() }}
        </div>
    </div>
@endsection
