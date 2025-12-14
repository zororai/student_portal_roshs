<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentApplication extends Model
{
    protected $fillable = [
        'school_applying_for',
        'previous_school',
        'first_name',
        'last_name',
        'gender',
        'date_of_birth',
        'applying_for_form',
        'religion',
        'street_address',
        'residential_area',
        'subjects_of_interest',
        'guardian_full_name',
        'guardian_phone',
        'guardian_email',
        'guardian_relationship',
        'birth_entry_number',
        'dream_job',
        'expected_start_date',
        'documents',
        'status',
        'admin_notes'
    ];

    protected $casts = [
        'subjects_of_interest' => 'array',
        'documents' => 'array',
        'date_of_birth' => 'date',
        'expected_start_date' => 'date'
    ];
}
