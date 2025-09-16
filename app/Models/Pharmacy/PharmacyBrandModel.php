<?php

namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyBrandModel extends Model
{
    protected $table      = 'pharmacy_brands';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'brand_name',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'brand_name' => 'required|trim|regex_match[/^[a-zA-Z0-9 .,&-]+$/]|max_length[255]|is_unique[pharmacy_brands.brand_name,id,{id}]'
    ];

    // Custom validation error messages
    protected $validationMessages = [
        'brand_name' => [
            'required'     => 'Brand name is required.',
            'regex_match'  => 'Brand name can only contain letters, numbers, spaces, dots, commas, ampersands, and hyphens.',
            'max_length'   => 'Brand name should not exceed 255 characters.',
            'is_unique'    => 'This brand name already exists.'
        ]
    ];

    protected $skipValidation = false;
}
