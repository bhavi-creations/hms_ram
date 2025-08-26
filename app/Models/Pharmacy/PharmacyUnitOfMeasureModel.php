<?php

namespace App\Models\Pharmacy;

use CodeIgniter\Model;

class PharmacyUnitOfMeasureModel extends Model
{
    protected $table = 'pharmacy_units_of_measure';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false; // We do not need soft deletes for this table

    protected $allowedFields = ['name'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
