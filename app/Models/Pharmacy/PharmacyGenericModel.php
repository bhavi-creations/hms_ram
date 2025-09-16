<?php

namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyGenericModel extends Model
{
    protected $table      = 'pharmacy_generics';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'generic_name',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'generic_name' => 'required|trim|regex_match[/^[a-zA-Z0-9 .,&-]+$/]|max_length[255]|is_unique[pharmacy_generics.generic_name,id,{id}]'
    ];

    // Custom validation error messages
    protected $validationMessages = [
        'generic_name' => [
            'required'     => 'Generic name is required.',
            'regex_match'  => 'Generic name can only contain letters, numbers, spaces, dots, commas, ampersands, and hyphens.',
            'max_length'   => 'Generic name should not exceed 255 characters.',
            'is_unique'    => 'This generic name already exists.'
        ]
    ];

    protected $skipValidation = false;
}
