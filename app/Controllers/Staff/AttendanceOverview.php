<?php
namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;

/**
 * Handles the Staff Attendance Overview page.
 * This controller authorizes management users (Executive, Senior, Head)
 * and determines which staff members they are authorized to view based on their role and team structure.
 */
class AttendanceOverview extends BaseController
{
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    /**
     * Renders the staff attendance overview, filtering users based on management level.
     * Route: /staff/attendance/overview
     */
    public function overview()
    {
        $userId = session()->get('user_id');
        $managementLevel = session()->get('management_level');
        // NOTE: We assume 'role_name' might be in the session, but rely primarily on managementLevel
        $userRoleName = session()->get('role_name') ?? 'N/A'; 

        // --- 1. Authorization Check (FIXES REDIRECTION ISSUE) ---
        
        // Only allow access if management level is defined and is NOT 'Team Member' or 'Users' (base level)
        if (!$managementLevel || $managementLevel === 'Team Member' || $managementLevel === 'Users') {
            // This is the source of the redirection if authorization fails
            return redirect()->to(base_url('dashboard'))->with('error', 'You are not authorized to view the staff attendance overview report.');
        }

        // --- 2. Determine Authorized Staff IDs to View ---
        
        // CRITICAL FIX: Initialize the array to prevent Undefined variable error.
        $authorizedStaffIds = [];

        switch ($managementLevel) {
            case 'Executive Leader':
                // Executive Leaders see everyone. The view script handles this with a special flag.
                $authorizedStaffIds = ['ALL_STAFF_VIEW'];
                break;

            case 'Department Head':
                // Department Heads see only the users who report directly to them.
                // Query users where manager_id matches the current user's ID.
                $teamMembers = $this->userModel
                    ->select('id')
                    ->where('manager_id', $userId)
                    ->findAll();
                    
                $authorizedStaffIds = array_column($teamMembers, 'id');
                // Ensure the head can see their own attendance record too
                $authorizedStaffIds[] = $userId;
                break;

            case 'Senior Management':
                // Senior Management sees everyone EXCEPT Executive Leaders.
                
                // 1. Get the role IDs of Executive Leader roles
                $executiveRoleIds = $this->roleModel
                    ->select('id')
                    ->where('management_level', 'Executive Leader')
                    ->findAll();
                $excludedRoleIds = array_column($executiveRoleIds, 'id');
                
                // 2. Get all staff user IDs excluding those roles
                $allStaff = $this->userModel
                    ->select('id')
                    ->whereNotIn('role_id', $excludedRoleIds)
                    ->findAll();

                $authorizedStaffIds = array_column($allStaff, 'id');
                break;
                
            default:
                // Fallback for any other unexpected management level
                 return redirect()->to(base_url('dashboard'))->with('error', 'Invalid management configuration. Access denied.');
        }
        
        // Ensure array contains only unique values and remove null/false (just for safety)
        $authorizedStaffIds = array_filter(array_unique($authorizedStaffIds));
        
        // Encode the list of authorized IDs for the JavaScript view
        $authorizedStaffIdsJson = json_encode($authorizedStaffIds);
        
        // --- TEMPORARY DEBUG: UNCOMMENT THE NEXT LINE TO CHECK THE AUTHORIZED STAFF ID LIST ---
        // This line MUST be uncommented to stop the view from loading!
        // echo "Management Level: {$managementLevel}<br>Calculated Authorized IDs: {$authorizedStaffIdsJson}"; die();
        
        $data = [
            'title' => 'Staff Attendance Overview',
            // This JSON string is crucial for the JavaScript in the view to filter Firestore data
            'authorizedStaffIdsJson' => $authorizedStaffIdsJson, 
            'userRoleName' => $userRoleName // Useful for displaying messages
        ];

        return view('staff/attendance', $data);
    }
}
