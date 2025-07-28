<?php

namespace App\Models;

use CodeIgniter\Model;

class WardModel extends Model
{
    protected $table            = 'wards'; // Table name for wards
    protected $primaryKey       = 'id'; // Primary key of the table
    protected $useAutoIncrement = true; // Auto-incrementing primary key
    protected $returnType       = 'array'; // Return type for query results (array or object)
    protected $useSoftDeletes   = true; // Enable soft deletes (uses 'deleted_at' column)

    // Fields that can be mass-assigned
    protected $allowedFields = [
        'name',
        'description',
        'capacity',
        'status',
        'bed_prefix'
    ];

    // Dates that will be automatically managed by CodeIgniter
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime'; // Type of date field (datetime, date, int)
    protected $createdField  = 'created_at'; // Column for creation timestamp
    protected $updatedField  = 'updated_at'; // Column for update timestamp
    protected $deletedField  = 'deleted_at'; // Column for soft delete timestamp

    // Validation rules for ward data
    protected $validationRules = [
        'name'       => 'required|min_length[3]|max_length[100]|is_unique[wards.name,id,{id}]', // Name must be unique
        'capacity'   => 'required|integer|greater_than[0]', // Capacity must be a positive integer
        'bed_prefix' => 'required|alpha_numeric|min_length[1]|max_length[20]', // Bed prefix must be alphanumeric
        'status'     => 'required|in_list[Active,Inactive,Under Maintenance]', // Status must be one of the allowed values
    ];

    protected $validationMessages = [
        'name' => [
            'required'    => 'Ward name is required.',
            'min_length'  => 'Ward name must be at least 3 characters long.',
            'max_length'  => 'Ward name cannot exceed 100 characters.',
            'is_unique'   => 'This ward name already exists. Please choose a different one.',
        ],
        'capacity' => [
            'required'      => 'Bed capacity is required.',
            'integer'       => 'Bed capacity must be an integer.',
            'greater_than'  => 'Bed capacity must be greater than 0.',
        ],
        'bed_prefix' => [
            'required'      => 'Bed prefix is required.',
            'alpha_numeric' => 'Bed prefix must contain only letters and numbers.',
            'min_length'    => 'Bed prefix must be at least 1 character long.',
            'max_length'    => 'Bed prefix cannot exceed 20 characters.',
        ],
        'status' => [
            'required'  => 'Status is required.',
            'in_list'   => 'Invalid status selected.',
        ],
    ];

    protected $skipValidation       = false; // Do not skip validation by default
    protected $cleanValidationRules = true; // Clean validation rules before each validation run

    // Callbacks for before and after insert/update operations
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
