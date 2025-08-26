<?php namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyDosageFormModel extends Model
{
    // Define the table name and primary key
    protected $table          = 'pharmacy_dosage_forms';
    protected $primaryKey     = 'id';

    // Enable auto-incrementing for the primary key
    protected $useAutoIncrement = true;

    // Define the return type for query results
    protected $returnType     = 'array';
    
    // Disable soft deletes
    protected $useSoftDeletes = false;

    // List of fields that are allowed to be mass-assigned
    protected $allowedFields = [
        'name'
    ];

    // Enable timestamps (created_at and updated_at)
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Define validation rules for the name field
    protected $validationRules = [
        'name' => 'required|is_unique[pharmacy_dosage_forms.name]'
    ];
    protected $validationMessages = [
        'name' => [
            'required' => 'The dosage form name is required.',
            'is_unique' => 'The dosage form name must be unique.'
        ]
    ];
    
    protected $skipValidation = false;
}
