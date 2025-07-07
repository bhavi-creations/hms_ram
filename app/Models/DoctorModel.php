<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorModel extends Model
{
    protected $table = 'doctors';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true; // Handles 'created_at' and 'updated_at' automatically

    // Define which fields are allowed to be mass-assigned
    protected $allowedFields = [
        'doctor_id_code', // <-- Add this new field
        'first_name',
        'last_name',
        'user_id', // Assuming this links to a users table for login
        'specialization',
        'qualification',
        'medical_license_no',
        'experience_years',
        'bio',
        'department_id',

        // New fields added to the doctors table
        'gender',
        'date_of_birth',
        'phone_number',
        'email',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'registration_number',
        'medical_council',
        'joining_date',
        'employment_status',
        'contract_type',
        'designation',
        'opd_fee',
        'ipd_charge_percentage',
        'bank_account_number',
        'bank_name',
        'ifsc_code',
        'pan_number',
        'profile_picture',
        'signature_image',
        'resume_path',
        'degree_certificate_path',
        'license_certificate_path',
        'other_certificates_path',
        'is_available',
        'status',
        'last_login_at',
    ];

    // Use a beforeInsert event to generate the custom doctor_id_code
    protected $beforeInsert = ['generateDoctorIdCode'];

    /**
     * Generates a unique doctor ID code (DOC-YYMMDD-XXXX).
     * @param array $data The data to be inserted.
     * @return array Modified data with doctor_id_code.
     */
    protected function generateDoctorIdCode(array $data)
    {
        // Get current date in YYMMDD format
        $datePart = date('ymd');

        // Get and increment the sequence number safely
        // Use a transaction or lock to prevent race conditions in highly concurrent environments
        // For most basic apps, this atomic update should be sufficient.
        $this->db->transStart(); // Start a transaction

        $this->db->table('doctor_id_sequences') // <--- Updated table name
                 ->where('name', 'doctor_sequence')
                 ->set('current_value', 'current_value + 1', FALSE) // Increment atomically
                 ->update();

        $sequence = $this->db->table('doctor_id_sequences') // <--- Updated table name
                             ->select('current_value')
                             ->where('name', 'doctor_sequence')
                             ->get()
                             ->getRow()
                             ->current_value;

        $this->db->transComplete(); // Complete the transaction

        if ($this->db->transStatus() === FALSE) {
            // Handle error, e.g., log it or throw an exception
            log_message('error', 'Failed to generate doctor sequence ID.');
            // Fallback or prevent insert if sequence generation fails critically
            throw new \RuntimeException('Failed to generate unique doctor ID. Please try again.');
        }

        // Format the sequence number to be 4 digits (e.g., 0001, 0010, 0100, 1000)
        $sequencePart = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        // Assemble the final doctor ID code
        $data['data']['doctor_id_code'] = 'DOC-' . $datePart . '-' . $sequencePart;

        return $data;
    }

    // --- Existing Methods ---

    public function findAllDoctors()
    {
        return $this->select('id, doctor_id_code, first_name, last_name, specialization, email, phone_number, designation')
                    ->findAll();
    }

    public function getDoctorDetails($id)
    {
        return $this->find($id);
    }
}