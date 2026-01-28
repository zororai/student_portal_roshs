<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'book_number',
        'condition',
        'condition_notes',
        'image',
        'author',
        'isbn',
        'category',
        'quantity',
        'available_quantity',
        'status',
        'added_by',
    ];

    /**
     * Get the user who added this book
     */
    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Get all library records (borrow history) for this book
     */
    public function libraryRecords()
    {
        return $this->hasMany(LibraryRecord::class);
    }

    /**
     * Get active borrows for this book
     */
    public function activeBorrows()
    {
        return $this->hasMany(LibraryRecord::class)->where('status', 'issued');
    }

    /**
     * Check if book is available for borrowing
     */
    public function isAvailable()
    {
        return $this->available_quantity > 0;
    }

    /**
     * Get condition badge color
     */
    public function getConditionBadgeAttribute()
    {
        $colors = [
            'excellent' => 'bg-green-100 text-green-800',
            'good' => 'bg-blue-100 text-blue-800',
            'fair' => 'bg-yellow-100 text-yellow-800',
            'poor' => 'bg-orange-100 text-orange-800',
            'damaged' => 'bg-red-100 text-red-800',
        ];

        return $colors[$this->condition] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'available' => 'bg-green-100 text-green-800',
            'borrowed' => 'bg-yellow-100 text-yellow-800',
            'reserved' => 'bg-blue-100 text-blue-800',
            'lost' => 'bg-red-100 text-red-800',
        ];

        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }
}
