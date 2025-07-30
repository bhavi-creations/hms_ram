<?php namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyMedicineModel extends Model
{
    protected $table      = 'pharmacy_medicines';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array'; // or 'object'
    protected $useSoftDeletes = false; // Set to true if you implement soft deletes

    protected $allowedFields = [
        'generic_name',
        'brand_name',
        'dosage_form',
        'strength',
        'unit_of_measure',
        'manufacturer_id',
        'category_id',
        'reorder_level',
        'is_active',
        'description',
        'created_by_user_id'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at'; // For soft deletes

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}