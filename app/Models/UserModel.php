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
        // Keeping 'manager_id' for backward compatibility, but hierarchy is now driven by 'role_id' and the roles table
        'role_id', 'manager_id', 'first_name', 'last_name', 'username', 'email', 
        'password', 'phone_number', 'address', 'status', 'last_login'
    ];

    // Use timestamps to automatically manage created_at and updated_at fields
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Return type for queries
    protected $returnType    = 'array';

    // Validation rules (critical updates here)
    protected $validationRules      = [
        // Ensure username is unique for new records, but ignore the current record's ID on update
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]', 
        
        // Ensure email is unique for new records, but ignore the current record's ID on update
        'email'    => 'required|valid_email|is_unique[users.email,id,{id}]', 
        
        // Password is required on insert, but we use 'permit_empty' and logic in hashPassword for updates.
        'password' => 'permit_empty|min_length[8]', 
        
        'role_id'  => 'required|integer',
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name' => 'required|min_length[2]|max_length[100]',
        
        // Validation for manager_id (optional, only validates if provided)
        'manager_id' => 'permit_empty|integer', 
    ];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    // Callbacks for hashing password before insert/update
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    // This method hashes the password before saving to the database
    protected function hashPassword(array $data)
    {
        // Only hash the password if it's explicitly set and not empty in the data array
        if (isset($data['data']['password']) && $data['data']['password'] !== '') {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        } 
        
        // If the password field is empty or not provided on update, remove it from the data array
        else if (isset($data['data']['password']) && $data['data']['password'] === '') {
             unset($data['data']['password']);
        }

        return $data;
    }
    
    /**
     * Overrides the default find() method to fetch user details including role name 
     * by joining the 'roles' table.
     * @param int|array|null $id The user ID(s).
     * @return array|null The user data (must include 'role_id' and 'role_name').
     */
    public function find($id = null)
    {
        // Use the query builder for a single ID lookup, which is what the controller expects.
        if (is_numeric($id) || (is_array($id) && count($id) === 1)) {
            $builder = $this->db->table('users');
            // Select all user fields and the role name
            $builder->select('users.*, roles.name as role_name');
            $builder->join('roles', 'roles.id = users.role_id');
            $builder->where('users.id', is_array($id) ? $id[0] : $id);
            return $builder->get()->getRowArray();
        }

        // Fallback to the parent implementation for finding multiple records or all records
        return parent::find($id);
    }
    
    /**
     * NEW: Fetches an array of User IDs that belong to a specific set of Role IDs.
     * This is crucial for the StaffAttendanceOverview controller to filter staff based on hierarchy.
     * * @param array $roleIds An array of role IDs (from roles.id).
     * @return array An array of user IDs (from users.id).
     */
    public function getUserIdsByRoleIds(array $roleIds): array
    {
        if (empty($roleIds)) {
            return [];
        }

        // Select only the user 'id' column
        $builder = $this->select('id');
        
        // Filter by the provided role IDs
        $builder->whereIn('role_id', $roleIds);
        
        // Find all records and then extract just the 'id' column into a flat array
        $results = $builder->findAll();
        
        return array_column($results, 'id');
    }

    /**
     * Fetches all users who belong to a role with the 'Department Head' management level.
     * These users will be available to assign as managers to Team Members.
     * @return array An array of user data (id, first_name, last_name).
     */
    public function getDepartmentHeads(): array
    {
        $builder = $this->db->table('users');
        $builder->select('users.id, users.first_name, users.last_name');
        $builder->join('roles', 'roles.id = users.role_id');
        // Filter users by the management_level we defined
        $builder->where('roles.management_level', 'Department Head');
        $builder->orderBy('users.last_name', 'ASC');
        
        return $builder->get()->getResultArray();
    }
}
