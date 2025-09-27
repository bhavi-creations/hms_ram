<?php

namespace App\Models\Diagnostics;

use CodeIgniter\Model;

class DiagnosticsTestModel extends Model
{
    protected $table = 'diagnostics_tests';
    protected $primaryKey = 'id';
    protected $allowedFields = ['test_name', 'test_type', 'price'];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
