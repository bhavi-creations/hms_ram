<?php

namespace App\Models;

use CodeIgniter\Model;

class ReferredPersonModel extends Model
{
    protected $table = 'referred_persons';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'contact_info', 'type'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType    = 'array';

    // Validation rules for referred persons (optional but good practice)
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'type' => 'required|max_length[100]',
    ];
}
