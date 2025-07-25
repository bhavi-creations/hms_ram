<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table            = 'appointments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // Added: Specify return type, 'array' is common
    protected $useSoftDeletes   = true;    // Added: Enable soft deletes if you want them

    // The fields that are allowed to be saved to the database
    // IMPORTANT: Make sure 'reason_for_visit' matches your database column name
    protected $allowedFields = [
        'patient_id',
        'doctor_id',
        'appointment_date',
        'appointment_time',
        'reason_for_visit',
        'status',
    ];


    // Enable CodeIgniter's built-in timestamp handling
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime'; // Ensure this matches your DB column type (DATETIME)
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at'; // Added: Required if useSoftDeletes is true

    // Validation Rules (Added for data integrity)
    protected $validationRules = [
        'patient_id'        => 'required|integer',
        'doctor_id'         => 'required|integer',
        'appointment_date'  => 'required|valid_date',
        'appointment_time'  => 'required', // More specific validation (e.g., regex) might be needed here
        'reason_for_visit' => 'permit_empty|string|max_length[500]',
        'status'            => 'required|in_list[Pending,Confirmed,Cancelled,Completed]',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
}
