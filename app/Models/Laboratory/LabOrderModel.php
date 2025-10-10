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
        'order_id_code',
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
     * Get lab orders with patient and user details for the orders page (Admin/Staff view).
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

    /**
     * Fetches lab orders for a specific patient, aggregating test names and report file paths.
     * This method is specifically for the Patient Portal view.
     * * @param int $patientId
     * @return array
     */
    public function getLabOrdersForPatient(int $patientId)
    {
        return $this->select(
            'lab_orders.*, ' . 
            'CONCAT_WS(" ", users.first_name, users.last_name) as doctor_name, ' .
            // Aggregates all test names associated with the order
            'GROUP_CONCAT(LT.name SEPARATOR ", ") AS test_names, ' . 
            // Aggregates all attached file paths for reports
            'GROUP_CONCAT(LF.file_path SEPARATOR ",") AS report_file_paths'
        )
        ->where('lab_orders.patient_id', $patientId)
        // Join to get ordering doctor's name
        ->join('users', 'users.id = lab_orders.ordered_by', 'left') 
        // Join to get test names
        ->join('lab_order_items LOI', 'LOI.lab_order_id = lab_orders.id', 'left')
        ->join('lab_tests LT', 'LT.id = LOI.lab_test_id', 'left')
        // Join to get report file paths (reports are linked to the order item)
        ->join('lab_order_files LF', 'LF.lab_order_item_id = LOI.id', 'left')
        ->groupBy('lab_orders.id')
        ->orderBy('lab_orders.order_date', 'DESC')
        ->findAll();
    }

}
