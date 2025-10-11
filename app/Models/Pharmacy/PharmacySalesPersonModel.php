<?php

namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacySalesPersonModel extends Model
{
    protected $table = 'pharmacy_sales_persons';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array'; // Explicitly defining return type
    
    // IMPORTANT: Added 'user_id' for linking to the login table, 
    // and 'profile_picture' to store the image path.
    protected $allowedFields = [
        'user_id', 
        'salesperson_id', 
        'first_name', 
        'last_name', 
        'phone', 
        'address', 
        'email', 
        'status',
        'profile_picture' // Added the new column for profile image path
    ]; 
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // --- Model Validation Rules ---
    protected $validationRules = [
        'first_name'      => 'required|min_length[2]',
        'last_name'       => 'required|min_length[2]',
        'phone'           => 'required|numeric|min_length[10]|max_length[15]',
        // This rule enforces unique email for inserts, but allows the same email for updates on the existing record.
        'email'           => 'required|valid_email|is_unique[pharmacy_sales_persons.email,id,{id}]', 
        'address'         => 'permit_empty',
        'status'          => 'required|in_list[0,1]',
        'profile_picture' => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    // --- End Model Validation Rules ---
    
    /**
     * Generates a unique Sales Person ID code (PHY-YYYYMMDD-####).
     * The numeric part (####) is now CONTINUOUS, regardless of the date, month, or year.
     *
     * @return string
     */
    public function generateSalesPersonCode()
    {
        $datePart = date('Ymd');

        // Find the last record based on the auto-incrementing primary key 'id' 
        // to ensure we get the latest entry overall.
        $lastRecord = $this->orderBy('id', 'DESC')->first();
        $nextNumber = 1;

        if ($lastRecord) {
            $lastId = $lastRecord['salesperson_id'];
            $parts = explode('-', $lastId);

            if (count($parts) === 3) {
                // The third part is the sequential number (e.g., '0008')
                $lastNumber = intval($parts[2]);
                
                // CRITICAL FIX: Always increment the last number found. 
                // We no longer check if the date matches, ensuring continuity.
                $nextNumber = $lastNumber + 1;
            }
            // If the last ID format is unexpected, nextNumber remains 1 (a safety fallback)
        }

        $numberPart = str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        // The final ID uses the current date but the continuous sequence number
        return 'PHY-' . $datePart . '-' . $numberPart;
    }


    /**
     * Retrieves invoice details, joining with patient name if available.
     *
     * @param string $invoiceNumber
     * @return array|null
     */
    public function getInvoiceDetails(string $invoiceNumber)
    {
        // This method is retained, but typically logic involving other tables (pharmacy_sales, patients)
        // would reside in a dedicated Sales or Billing Model.
        return $this->select('pharmacy_sales.*, patients.name as patient_name')
            ->join('patients', 'patients.patient_id = pharmacy_sales.patient_id', 'left')
            ->where('pharmacy_sales.invoice_number', $invoiceNumber)
            ->first();
    }
}
