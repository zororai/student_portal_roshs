<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LibraryRecord;
use App\Book;
use App\Student;
use App\User;
use Carbon\Carbon;

class LibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display listing of library records
     */
    public function index(Request $request)
    {
        $query = LibraryRecord::with(['student.user', 'student.class', 'issuedBy']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by search term (student name or book)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('book_title', 'like', "%{$search}%")
                  ->orWhere('book_number', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $records = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('backend.admin.library.index', compact('records'));
    }

    /**
     * Show form to issue a book
     */
    public function create()
    {
        return view('backend.admin.library.create');
    }

    /**
     * Search students for AJAX autocomplete
     */
    public function searchStudents(Request $request)
    {
        $search = $request->get('q', '');
        
        $students = Student::with(['user', 'class'])
            ->where('is_transferred', false)
            ->where(function ($query) use ($search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhere('roll_number', 'like', "%{$search}%");
            })
            ->limit(15)
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->name ?? 'Unknown',
                    'roll_number' => $student->roll_number,
                    'class' => $student->class->class_name ?? 'N/A',
                ];
            });

        return response()->json($students);
    }

    /**
     * Store a new library record (issue book)
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'book_id' => 'nullable|exists:books,id',
            'book_title' => 'required|string|max:255',
            'book_number' => 'required|string|max:100',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'notes' => 'nullable|string|max:500',
        ]);

        // If a book from archive is selected, decrement available quantity
        if ($request->book_id) {
            $book = Book::findOrFail($request->book_id);
            
            if ($book->available_quantity <= 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'This book is not available for borrowing.');
            }

            $book->decrement('available_quantity');
            
            // Update book status if all copies are borrowed
            if ($book->available_quantity <= 0) {
                $book->update(['status' => 'borrowed']);
            }
        }

        LibraryRecord::create([
            'student_id' => $request->student_id,
            'issued_by' => auth()->id(),
            'book_id' => $request->book_id,
            'book_title' => $request->book_title,
            'book_number' => $request->book_number,
            'issue_date' => $request->issue_date,
            'due_date' => $request->due_date,
            'status' => 'issued',
            'notes' => $request->notes,
        ]);

        return redirect()->route('admin.library.index')
            ->with('success', 'Book issued successfully!');
    }

    /**
     * Show a specific library record
     */
    public function show($id)
    {
        $record = LibraryRecord::with(['student.user', 'student.class', 'issuedBy'])->findOrFail($id);
        return view('backend.admin.library.show', compact('record'));
    }

    /**
     * Mark book as returned
     */
    public function returnBook(Request $request, $id)
    {
        $record = LibraryRecord::findOrFail($id);
        
        // If book was from archive, increment available quantity
        if ($record->book_id) {
            $book = Book::find($record->book_id);
            if ($book) {
                $book->increment('available_quantity');
                $book->update(['status' => 'available']);
            }
        }
        
        $record->update([
            'return_date' => Carbon::now(),
            'status' => 'returned',
        ]);

        return redirect()->back()->with('success', 'Book marked as returned!');
    }

    /**
     * Delete a library record
     */
    public function destroy($id)
    {
        $record = LibraryRecord::findOrFail($id);
        $record->delete();

        return redirect()->route('admin.library.index')
            ->with('success', 'Library record deleted successfully!');
    }

    /**
     * View library history for a specific student
     */
    public function studentHistory($studentId)
    {
        $student = Student::with(['user', 'class'])->findOrFail($studentId);
        $records = LibraryRecord::where('student_id', $studentId)
            ->with('issuedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('backend.admin.library.student-history', compact('student', 'records'));
    }

    /**
     * Display listing of all books
     */
    public function books(Request $request)
    {
        $query = Book::with('addedBy');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('book_number', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $books = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('backend.admin.library.books.index', compact('books'));
    }

    /**
     * Show form to add a new book
     */
    public function createBook()
    {
        return view('backend.admin.library.books.create');
    }

    /**
     * Store a new book
     */
    public function storeBook(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'book_number' => 'required|string|max:100|unique:books,book_number',
            'condition' => 'required|in:excellent,good,fair,poor,damaged',
            'condition_notes' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:1',
        ]);

        $data = $request->only([
            'title', 'book_number', 'condition', 'condition_notes',
            'author', 'isbn', 'category', 'quantity'
        ]);

        $data['available_quantity'] = $request->quantity;
        $data['added_by'] = auth()->id();
        $data['status'] = 'available';

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/books'), $imageName);
            $data['image'] = 'uploads/books/' . $imageName;
        }

        Book::create($data);

        return redirect()->route('admin.library.books')
            ->with('success', 'Book added successfully!');
    }

    /**
     * Show form to edit a book
     */
    public function editBook($id)
    {
        $book = Book::findOrFail($id);
        return view('backend.admin.library.books.edit', compact('book'));
    }

    /**
     * Update a book
     */
    public function updateBook(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'book_number' => 'required|string|max:100|unique:books,book_number,' . $id,
            'condition' => 'required|in:excellent,good,fair,poor,damaged',
            'condition_notes' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'author' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:1',
        ]);

        $data = $request->only([
            'title', 'book_number', 'condition', 'condition_notes',
            'author', 'isbn', 'category', 'quantity'
        ]);

        // Adjust available quantity based on quantity change
        $quantityDiff = $request->quantity - $book->quantity;
        $data['available_quantity'] = max(0, $book->available_quantity + $quantityDiff);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($book->image && file_exists(public_path($book->image))) {
                unlink(public_path($book->image));
            }
            
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('uploads/books'), $imageName);
            $data['image'] = 'uploads/books/' . $imageName;
        }

        $book->update($data);

        return redirect()->route('admin.library.books')
            ->with('success', 'Book updated successfully!');
    }

    /**
     * View book history (who borrowed this book)
     */
    public function bookHistory($id)
    {
        $book = Book::with('addedBy')->findOrFail($id);
        $records = LibraryRecord::where('book_id', $id)
            ->orWhere(function ($q) use ($book) {
                $q->where('book_number', $book->book_number);
            })
            ->with(['student.user', 'student.class', 'issuedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('backend.admin.library.books.history', compact('book', 'records'));
    }

    /**
     * Delete a book
     */
    public function destroyBook($id)
    {
        $book = Book::findOrFail($id);

        // Check if book has active borrows
        $activeBorrows = LibraryRecord::where('book_id', $id)
            ->where('status', 'issued')
            ->count();

        if ($activeBorrows > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete book with active borrows. Please return all copies first.');
        }

        // Delete image if exists
        if ($book->image && file_exists(public_path($book->image))) {
            unlink(public_path($book->image));
        }

        $book->delete();

        return redirect()->route('admin.library.books')
            ->with('success', 'Book deleted successfully!');
    }

    /**
     * Search books for AJAX autocomplete
     */
    public function searchBooks(Request $request)
    {
        $search = $request->get('q', '');
        
        $books = Book::where('available_quantity', '>', 0)
            ->where(function ($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('book_number', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
            })
            ->limit(15)
            ->get()
            ->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'book_number' => $book->book_number,
                    'author' => $book->author ?? 'Unknown',
                    'available' => $book->available_quantity,
                    'condition' => ucfirst($book->condition),
                ];
            });

        return response()->json($books);
    }

    /**
     * Student view - My Library (borrowed books and history)
     */
    public function myLibrary()
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('home')->with('error', 'Student record not found.');
        }

        // Get currently borrowed books (status = issued)
        $borrowedBooks = LibraryRecord::where('student_id', $student->id)
            ->where('status', 'issued')
            ->with(['book', 'issuedBy'])
            ->orderBy('issue_date', 'desc')
            ->get();

        // Get borrowing history (all records)
        $borrowingHistory = LibraryRecord::where('student_id', $student->id)
            ->with(['book', 'issuedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Stats
        $totalBorrowed = LibraryRecord::where('student_id', $student->id)->count();
        $currentlyBorrowed = $borrowedBooks->count();
        $returned = LibraryRecord::where('student_id', $student->id)->where('status', 'returned')->count();
        $overdue = $borrowedBooks->filter(function ($record) {
            return $record->due_date && $record->due_date < now();
        })->count();

        return view('backend.student.library', compact(
            'student', 'borrowedBooks', 'borrowingHistory',
            'totalBorrowed', 'currentlyBorrowed', 'returned', 'overdue'
        ));
    }
}
