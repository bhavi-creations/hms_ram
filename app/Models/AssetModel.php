<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetModel extends Model
{
    protected $table            = 'assets_equipment';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'name',
        'asset_tag',
        'category',
        'description',
        'purchase_date',
        'warranty_expiry_date',
        'location',
        'status',
        'notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'name'                 => 'required|min_length[3]|max_length[255]',
        'asset_tag'            => 'permit_empty|max_length[100]|is_unique[assets_equipment.asset_tag,id,{id}]',
        'category'             => 'required|min_length[3]|max_length[100]',
        'description'          => 'permit_empty|max_length[500]',
        'purchase_date'        => 'permit_empty|valid_date',
        'warranty_expiry_date' => 'permit_empty|valid_date|after_or_equal_to[purchase_date]',
        'location'             => 'permit_empty|max_length[255]',
        'status'               => 'required|in_list[Operational,Under Maintenance,Out of Service,Disposed]',
        'notes'                => 'permit_empty|max_length[500]',
    ];
    protected $validationMessages = [
        'name' => [
            'required'   => 'Asset name is required.',
            'min_length' => 'Asset name must be at least 3 characters long.',
            'max_length' => 'Asset name cannot exceed 255 characters.',
        ],
        'asset_tag' => [
            'max_length' => 'Asset tag cannot exceed 100 characters.',
            'is_unique'  => 'This asset tag already exists. Please use a unique tag.',
        ],
        'category' => [
            'required'   => 'Category is required.',
            'min_length' => 'Category must be at least 3 characters long.',
            'max_length' => 'Category cannot exceed 100 characters.',
        ],
        'purchase_date' => [
            'valid_date' => 'Invalid purchase date format.',
        ],
        'warranty_expiry_date' => [
            'valid_date'        => 'Invalid warranty expiry date format.',
            'after_or_equal_to' => 'Warranty expiry date must be on or after the purchase date.',
        ],
        'status' => [
            'required' => 'Status is required.',
            'in_list'  => 'Invalid status selected.',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}
