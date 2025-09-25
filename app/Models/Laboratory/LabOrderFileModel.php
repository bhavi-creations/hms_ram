<?php

namespace App\Models\Laboratory;

use CodeIgniter\Model;

class LabOrderFileModel extends Model
{
    protected $table = 'lab_order_files';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'lab_order_item_id',
        'file_name',
        'file_path',
        'uploaded_at',
    ];

    protected $useTimestamps = false;

    protected $beforeInsert = ['setUploadedAt'];

    protected function setUploadedAt(array $data)
    {
        $data['data']['uploaded_at'] = date('Y-m-d H:i:s');
        return $data;
    }
    
    /**
     * Get files by lab_order_item_id
     */
    public function getFilesByOrderItem(int $orderItemId)
    {
        return $this->where('lab_order_item_id', $orderItemId)->findAll();
    }
}
