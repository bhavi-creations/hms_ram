<?php

namespace App\Models\Laboratory;

use CodeIgniter\Model;

class LabOrderModel extends Model
{
    protected $table = 'lab_orders';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'patient_id',
        'ordered_by',
        'order_date',
        'status',
        'remarks',
        'order_id_code', // Add the new field here
        'patient_id_code',
        'doctor_name',
        'patient_name',
        'patient_phone'
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Event hooks to handle custom ID generation
    protected $beforeInsert = ['generateCustomId'];

    /**
     * Generates a custom, non-resettable order ID.
     * Format: LABRPOS-YYYYMMDD-XXXX
     * The sequential number is based on the auto-incremented `id`.
     */
    protected function generateCustomId(array $data)
    {
        // Get the current date in YYYYMMDD format
        $date = date('Ymd');

        // Find the next available auto-increment ID
        $nextId = $this->db->table($this->table)
            ->selectMax('id')
            ->get()
            ->getRowArray();

        $nextNumber = ($nextId['id'] ?? 0) + 1;

        // Format the sequential number with leading zeros (e.g., 0001, 0002)
        $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // Build the full custom ID
        $customId = "LABRPOS-{$date}-{$formattedNumber}";

        // Add the custom ID to the data array
        $data['data']['order_id_code'] = $customId;

        return $data;
    }

 

    /**
     * Get lab orders with patient and user details for the orders page.
     *
     * @return array
     */
    public function getOrdersWithDetails()
    {
        return $this->select('lab_orders.*, patients.id as patient_id, patients.first_name, patients.last_name, patients.phone_number, users.name as doctor_name')
            ->join('patients', 'patients.id = lab_orders.patient_id')
            ->join('users', 'users.id = lab_orders.ordered_by')
            ->orderBy('lab_orders.created_at', 'DESC')
            ->findAll();
    }

        public function getLabOrdersForPatient(int $patientId)
    {
        // Use CONCAT_WS(' ', users.first_name, users.last_name) to combine first and last name
        return $this->select("lab_orders.*, CONCAT_WS(' ', users.first_name, users.last_name) as doctor_name")
            ->join('users', 'users.id = lab_orders.ordered_by')
            ->where('lab_orders.patient_id', $patientId)
            ->orderBy('lab_orders.order_date', 'DESC')
            ->findAll();
        
        // FUTURE ENHANCEMENT: If you have a separate table linking lab orders to test names 
        // (e.g., 'lab_order_details' and 'lab_tests'), you will need to add those joins here.
    }

}
