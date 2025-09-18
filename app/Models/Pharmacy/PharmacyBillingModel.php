<?php namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyBillingModel extends Model
{
    protected $table = 'pharmacy_billings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'patient_id',
        'bill_id',
        'bill_date',
        'total_amount', 
        'paid_amount',
        'due_amount',
        'sales_person_id' // Added the new column here
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Fetches in-hospital sales bills with patient and sales person details within a date range.
     */
    public function getInHospitalSalesByDateRange(string $startDate, string $endDate)
    {
        return $this->select('pharmacy_billings.*, patients.first_name, patients.last_name, patients.phone_number, users.first_name AS sales_person_first_name, users.last_name AS sales_person_last_name')
                    ->join('patients', 'patients.id = pharmacy_billings.patient_id')
                    ->join('users', 'users.id = pharmacy_billings.sales_person_id', 'left') // Corrected to use the standard join() method with 'left'
                    ->where('bill_date >=', $startDate . ' 00:00:00')
                    ->where('bill_date <=', $endDate . ' 23:59:59')
                    ->orderBy('bill_date', 'DESC')
                    ->findAll();
    }

    /**
     * Fetches a single invoice details for an in-hospital patient.
     */
    public function getInHospitalInvoiceDetails(string $invoiceNumber)
    {
        return $this->select('pharmacy_billings.*, patients.first_name, patients.last_name, patients.phone_number, users.first_name AS sales_person_first_name, users.last_name AS sales_person_last_name')
                    ->join('patients', 'patients.id = pharmacy_billings.patient_id')
                    ->join('users', 'users.id = pharmacy_billings.sales_person_id', 'left') // Corrected to use the standard join() method with 'left'
                    ->where('bill_id', $invoiceNumber)
                    ->first();
    }
}