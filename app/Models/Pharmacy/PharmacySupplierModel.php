<?php namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacySupplierModel extends Model
{
    protected $table      = 'pharmacy_suppliers';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}