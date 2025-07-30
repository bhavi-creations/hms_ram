<?php namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyPurchaseItemModel extends Model
{
    protected $table      = 'pharmacy_purchase_items';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'purchase_id',
        'medicine_id',
        'batch_id',
        'ordered_quantity',
        'received_quantity',
        'unit_purchase_price',
        'sub_total'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}