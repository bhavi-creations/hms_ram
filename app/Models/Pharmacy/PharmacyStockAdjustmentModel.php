<?php namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyStockAdjustmentModel extends Model
{
    protected $table      = 'pharmacy_stock_adjustments';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'medicine_id',
        'batch_id',
        'adjustment_type',
        'quantity',
        'reason',
        'adjusted_by_user_id',
        'adjustment_date',
        'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;
}