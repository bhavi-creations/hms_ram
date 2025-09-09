<?php namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyReturnModel extends Model
{
    protected $table      = 'pharmacy_returns';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'sale_id',
        'billing_id',          // <-- Add this line to allow billing_id updates
        'sale_item_id',
        'medicine_id',
        'batch_id',
        'quantity_returned',
        'return_date',
        'return_reason',
        'requested_by_user_id',
        'approval_status',
        'approved_by_user_id',
        'approval_date',
        'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules   = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}
