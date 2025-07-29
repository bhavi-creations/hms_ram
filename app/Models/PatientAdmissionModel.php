<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientAdmissionModel extends Model
{
    protected $table            = 'patient_admissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true; // Assuming soft deletes for admissions for historical purposes

    protected $allowedFields = [
        'patient_id',
        'ward_id',
        'bed_id',
        'admission_date',
        'discharge_date',
        'admission_status',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at'; // For soft deletes

    // Validation rules for patient admissions
    protected $validationRules = [
        'patient_id'       => 'required|integer',
        'admission_date'   => 'required|valid_date',
        'admission_status' => 'required|in_list[Admitted,Discharged,Transferred,Waiting Assignment]',
        'ward_id'          => 'permit_empty|integer', // Ward and bed can be empty initially
        'bed_id'           => 'permit_empty|integer',
    ];

    protected $validationMessages = [
        'patient_id' => [
            'required' => 'Patient ID is required.',
            'integer'  => 'Patient ID must be an integer.',
        ],
        'admission_date' => [
            'required'   => 'Admission date is required.',
            'valid_date' => 'Admission date must be a valid date.',
        ],
        'admission_status' => [
            'required' => 'Admission status is required.',
            'in_list'  => 'Invalid admission status.',
        ],
        'ward_id' => [
            'integer' => 'Ward ID must be an integer if provided.',
        ],
        'bed_id' => [
            'integer' => 'Bed ID must be an integer if provided.',
        ],
    ];

    protected $skipValidation = false;

    /**
     * Fetches active IPD admissions with patient, ward, and bed details.
     *
     * @return array
     */
    public function getActiveAdmissionsWithDetails()
    {
        return $this->select('patient_admissions.*, patients.first_name, patients.last_name, patients.gender, patients.phone_number, patients.ipd_id_code, wards.name as ward_name, beds.bed_number')
                    ->join('patients', 'patients.id = patient_admissions.patient_id')
                    ->join('wards', 'wards.id = patient_admissions.ward_id', 'left') // Use left join for ward/bed as they can be null
                    ->join('beds', 'beds.id = patient_admissions.bed_id', 'left')
                    ->where('patient_admissions.admission_status', 'Admitted')
                    ->orWhere('patient_admissions.admission_status', 'Waiting Assignment') // Include patients waiting assignment
                    ->findAll();
    }

    /**
     * Checks if a bed is currently occupied by an active admission.
     *
     * @param int $bedId
     * @param int|null $excludeAdmissionId
     * @return bool
     */
    public function isBedOccupied(int $bedId, ?int $excludeAdmissionId = null): bool
    {
        $query = $this->where('bed_id', $bedId)
                      ->whereIn('admission_status', ['Admitted', 'Transferred']); // Consider 'Transferred' if bed is still technically occupied during transfer

        if ($excludeAdmissionId) {
            $query->where('id !=', $excludeAdmissionId);
        }

        return $query->countAllResults() > 0;
    }

    /**
     * Gets the active admission for a given patient.
     *
     * @param int $patientId
     * @return array|null
     */
    public function getActiveAdmissionForPatient(int $patientId): ?array
    {
        return $this->where('patient_id', $patientId)
                    ->whereIn('admission_status', ['Admitted', 'Waiting Assignment'])
                    ->first();
    }
}
