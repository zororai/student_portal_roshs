<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class LibraryRecord extends Model
{
    use Auditable;

    protected $fillable = [
        'student_id',
        'issued_by',
        'book_id',
        'book_title',
        'book_number',
        'issue_date',
        'due_date',
        'return_date',
        'status',
        'notes',
    ];

    protected $dates = [
        'issue_date',
        'due_date',
        'return_date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
