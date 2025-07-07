<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    // Define the table name for this model
    protected $table = 'users';

    // The primary key of the table
    protected $primaryKey = 'id';

    // Define the fields that can be manipulated (inserted/updated)
    protected $allowedFields = [
        'role_id', 'first_name', 'last_name', 'username', 'email',
        'password', 'phone_number', 'address', 'status', 'last_login'
    ];

    // Use timestamps to automatically manage created_at and updated_at fields
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Return type for queries
    protected $returnType    = 'array'; // Can be 'object' as well

    // Validation rules (optional but recommended)
    protected $validationRules    = [
       'username' => 'required|min_length[3]|max_length[100]', // MODIFIED: Removed |is_unique[users.username]
        'email'    => 'required|valid_email',   
        'password' => 'required|min_length[8]',
        'role_id'  => 'required|integer',
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name' => 'required|min_length[2]|max_length[100]',
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    // Callbacks for hashing password before insert/update
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    // This method hashes the password before saving to the database
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
}
