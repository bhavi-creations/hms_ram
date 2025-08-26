<?php 

namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyMedicineModel extends Model
{
    protected $table = 'pharmacy_medicines';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array'; // or 'object'
    protected $useSoftDeletes = false;

    // Corrected the allowedFields array to match the controller's data keys and added new fields
    protected $allowedFields = [
        'generic_name',
        'brand_name',
        'dosage_form_id',
        'strength',
        'unit_of_measure_id',
        'manufacturer_id',
        'category_id',
        'reorder_level',
        'is_active',
        'description',
        'created_by_user_id',
        'updated_by_user_id',
        'gst_rate', // New field for GST percentage
        'hsn_code'  // New field for the HSN code
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    // protected $deletedField = 'deleted_at';

    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}

