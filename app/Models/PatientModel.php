<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time; // Make sure this is included for date formatting

class PatientModel extends Model
{
    protected $table = 'patients';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'patient_id_code',
        'patient_type',
        'previous_patient_type', // <--- ADDED: To store the patient's type before IPD
        'opd_id_code',
        'ipd_id_code',
        'gen_id_code',
        'cus_id_code',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'marital_status',
        'occupation',
        'address',
        'phone_number',
        'email',
        'emergency_contact_name',
        'emergency_contact_phone',
        'known_allergies',
        'pre_existing_conditions',
        'referred_to_doctor_id',
        'referred_by_id',
        'remarks',
        'reports',
        'fee',
        'discount_percentage',
        'final_amount'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $dateFormat    = 'datetime'; // Your database uses datetime for timestamps

    protected $validationRules = [
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name' => 'required|min_length[2]|max_length[100]',
        'date_of_birth' => 'required|valid_date',
        'gender' => 'required|in_list[Male,Female,Other]',
        // UPDATED: Added 'Discharged' to patient_type enum
        'patient_type' => 'required|in_list[General,OPD,IPD,Casualty,Discharged]',
        'phone_number' => 'permit_empty|max_length[20]',
        'email'        => 'permit_empty|valid_email|max_length[255]',
        'emergency_contact_name' => 'permit_empty|max_length[255]',
        'emergency_contact_phone' => 'permit_empty|max_length[20]',
        'referred_to_doctor_id' => 'permit_empty|integer',
        'referred_by_id' => 'permit_empty|integer',
        'remarks' => 'permit_empty|max_length[500]',
        'fee' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'discount_percentage' => 'permit_empty|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
        'final_amount' => 'permit_empty|numeric|greater_than_equal_to[0]',
        // ADDED: Validation for the new previous_patient_type field
        'previous_patient_type' => 'permit_empty|in_list[General,OPD,Casualty]',
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generatePatientIDs'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['generateTypeSpecificIDsOnUpdate'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Generates all patient-related IDs (primary and type-specific) before inserting the record.
     * This method is called automatically by CodeIgniter's Model before an insert operation.
     *
     * @param array $data The data array being inserted (e.g., from controller's $this->request->getPost())
     * @return array The modified data array with generated IDs
     */
    protected function generatePatientIDs(array $data)
    {
        // Extract patient_type from the input data
        $patientType = $data['data']['patient_type'] ?? null;

        // Get the current date in YYMMDD format, which will be part of the ID
        $currentDate = Time::now(TIMEZONE)->format('ymd');

        // Initialize all ID fields to null.
        $data['data']['patient_id_code'] = null; // This will always be overwritten by PAT ID
        $data['data']['opd_id_code'] = null;
        $data['data']['ipd_id_code'] = null;
        $data['data']['gen_id_code'] = null;
        $data['data']['cus_id_code'] = null;
        $data['data']['previous_patient_type'] = null; // <--- ADDED: Initialize new field

        // Load the Patient ID Sequences Model to get the next available sequence number
        $sequenceModel = new \App\Models\PatientIdSequenceModel();

        // --- Generate Primary Patient ID (PAT-YYMMDD-XXXXX) ---
        // Every patient gets a primary patient_id_code regardless of their type.
        $primaryPrefix = 'PAT';
        $primarySequence = $sequenceModel->getNextSequence($primaryPrefix);
        $data['data']['patient_id_code'] = sprintf('%s-%s-%05d', $primaryPrefix, $currentDate, $primarySequence);

        // --- Generate Type-Specific IDs based on patient_type ---
        // Only generate a type-specific ID if patient_type is provided
        if ($patientType) {
            list($typePrefix, $idFieldName) = $this->getPrefixAndFieldName($patientType);

            if ($typePrefix && $idFieldName) {
                $typeSequence = $sequenceModel->getNextSequence($typePrefix);
                $data['data'][$idFieldName] = sprintf('%s-%s-%05d', $typePrefix, $currentDate, $typeSequence);
            }
        }

        return $data; // Return the modified data array
    }

    /**
     * Handles ID generation for type-specific IDs when a patient is updated.
     * This method is called automatically by CodeIgniter's Model before an update operation.
     *
     * @param array $data The data array being updated.
     * @return array The modified data array with generated IDs.
     */
    protected function generateTypeSpecificIDsOnUpdate(array $data)
    {
        if (isset($data['id']) && is_array($data['id']) && !empty($data['id']) &&
            isset($data['data']) && array_key_exists('patient_type', $data['data'])) {

            $patientId = $data['id'][0];
            $currentPatient = $this->find($patientId);

            $oldPatientType = $currentPatient['patient_type'] ?? null;
            $newPatientType = $data['data']['patient_type'];

            // Proceed only if patient exists AND patient_type is changing
            if ($currentPatient && $oldPatientType !== $newPatientType) {
                list($typePrefix, $idFieldName) = $this->getPrefixAndFieldName($newPatientType);

                // Only generate a new ID if:
                // 1. A valid prefix and field name are found for the new type.
                // 2. The specific ID field (e.g., ipd_id_code) is currently empty in the database.
                // 3. The specific ID field is NOT ALREADY SET in the $data['data'] array that's being updated.
                if ($typePrefix && $idFieldName && empty($currentPatient[$idFieldName]) && !isset($data['data'][$idFieldName])) {
                    $currentDate = Time::now(TIMEZONE)->format('ymd');
                    $sequenceModel = new \App\Models\PatientIdSequenceModel();
                    $typeSequence = $sequenceModel->getNextSequence($typePrefix);
                    $data['data'][$idFieldName] = sprintf('%s-%s-%05d', $typePrefix, $currentDate, $typeSequence);
                    log_message('info', 'Generated new ID (via beforeUpdate) ' . $data['data'][$idFieldName] . ' for patient ' . $patientId . ' for type ' . $newPatientType);
                } elseif ($typePrefix && $idFieldName && !empty($currentPatient[$idFieldName]) && !isset($data['data'][$idFieldName])) {
                    // If the patient type is changed and the ID already exists in DB, preserve it,
                    // unless it's explicitly being set in $data['data'] already.
                    $data['data'][$idFieldName] = $currentPatient[$idFieldName];
                    log_message('info', 'Preserving existing ID (via beforeUpdate) ' . $currentPatient[$idFieldName] . ' for patient ' . $patientId . ' for type ' . $newPatientType);
                }
            }
        }
        return $data;
    }

    /**
     * Helper method to get the ID prefix and field name based on patient type.
     *
     * @param string $patientType
     * @return array [string $typePrefix, string $idFieldName]
     */
    private function getPrefixAndFieldName(string $patientType): array
    {
        $typePrefix = '';
        $idFieldName = '';
        switch (strtoupper($patientType)) {
            case 'OPD':
                $typePrefix = 'OPD';
                $idFieldName = 'opd_id_code';
                break;
            case 'IPD':
                $typePrefix = 'IPD';
                $idFieldName = 'ipd_id_code';
                break;
            case 'GENERAL':
                $typePrefix = 'GEN';
                $idFieldName = 'gen_id_code';
                break;
            case 'CASUALTY':
                $typePrefix = 'CUS';
                $idFieldName = 'cus_id_code';
                break;
            // No prefix/ID field for 'Discharged' as it's a status, not a primary type for new ID generation
        }
        return [$typePrefix, $idFieldName];
    }

    /**
     * Admits a patient to IPD. This updates the patient_type to 'IPD'
     * and ensures an 'ipd_id_code' is generated/assigned.
     *
     * @param int $patientId The ID of the patient to admit.
     * @param array $admissionData Additional data relevant to IPD admission (e.g., ward, bed, etc.).
     * @return bool True on successful admission, false otherwise.
     */
    public function admitPatientToIPD(int $patientId, array $admissionData): bool
    {
        $this->db->transBegin();

        try {
            $patient = $this->find($patientId);

            if (!$patient) {
                log_message('error', 'Patient not found for admission to IPD. ID: ' . $patientId);
                return false;
            }

            $updateData = [
                'patient_type' => 'IPD',
            ];

            // ADDED LOGIC: Store the patient's current type as previous_patient_type
            // Do this BEFORE changing patient_type to 'IPD'
            // Ensure previous_patient_type is only set if it's not already IPD or Discharged
            // This prevents overwriting a meaningful previous type if admitting from an already IPD/Discharged state
            if (!in_array($patient['patient_type'], ['IPD', 'Discharged'])) {
                 $updateData['previous_patient_type'] = $patient['patient_type'];
                 log_message('info', 'Storing previous patient type ' . $patient['patient_type'] . ' for patient ' . $patientId . ' before IPD admission.');
            } else {
                 // If patient was already IPD/Discharged, we might want to clear previous_patient_type
                 // or handle it differently based on your specific re-admission logic.
                 // For now, setting to null to ensure clarity.
                 $updateData['previous_patient_type'] = null;
            }

            if (empty($patient['ipd_id_code'])) {
                $sequenceModel = new \App\Models\PatientIdSequenceModel();
                $currentDate = Time::now(TIMEZONE)->format('ymd');
                $ipdSequence = $sequenceModel->getNextSequence('IPD');
                $updateData['ipd_id_code'] = sprintf('IPD-%s-%05d', $currentDate, $ipdSequence);
                log_message('info', 'Generated new IPD ID ' . $updateData['ipd_id_code'] . ' for patient ' . $patientId . ' during admission.');
            } else {
                $updateData['ipd_id_code'] = $patient['ipd_id_code'];
                log_message('info', 'Preserving existing IPD ID ' . $patient['ipd_id_code'] . ' for patient ' . $patientId . ' during admission.');
            }

            $this->update($patientId, $updateData);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_message('error', 'IPD Admission transaction failed for patient ID: ' . $patientId . '. Query: ' . $this->db->getLastQuery());
                return false;
            } else {
                $this->db->transCommit();
                log_message('info', 'Patient ' . $patientId . ' admitted to IPD. IPD Code: ' . $updateData['ipd_id_code']);
                return true;
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Exception during IPD admission for patient ID ' . $patientId . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Reverts an IPD patient to their previous patient type (OPD, General, Casualty).
     * Clears IPD-specific details.
     *
     * @param int $patientId The ID of the patient to revert.
     * @return bool True on success, false otherwise.
     */
    public function revertFromIPD(int $patientId): bool
    {
        $this->db->transBegin();
        try {
            $patient = $this->find($patientId);

            if (!$patient) {
                log_message('error', 'Patient not found for IPD reversion. ID: ' . $patientId);
                return false;
            }

            // Determine the type to revert to
            // Default to 'General' if previous_patient_type is null or invalid
            $revertType = 'General'; // Default if previous type is not recorded or invalid
            if (!empty($patient['previous_patient_type']) && in_array($patient['previous_patient_type'], ['General', 'OPD', 'Casualty'])) {
                $revertType = $patient['previous_patient_type'];
            }

            $updateData = [
                'patient_type' => $revertType,
                'ipd_id_code' => null, // Clear IPD ID
                'previous_patient_type' => null, // Clear previous type after reverting for cleanup
            ];

            $this->update($patientId, $updateData);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_message('error', 'IPD Reversion transaction failed for patient ID: ' . $patientId . '. Query: ' . $this->db->getLastQuery());
                return false;
            } else {
                $this->db->transCommit();
                log_message('info', 'Patient ' . $patientId . ' reverted from IPD to ' . $revertType . '.');
                return true;
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Exception during IPD reversion for patient ID ' . $patientId . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Marks an IPD patient as 'Discharged'.
     * Clears IPD-specific details.
     *
     * @param int $patientId The ID of the patient to discharge.
     * @return bool True on success, false otherwise.
     */
    public function markAsDischarged(int $patientId): bool
    {
        $this->db->transBegin();
        try {
            $patient = $this->find($patientId);

            if (!$patient) {
                log_message('error', 'Patient not found for discharge. ID: ' . $patientId);
                return false;
            }

            $updateData = [
                'patient_type' => 'Discharged',
                'ipd_id_code' => null, // Clear IPD ID
                'previous_patient_type' => null, // Clear previous type on discharge for cleanup
            ];

            $this->update($patientId, $updateData);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_message('error', 'Patient Discharge transaction failed for patient ID: ' . $patientId . '. Query: ' . $this->db->getLastQuery());
                return false;
            } else {
                $this->db->transCommit();
                log_message('info', 'Patient ' . $patientId . ' discharged.');
                return true;
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Exception during patient discharge for patient ID ' . $patientId . ': ' . $e->getMessage());
            return false;
        }
    }
}