<?php

namespace App\Models\Pharmacy; // Corrected namespace

use CodeIgniter\Model;

class PharmacyBillingModel extends Model
{
    // Make sure to change 'pharmacy_billings' to your actual table name if it's different.
    protected $table = 'pharmacy_billings'; 
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'patient_id',
        'bill_id', // Adjust these to match your actual column names
        'bill_date',
        'total_amount',
        'paid_amount',
        'due_amount'
        // Add any other relevant fields from your billing table
    ];

    protected $useTimestamps = true; // Use timestamps if your table has created_at/updated_at columns
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
