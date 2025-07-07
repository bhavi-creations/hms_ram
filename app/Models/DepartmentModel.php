<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table            = 'hospital_departments'; // Updated to the new table name
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true; // Set to true if you want to soft delete departments

    protected $allowedFields    = ['name', 'description'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'name' => 'required|is_unique[hospital_departments.name,id,{id}]|min_length[3]|max_length[100]', // Updated table name in unique rule
        'description' => 'permit_empty|max_length[500]',
    ];
    protected $validationMessages   = [
        'name' => [
            'required' => 'Department name is required.',
            'is_unique' => 'This department name already exists.',
            'min_length' => 'Department name must be at least 3 characters long.',
            'max_length' => 'Department name cannot exceed 100 characters.',
        ],
        'description' => [
            'max_length' => 'Description cannot exceed 500 characters.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}