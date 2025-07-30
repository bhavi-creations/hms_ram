<?php namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyBatchModel extends Model
{
    protected $table      = 'pharmacy_batches';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'medicine_id',
        'batch_number',
        'manufacturing_date',
        'expiry_date',
        'initial_quantity',
        'current_stock',
        'purchase_price',
        'selling_price',
        'supplier_id',
        'status'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}