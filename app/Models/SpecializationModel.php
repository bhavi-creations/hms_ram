<?php

namespace App\Models;

use CodeIgniter\Model;

class SpecializationModel extends Model
{
    protected $table            = 'specializations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true; // Assuming soft deletes are enabled for consistency

    protected $allowedFields    = ['name', 'description'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|min_length[3]|max_length[100]|is_unique[specializations.name,id,{id}]',
        'description' => 'permit_empty|max_length[255]',
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'The specialization name is required.',
            'min_length' => 'The specialization name must be at least 3 characters long.',
            'is_unique' => 'This specialization name already exists.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
