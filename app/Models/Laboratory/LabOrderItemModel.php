<?php
namespace App\Models\Laboratory;

use CodeIgniter\Model;

class LabOrderItemModel extends Model
{
    protected $table = 'lab_order_items';
    protected $primaryKey = 'id';
    
    // Explicitly define all allowed fields to prevent silent failures
    protected $allowedFields = [
        'lab_order_id', 
        'lab_test_id', 
        'result', 
        'result_date', 
        'status', 
        'created_at', 
        'updated_at'
    ];
    
    protected $useTimestamps = true;

    // Add validation to ensure the result is not empty
    protected $validationRules = [
        'result' => 'permit_empty|string',
        'status' => 'required',
    ];
}
