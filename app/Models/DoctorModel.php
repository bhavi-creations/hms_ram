<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorModel extends Model
{
    protected $table = 'doctors';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    // Return all doctors
   public function findAllDoctors()
{
    return $this->select('id, first_name, last_name, specialization')
                ->findAll();
}

}
