<?php namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacySalesModel extends Model
{
    protected $table        = 'pharmacy_sales';
    protected $primaryKey   = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'invoice_number',
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
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateInvoiceNumber'];

    /**
     * Callback to generate a unique invoice number before inserting a new sale.
     */
    protected function generateInvoiceNumber(array $data)
    {
        if (!isset($data['data']['invoice_number']) || empty($data['data']['invoice_number'])) {
            $year = date('Y');
            $count = $this->like('invoice_number', "PHM-{$year}-", 'after')->countAllResults() + 1;
            $data['data']['invoice_number'] = "PHM-{$year}-" . str_pad($count, 6, '0', STR_PAD_LEFT);
        }
        return $data;
    }

    /**
     * Fetches outside sales bills with sales person name within a date range.
     */
    public function getOutsideSalesByDateRange(string $startDate, string $endDate)
    {
        return $this->select('pharmacy_sales.*, users.first_name as sales_person_first_name, users.last_name as sales_person_last_name')
                    ->join('users', 'users.id = pharmacy_sales.sales_person_id')
                    ->where('prescription_type', 'outside_sale')
                    ->where('sale_date >=', $startDate . ' 00:00:00')
                    ->where('sale_date <=', $endDate . ' 23:59:59')
                    ->orderBy('sale_date', 'DESC')
                    ->findAll();
    }

    /**
     * Fetches a single invoice details for an outside patient.
     */
    public function getInvoiceDetails(string $invoiceNumber)
    {
        return $this->select('pharmacy_sales.*, users.first_name as sales_person_first_name, users.last_name as sales_person_last_name')
                    ->join('users', 'users.id = pharmacy_sales.sales_person_id')
                    ->where('invoice_number', $invoiceNumber)
                    ->first();
    }
}