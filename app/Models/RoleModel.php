<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    // CRUCIAL: Ensure 'management_level' and 'reports_to_role_id' are allowed fields
    protected $allowedFields = ['name', 'description', 'management_level', 'reports_to_role_id']; 
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $validationRules      = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    /**
     * Fetches all roles, joining with the roles table itself 
     * to get the name of the reporting manager's role.
     * * This is required for the roles list (index) view.
     * * @return array
     */
    public function getRolesWithReportsToName(): array
    {
        $builder = $this->db->table($this->table);
        
        // Select all role fields, plus the manager role's name
        $builder->select('roles.*, manager.name as reports_to_role_name');
        
        // Left Join to the same table (aliased as 'manager') on the reports_to_role_id field
        $builder->join(
            "{$this->table} manager", 
            'manager.id = roles.reports_to_role_id', 
            'left' // Use LEFT JOIN to include roles that don't report to anyone (like Department Head)
        );

        $builder->orderBy('roles.name', 'ASC');

        return $builder->get()->getResultArray();
    }
}
