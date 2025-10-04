<?php
namespace App\Models\Staff;

use CodeIgniter\Model;

/**
 * Manages staff attendance records in the database.
 * Table: 'attendance'
 */
class AttendanceModel extends Model
{
    protected $table = 'attendance';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $returnType = 'array';
    protected $useSoftDeletes = false; 

    protected $allowedFields = [
        'user_id', 
        'date', 
        'check_in_time', 
        'check_out_time', 
        'status', 
        'note'
    ];

    // Dates
    protected $useTimestamps = true; 
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation = true;

    // Static Role IDs (Must match controller)
    const ADMIN_ROLE_ID = 1; 
    const PHARMACY_MANAGER_ROLE_ID = 9; 
    const PHARMACY_SALES_ROLE_ID = 8; 

    /**
     * Finds today's check-in record for a specific user.
     */
    public function getTodayCheckIn(int $userId, string $date)
    {
        return $this->where('user_id', $userId)
                    ->where('date', $date)
                    ->first();
    }

    /**
     * Gets the attendance history for a single user (self view).
     */
    public function getSelfHistory(int $userId, string $dateFrom = null, string $dateTo = null)
    {
        $builder = $this->where('user_id', $userId);
        
        // Apply date filtering
        if ($dateFrom) {
            $builder->where('date >=', $dateFrom);
        }
        
        if ($dateTo) {
            $builder->where('date <=', $dateTo);
        }

        return $builder->orderBy('date', 'DESC')
                       ->orderBy('check_in_time', 'DESC')
                       ->findAll();
    }
    
    /**
     * Retrieves attendance records for all staff (Admin) or staff reporting to a specific manager role.
     * This method joins the 'attendance' table with the 'users' and 'roles' tables.
     *
     * @param string $dateFrom Start date for filtering (YYYY-MM-DD).
     * @param string $dateTo End date for filtering (YYYY-MM-DD).
     * @param int|null $reportingRoleId If provided, filters records where staff's role reports to this role ID. Null fetches all (Admin view).
     * @return array Array of attendance records including staff name, employee ID, and role name.
     */
    public function getStaffAttendanceOverview(string $dateFrom, string $dateTo, ?int $reportingRoleId = null): array
    {
        $builder = $this->db->table('attendance as al');
        
        // Select all attendance fields plus the user's name, ID, and role name
        $builder->select('al.*, u.first_name, u.last_name, u.id as employee_id, r.name as role_name');
        
        // Join 1: attendance log to users
        $builder->join('users as u', 'u.id = al.user_id');
        
        // Join 2: users to roles (CRITICAL: Needed to check reports_to_role_id)
        $builder->join('roles as r', 'r.id = u.role_id'); 

        // Apply date filtering
        $builder->where('al.date >=', $dateFrom);
        $builder->where('al.date <=', $dateTo);
        
        // Filter by the staff member's role's reports_to_role_id.
        if ($reportingRoleId !== null) {
            $builder->where('r.reports_to_role_id', $reportingRoleId);
        }

        // Order by date and time
        return $builder->orderBy('al.date', 'DESC')
                       ->orderBy('al.check_in_time', 'DESC')
                       ->get()
                       ->getResultArray();
    }
}
