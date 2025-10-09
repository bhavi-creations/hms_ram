<?php namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyBrandModel extends Model
{
    // --- CORE CONFIGURATION FOR pharmacy_brands TABLE ---
    protected $table          = 'pharmacy_brands';
    protected $primaryKey     = 'id';
    protected $returnType     = 'array';
    
    // IMPORTANT FIX: Only allow fields present in the pharmacy_brands table
    protected $allowedFields = ['brand_name']; 

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    // The pharmacy_brands table schema provided does NOT have a 'deleted_at' column.
    protected $useSoftDeletes = false; 

    // Validation (Recommended for Brand Name)
    protected $validationRules    = [
        'brand_name' => 'required|is_unique[pharmacy_brands.brand_name,id,{id}]|min_length[2]|max_length[255]',
    ];
    protected $validationMessages = [
        'brand_name' => [
            'required'  => 'Brand name is required.',
            'is_unique' => 'This brand name already exists.',
            'min_length' => 'Brand name must be at least 2 characters long.',
        ],
    ];

    // --- REMOVED: All Bill-related methods (getInHospitalSalesByDateRange, getInHospitalInvoiceDetails) ---
    // These methods belong in a separate PharmacyBillingModel.
}