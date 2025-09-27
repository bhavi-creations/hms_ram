<?php

namespace App\Models\Diagnostics;

use CodeIgniter\Model;

class DiagnosticsOrderItemModel extends Model
{
    protected $table             = 'diagnostics_order_items';
    protected $primaryKey        = 'id';
    protected $useAutoIncrement  = true;
    protected $returnType        = 'array';
    protected $useSoftDeletes    = false;
    protected $protectFields     = true;
    protected $allowedFields     = [
        'diagnostics_order_id',
        'diagnostics_test_id',
        'result', // <-- FIX: Changed from 'result_value' to 'result' to match DB column and controller key
        'status',
        'remarks',
        'result_date', // Added result_date as it is used in the controller update
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Fetches all order items for a specific diagnostic order, including test names and types.
     * * This method fulfills the requirement from the Diagnostics controller's viewReport method.
     *
     * @param int $orderId The ID of the diagnostic order.
     * @return array An array of order item data.
     */
    public function getResultsByOrderId(int $orderId): array
    {
        return $this->select('diagnostics_order_items.*, 
                              diagnostics_tests.test_name, 
                              diagnostics_tests.test_type') // FIXED: Now correctly selecting the 'test_type' column as per your database schema.
                     ->join('diagnostics_tests', 'diagnostics_tests.id = diagnostics_order_items.diagnostics_test_id')
                     ->where('diagnostics_order_items.diagnostics_order_id', $orderId)
                     ->findAll();
    }

    /**
     * Gets a list of tests for a specific diagnostic order, including test name and price.
     *
     * @param int $orderId The ID of the diagnostic order.
     * @return array An array of test details.
     */
    public function getTestsForOrder(int $orderId): array
    {
        // NOTE: We are now selecting the price field from the diagnostics_tests table.
        return $this->select('diagnostics_order_items.*, diagnostics_tests.test_name, diagnostics_tests.price')
                    ->join('diagnostics_tests', 'diagnostics_tests.id = diagnostics_order_items.diagnostics_test_id')
                    ->where('diagnostics_order_items.diagnostics_order_id', $orderId)
                    ->findAll();
    }
}
