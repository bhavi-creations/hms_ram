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



    public function getPatientAppointmentsWithDoctorName($patientId)
    {
        // Concatenate first_name and last_name from the doctors table (aliased as d)
        // and name the resulting column 'doctor_name'. CONCAT_WS is used for safety.
        $doctorNameSelect = "CONCAT_WS(' ', d.first_name, d.last_name)";

        return $this->select('appointments.*, ' . $doctorNameSelect . ' AS doctor_name')
            ->join('doctors d', 'd.id = appointments.doctor_id', 'left')
            ->where('appointments.patient_id', $patientId)
            // Filter to show only appointments that are upcoming (Scheduled or Confirmed)
            ->whereIn('appointments.status', ['Scheduled', 'Confirmed'])
            ->orderBy('appointment_date', 'ASC')
            ->orderBy('appointment_time', 'ASC')
            ->findAll();
    }
}
