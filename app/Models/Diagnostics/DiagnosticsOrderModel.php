<?php

namespace App\Models\Diagnostics;

use CodeIgniter\Model;

class DiagnosticsOrderModel extends Model
{
    protected $table = 'diagnostics_orders';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'patient_id',
        'procedure_id', // Assuming this field exists to link to the procedures table
        'ordered_by',
        'order_date',
        'status',
        'remarks',
        'order_id_code',
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // The `beforeInsert` property tells CodeIgniter to run the `generateCustomId` method before every insert.
    protected $beforeInsert = ['generateCustomId'];

    /**
     * Generates a custom, non-resettable order ID.
     * Format: DIAG-YYYYMMDD-XXXX
     * The sequential number is based on the auto-incremented `id`.
     */
    protected function generateCustomId(array $data)
    {
        // 1. Get the current date in YYYYMMDD format
        $date = date('Ymd');

        // 2. Find the highest existing sequence number (XXXX)
        // We do this by selecting the max of the 'XXXX' part of the order_id_code
        $lastOrder = $this->db->table($this->table)
            ->select('order_id_code')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()
            ->getRowArray();

        $lastSequence = 0;
        if (!empty($lastOrder) && !empty($lastOrder['order_id_code'])) {
            // Explode the code: DIAG-YYYYMMDD-XXXX
            $parts = explode('-', $lastOrder['order_id_code']);
            // The sequence number is the last part
            if (count($parts) === 3) {
                // Get the 'XXXX' part and convert to integer
                $lastSequence = (int)$parts[2];
            }
        }

        // 3. Increment the sequence number
        $nextNumber = $lastSequence + 1;

        // 4. Format the sequential number with leading zeros (e.g., 0001, 0002)
        $formattedNumber = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // 5. Build the full custom ID
        $customId = "DIAG-{$date}-{$formattedNumber}";

        // Add the custom ID to the data array that will be inserted
        $data['data']['order_id_code'] = $customId;

        return $data;
    }

    /**
     * Get diagnostic orders with patient, doctor, and user details.
     *
     * @param int|null $orderId The ID of a specific order to retrieve.
     * @return array|null The order data, or an array of all orders if no ID is specified.
     */
    public function getOrdersWithDetails($orderId = null)
    {
        $builder = $this->db->table('diagnostics_orders')
            ->select("
                diagnostics_orders.*,
                CONCAT(patients.first_name, ' ', patients.last_name) AS patient_name,
                patients.patient_id_code,
                IFNULL(CONCAT(doctors.first_name, ' ', doctors.last_name), 'N/A') AS doctor_name,
                IFNULL(CONCAT(users.first_name, ' ', users.last_name), 'N/A') AS created_by_name
            ")
            // Join to patients table first
            ->join('patients', 'patients.id = diagnostics_orders.patient_id', 'left')
            // CORRECTED JOIN: Link patients.referred_to_doctor_id to doctors.id 
            ->join('doctors', 'doctors.id = patients.referred_to_doctor_id', 'left')
            // This join is for the 'Ordered By' name from the users table
            ->join('users', 'users.id = diagnostics_orders.ordered_by', 'left')
            ->orderBy('diagnostics_orders.created_at', 'DESC');

        if ($orderId) {
            return $builder->where('diagnostics_orders.id', $orderId)->get()->getRowArray();
        }

        return $builder->get()->getResultArray();
    }
    
    /**
     * Fetches a single diagnostic order along with patient and doctor details for reporting.
     *
     * This method joins the diagnostics_orders table with the patients and doctors tables
     * to collect all necessary display data in one go.
     *
     * @param int $orderId The ID of the diagnostic order.
     * @return array|null The combined order data, or null if not found.
     */
    public function getReportData(int $orderId)
    {
        return $this->select('diagnostics_orders.*,
                            patients.patient_id_code,
                            patients.first_name as patient_first_name,
                            patients.last_name as patient_last_name,
                            patients.phone_number,
                            doctors.first_name as doctor_first_name,
                            doctors.last_name as doctor_last_name')
                    ->join('patients', 'patients.id = diagnostics_orders.patient_id')
                    // FIX: Changed join key from the non-existent diagnostics_orders.doctor_id 
                    // to the correct patients.referred_to_doctor_id, using a LEFT join for safety.
                    ->join('doctors', 'doctors.id = patients.referred_to_doctor_id', 'left') 
                    ->where('diagnostics_orders.id', $orderId)
                    ->first();
    }


    /**
     * Fetches diagnostic orders for a patient, joining related tables
     * to get the procedure name and report file path.
     * * IMPORTANT: This implementation uses GROUP_CONCAT to handle multiple tests or files per order.
     * The procedure_name and report_file_path will be comma-separated lists if multiple items exist.
     * * @param int $patientId The ID of the patient.
     * @return array An array of diagnostic orders with joined data.
     */
    public function getDiagnosticsOrdersForPatient(int $patientId)
    {
        return $this->select("
                diagnostics_orders.*, 
                CONCAT_WS(' ', users.first_name, users.last_name) as doctor_name,
                -- Joins to items and tests to get the service name
                GROUP_CONCAT(DISTINCT T.test_name SEPARATOR ', ') AS procedure_name,
                -- Joins to items and files to get the report path
                GROUP_CONCAT(DISTINCT R.file_path SEPARATOR ', ') AS report_file_path
            ")
            // Join for the doctor's name (Ordered By)
            ->join('users', 'users.id = diagnostics_orders.ordered_by', 'left')
            
            // 1. Join to the intermediate item table to find the specific tests ordered
            // (Assumes: diagnostics_order_items.diagnostics_order_id = diagnostics_orders.id)
            ->join('diagnostics_order_items OI', 'OI.diagnostics_order_id = diagnostics_orders.id', 'left')
            
            // 2. Join to the tests table to get the test name (aliased as procedure_name)
            // (Assumes: diagnostics_tests.id = diagnostics_order_items.diagnostics_test_id)
            ->join('diagnostics_tests T', 'T.id = OI.diagnostics_test_id', 'left')
            
            // 3. Join to the files table to get the report path (aliased as report_file_path)
            // (Assumes: diagnostics_order_files.diagnostics_order_item_id = diagnostics_order_items.id)
            ->join('diagnostics_order_files R', 'R.diagnostics_order_item_id = OI.id', 'left')
            
            ->where('diagnostics_orders.patient_id', $patientId)
            
            // Group by the order ID to return one row per diagnostic order, even if it has multiple items/files
            ->groupBy('diagnostics_orders.id') 
            
            ->orderBy('diagnostics_orders.order_date', 'DESC')
            ->findAll();
    }

}
