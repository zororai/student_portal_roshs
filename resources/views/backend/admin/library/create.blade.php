@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.library.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Issue Book to Student</h1>
                <p class="text-gray-600 mt-1">Search for a student and enter book details</p>
            </div>
        </div>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Book Issue Form
                </h2>
            </div>

            <form action="{{ route('admin.library.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- Student Search -->
                <div x-data="studentSearch()" class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Search Student <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                            x-model="searchQuery"
                            @input.debounce.300ms="searchStudents()"
                            @focus="showDropdown = true"
                            placeholder="Type student name or roll number..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        
                        <input type="hidden" name="student_id" x-model="selectedStudentId" required>

                        <!-- Dropdown Results -->
                        <div x-show="showDropdown && students.length > 0" 
                            @click.away="showDropdown = false"
                            class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <template x-for="student in students" :key="student.id">
                                <div @click="selectStudent(student)" 
                                    class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                    <p class="font-medium text-gray-900" x-text="student.name"></p>
                                    <p class="text-sm text-gray-500">
                                        <span x-text="student.class"></span> • 
                                        <span>Roll: </span><span x-text="student.roll_number"></span>
                                    </p>
                                </div>
                            </template>
                        </div>

                        <!-- No results message -->
                        <div x-show="showDropdown && searchQuery.length > 2 && students.length === 0 && !loading" 
                            class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg p-4 text-center text-gray-500">
                            No students found
                        </div>
                    </div>

                    <!-- Selected Student Display -->
                    <div x-show="selectedStudent" class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium text-sm" x-text="selectedStudent ? selectedStudent.name.substring(0,2).toUpperCase() : ''"></span>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900" x-text="selectedStudent ? selectedStudent.name : ''"></p>
                                    <p class="text-sm text-gray-600">
                                        <span x-text="selectedStudent ? selectedStudent.class : ''"></span> • 
                                        <span>Roll: </span><span x-text="selectedStudent ? selectedStudent.roll_number : ''"></span>
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="clearSelection()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Book Search -->
                <div x-data="bookSearch()" class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">
                        Search Book from Archive <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="text" 
                            x-model="bookSearchQuery"
                            @input.debounce.300ms="searchBooks()"
                            @focus="showBookDropdown = true"
                            placeholder="Type book title or number..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        
                        <input type="hidden" name="book_id" x-model="selectedBookId">
                        <input type="hidden" name="book_title" x-model="selectedBookTitle">
                        <input type="hidden" name="book_number" x-model="selectedBookNumber">

                        <!-- Dropdown Results -->
                        <div x-show="showBookDropdown && books.length > 0" 
                            @click.away="showBookDropdown = false"
                            class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                            <template x-for="book in books" :key="book.id">
                                <div @click="selectBook(book)" 
                                    class="px-4 py-3 hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-b-0">
                                    <p class="font-medium text-gray-900" x-text="book.title"></p>
                                    <p class="text-sm text-gray-500">
                                        <span>Book #: </span><span x-text="book.book_number"></span> • 
                                        <span x-text="book.author"></span> •
                                        <span class="text-green-600" x-text="'Available: ' + book.available"></span>
                                    </p>
                                </div>
                            </template>
                        </div>

                        <!-- No results message -->
                        <div x-show="showBookDropdown && bookSearchQuery.length > 2 && books.length === 0 && !bookLoading" 
                            class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg p-4 text-center text-gray-500">
                            No available books found. <a href="{{ route('admin.library.books.create') }}" class="text-blue-600 hover:underline">Add a new book</a>
                        </div>
                    </div>

                    <!-- Selected Book Display -->
                    <div x-show="selectedBook" class="mt-3 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-gray-900" x-text="selectedBook ? selectedBook.title : ''"></p>
                                    <p class="text-sm text-gray-600">
                                        <span>Book #: </span><span x-text="selectedBook ? selectedBook.book_number : ''"></span> • 
                                        <span x-text="selectedBook ? selectedBook.condition : ''"></span>
                                    </p>
                                </div>
                            </div>
                            <button type="button" @click="clearBookSelection()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Issue Date and Due Date -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="issue_date" class="block text-sm font-medium text-gray-700">
                            Issue Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="issue_date" name="issue_date" 
                            value="{{ old('issue_date', date('Y-m-d')) }}" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="space-y-2">
                        <label for="due_date" class="block text-sm font-medium text-gray-700">
                            Due Date
                        </label>
                        <input type="date" id="due_date" name="due_date" value="{{ old('due_date') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <!-- Notes -->
                <div class="space-y-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">
                        Notes (Optional)
                    </label>
                    <textarea id="notes" name="notes" rows="3" 
                        placeholder="Any additional notes..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                    <a href="{{ route('admin.library.index') }}" 
                        class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                        Issue Book
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function studentSearch() {
    return {
        searchQuery: '',
        students: [],
        selectedStudent: null,
        selectedStudentId: '',
        showDropdown: false,
        loading: false,

        async searchStudents() {
            if (this.searchQuery.length < 2) {
                this.students = [];
                return;
            }

            this.loading = true;
            try {
                const response = await fetch(`{{ route('admin.library.search-students') }}?q=${encodeURIComponent(this.searchQuery)}`);
                this.students = await response.json();
            } catch (error) {
                console.error('Error searching students:', error);
                this.students = [];
            }
            this.loading = false;
        },

        selectStudent(student) {
            this.selectedStudent = student;
            this.selectedStudentId = student.id;
            this.searchQuery = student.name;
            this.showDropdown = false;
        },

        clearSelection() {
            this.selectedStudent = null;
            this.selectedStudentId = '';
            this.searchQuery = '';
        }
    }
}

function bookSearch() {
    return {
        bookSearchQuery: '',
        books: [],
        selectedBook: null,
        selectedBookId: '',
        selectedBookTitle: '',
        selectedBookNumber: '',
        showBookDropdown: false,
        bookLoading: false,

        async searchBooks() {
            if (this.bookSearchQuery.length < 2) {
                this.books = [];
                return;
            }

            this.bookLoading = true;
            try {
                const response = await fetch(`{{ route('admin.library.search-books') }}?q=${encodeURIComponent(this.bookSearchQuery)}`);
                this.books = await response.json();
            } catch (error) {
                console.error('Error searching books:', error);
                this.books = [];
            }
            this.bookLoading = false;
        },

        selectBook(book) {
            this.selectedBook = book;
            this.selectedBookId = book.id;
            this.selectedBookTitle = book.title;
            this.selectedBookNumber = book.book_number;
            this.bookSearchQuery = book.title;
            this.showBookDropdown = false;
        },

        clearBookSelection() {
            this.selectedBook = null;
            this.selectedBookId = '';
            this.selectedBookTitle = '';
            this.selectedBookNumber = '';
            this.bookSearchQuery = '';
        }
    }
}
</script>
@endsection
