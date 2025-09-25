<?php
namespace App\Models\Laboratory;

use CodeIgniter\Model;

class LabTestModel extends Model
{
    protected $table = 'lab_tests';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'test_type_id', 'price', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
