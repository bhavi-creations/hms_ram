<?php namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyPurchaseModel extends Model
{
    protected $table      = 'pharmacy_purchases';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'supplier_id',
        'purchase_date',
        'invoice_number',
        'total_amount',
        'status',
        'ordered_by_user_id',
        'received_by_user_id',
        'received_at',
        'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}