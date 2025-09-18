<?php

namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacySalesPersonModel extends Model
{
    protected $table = 'pharmacy_sales_persons';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['salesperson_id', 'first_name', 'last_name', 'phone', 'address', 'email', 'status']; // ADD 'status' HERE
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateSalesPersonCode()
    {
        $datePart = date('Ymd');

        $lastRecord = $this->orderBy('id', 'DESC')->first();
        $nextNumber = 1;

        if ($lastRecord) {
            $lastId = $lastRecord['salesperson_id'];
            $parts = explode('-', $lastId);
            if (count($parts) === 3) {
                $lastNumber = intval($parts[2]);
                $nextNumber = $lastNumber + 1;
            }
        }

        $numberPart = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return 'PHY-' . $datePart . '-' . $numberPart;
    }


   
    public function getInvoiceDetails(string $invoiceNumber)
    {
        // Check if it's an outside patient or a registered patient
        // Assuming you have a `patients` table for registered patients
        return $this->select('pharmacy_sales.*, patients.name as patient_name')
            ->join('patients', 'patients.patient_id = pharmacy_sales.patient_id', 'left')
            ->where('pharmacy_sales.invoice_number', $invoiceNumber)
            ->first();
    }
}
