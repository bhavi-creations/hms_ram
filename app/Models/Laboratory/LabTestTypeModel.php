<?php
namespace App\Models\Laboratory;

use CodeIgniter\Model;

class LabTestTypeModel extends Model
{
    protected $table = 'lab_test_types';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'description', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
