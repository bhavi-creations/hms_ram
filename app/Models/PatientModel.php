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
        'patient_type' => 'required|in_list[General,OPD,IPD,Casualty]',
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
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generatePatientIDs']; // This callback generates the IDs
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
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
        // Make sure your app/Config/Constants.php defines a TIMEZONE
        $currentDate = Time::now(TIMEZONE)->format('ymd');
        
        // Initialize all ID fields to null. This ensures that if a specific ID
        // type is not generated (e.g., not an OPD patient), its corresponding
        // column in the database is explicitly set to NULL.
        $data['data']['patient_id_code'] = null; // This will always be overwritten by PAT ID
        $data['data']['opd_id_code'] = null;
        $data['data']['ipd_id_code'] = null;
        $data['data']['gen_id_code'] = null;
        $data['data']['cus_id_code'] = null;

        // Load the Patient ID Sequences Model to get the next available sequence number
        $sequenceModel = new \App\Models\PatientIdSequenceModel();

        // --- Generate Primary Patient ID (PAT-YYMMDD-XXXXX) ---
        // Every patient gets a primary patient_id_code regardless of their type.
        $primaryPrefix = 'PAT';
        $primarySequence = $sequenceModel->getNextSequence($primaryPrefix);
        // Format the primary ID: PAT-YYMMDD-00001 (e.g., PAT-240628-00001)
        $data['data']['patient_id_code'] = sprintf('%s-%s-%05d', $primaryPrefix, $currentDate, $primarySequence);

        // --- Generate Type-Specific IDs based on patient_type ---
        // Only generate a type-specific ID if patient_type is provided
        if ($patientType) {
            $typePrefix = '';     // Stores the prefix (e.g., 'OPD', 'GEN')
            $idFieldName = '';    // Stores the column name in the 'patients' table (e.g., 'opd_id_code')

            // Determine the prefix and field name based on the patient type
            switch (strtoupper($patientType)) {
                case 'OPD':
                    $typePrefix = 'OPD';
                    $idFieldName = 'opd_id_code';
                    break;
                case 'IPD':
                    $typePrefix = 'IPD';
                    $idFieldName = 'ipd_id_code';
                    break;
                case 'GENERAL': // Matches the value from your form
                    $typePrefix = 'GEN';
                    $idFieldName = 'gen_id_code';
                    break;
                case 'CASUALTY': // Matches the value from your form
                    $typePrefix = 'CUS';
                    $idFieldName = 'cus_id_code';
                    break;
                // No 'default' case needed, as other patient types simply won't get a specific ID
            }

            // If a valid type prefix and field name were determined, generate the ID
            if ($typePrefix && $idFieldName) {
                $typeSequence = $sequenceModel->getNextSequence($typePrefix);
                // Format the type-specific ID: OPD-YYMMDD-00001 (e.g., OPD-240628-00001)
                $data['data'][$idFieldName] = sprintf('%s-%s-%05d', $typePrefix, $currentDate, $typeSequence);
            }
        }

        return $data; // Return the modified data array
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
        $this->db->transBegin(); // Start a database transaction

        try {
            // 1. Fetch the current patient record to check existing ID
            $patient = $this->find($patientId);

            if (!$patient) {
                log_message('error', 'Patient not found for admission to IPD. ID: ' . $patientId);
                return false;
            }

            $updateData = [
                'patient_type' => 'IPD', // Set patient type to IPD
            ];

            // 2. Generate or ensure IPD ID Code is set
            // If ipd_id_code is empty, generate a new one using your sequence model
            if (empty($patient['ipd_id_code'])) {
                $sequenceModel = new \App\Models\PatientIdSequenceModel();
                $currentDate = Time::now(TIMEZONE)->format('ymd'); // Ensure TIMEZONE is defined in Constants.php
                $ipdSequence = $sequenceModel->getNextSequence('IPD');
                $updateData['ipd_id_code'] = sprintf('IPD-%s-%05d', $currentDate, $ipdSequence);
            } else {
                // If an ipd_id_code already exists, keep it
                $updateData['ipd_id_code'] = $patient['ipd_id_code'];
            }

            // 3. Update the patient record
            $this->update($patientId, $updateData);

            // OPTIONAL: If you have a separate 'ipd_admissions' table for detailed IPD records
            // Example: Inserting data into an IPD admissions table
            // This would require a separate model for 'ipd_admissions' or direct query builder usage.
            /*
            if (isset($admissionData['ward_id']) && isset($admissionData['bed_id'])) {
                // Load or create an instance of your IpdAdmissionModel here
                // $ipdAdmissionModel = new \App\Models\IpdAdmissionModel();
                // $ipdAdmissionModel->insert([
                //     'patient_id' => $patientId,
                //     'ipd_id_code' => $updateData['ipd_id_code'],
                //     'ward_id' => $admissionData['ward_id'],
                //     'bed_id' => $admissionData['bed_id'],
                //     'admission_date' => date('Y-m-d H:i:s'),
                //     // ... any other IPD specific fields from $admissionData
                // ]);
                // Or directly using the query builder:
                // $this->db->table('ipd_admissions')->insert([
                //     'patient_id' => $patientId,
                //     'ipd_id_code' => $updateData['ipd_id_code'],
                //     'ward_id' => $admissionData['ward_id'],
                //     'bed_id' => $admissionData['bed_id'],
                //     'admission_date' => date('Y-m-d H:i:s'),
                // ]);
            }
            */

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                log_message('error', 'IPD Admission transaction failed for patient ID: ' . $patientId);
                return false; // Admission failed
            } else {
                $this->db->transCommit();
                log_message('info', 'Patient ' . $patientId . ' admitted to IPD. IPD Code: ' . $updateData['ipd_id_code']);
                return true; // Admission successful
            }

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Exception during IPD admission for patient ID ' . $patientId . ': ' . $e->getMessage());
            return false; // Admission failed due to an exception
        }
    }
    // --- END OF UPDATED METHOD ---
}