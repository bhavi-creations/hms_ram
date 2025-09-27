<?php

namespace App\Models\Diagnostics;

use CodeIgniter\Model;

class DiagnosticsOrderFileModel extends Model
{
    protected $table = 'diagnostics_order_files';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'diagnostics_order_item_id',
        'file_name',
        'file_path',
        'uploaded_by',
        'remarks', // Assuming 'remarks' is still needed for files
    ];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Fetches all uploaded files associated with a specific diagnostic order
     * by joining through the diagnostics_order_items table.
     *
     * @param int $orderId The ID of the diagnostic order.
     * @return array An array of file records.
     */
    public function getFilesByOrderId(int $orderId): array
    {
        // 1. Select the file details and the item ID it belongs to.
        return $this->select('diagnostics_order_files.*')
            // 2. Join to the items table using the item ID in the file record.
            ->join('diagnostics_order_items', 
                   'diagnostics_order_items.id = diagnostics_order_files.diagnostics_order_item_id')
            // 3. Filter by the main diagnostics_order_id in the items table.
            ->where('diagnostics_order_items.diagnostics_order_id', $orderId)
            // 4. Ensure we only get unique file records, and order them.
            ->orderBy('diagnostics_order_files.created_at', 'ASC')
            ->findAll();
    }
}
