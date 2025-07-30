<?php namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacySaleItemModel extends Model
{
    protected $table      = 'pharmacy_sale_items';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'sale_id',
        'medicine_id',
        'batch_id',
        'quantity',
        'unit_selling_price',
        'discount_per_item',
        'sub_total'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}