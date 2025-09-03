<?php

namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacySaleItemModel extends Model
{
    protected $table = 'pharmacy_sale_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'sale_id',
        'billing_id', // This is the new column you added to the database.
        'medicine_id',
        'batch_id',
        'quantity',
        'unit_selling_price',
        'discount_per_item',
        'sub_total',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
