<?php namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacySalesModel extends Model
{
    protected $table      = 'pharmacy_sales';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'invoice_number', // Renamed from sale_code, but same purpose
        'sale_date',
        'sales_person_id',
        'prescription_type',
        'patient_id',
        'doctor_id',
        'outside_patient_name',
        'outside_patient_phone',
        'outside_patient_address',
        'total_amount',
        'discount_amount',
        'net_amount',
        'payment_method',
        'status',
        'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    // --- Add these lines for the callback ---
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateInvoiceNumber']; // Changed to match your column name
    // ----------------------------------------

    /**
     * Callback to generate a unique invoice number before inserting a new sale.
     */
    protected function generateInvoiceNumber(array $data)
    {
        if (!isset($data['data']['invoice_number']) || empty($data['data']['invoice_number'])) {
            $year = date('Y');
            // Get the count of sales for the current year, based on invoice_number prefix
            $count = $this->like('invoice_number', "PHM-{$year}-", 'after')->countAllResults() + 1;
            $data['data']['invoice_number'] = "PHM-{$year}-" . str_pad($count, 6, '0', STR_PAD_LEFT);
        }
        return $data;
    }
}