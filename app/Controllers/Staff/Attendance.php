<?php
namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Models\Staff\AttendanceModel;
use CodeIgniter\I18n\Time; 

/**
 * Handles the staff's personal attendance clocking actions (check-in/check-out)
 * and displays their personal log history with filtering.
 */
class Attendance extends BaseController
{
    protected $attendanceModel;
    protected $db; 

    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Display the user's personal attendance log and clocking status, with filters.
     */
    public function index()
    {
        // 1. Get the authenticated user's ID
        $userId = session()->get('user_id'); 

        if (!$userId) {
            return redirect()->to(base_url('login'))->with('error', 'User session ID is missing. Please log in again.');
        }

        $currentDate = date('Y-m-d');

        // --- 2. Filtering Logic ---
        // Get filter dates from GET request or set to default (last 30 days up to today)
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('-30 days'));
        $dateTo = $this->request->getGet('date_to') ?? $currentDate;
        
        // Ensure $dateTo is not in the future
        if (strtotime($dateTo) > strtotime($currentDate)) {
             $dateTo = $currentDate;
        }

        // 3. Fetch today's status
        $todayRecord = $this->attendanceModel->getTodayCheckIn($userId, $currentDate);

        // 4. Fetch filtered history 
        $logHistory = $this->attendanceModel->getSelfHistory($userId, $dateFrom, $dateTo); 
        
        // --- 5. Determine Clocking Status ---
        $isClockedIn = false;
        $isShiftComplete = false;
        
        if ($todayRecord && $todayRecord['check_in_time']) {
            if (!$todayRecord['check_out_time']) {
                $isClockedIn = true;
            } else {
                $isShiftComplete = true; 
            }
        }
        
        // --- 6. Calculate Total Working Days (Summary) ---
        // Count unique dates in the filtered results where a check-in occurred
        $uniqueDates = [];
        if ($logHistory) {
            foreach ($logHistory as $log) {
                if (!empty($log['check_in_time'])) {
                    $uniqueDates[date('Y-m-d', strtotime($log['date']))] = true;
                }
            }
        }
        $totalWorkingDays = count($uniqueDates);


        $data = [
            'title' => 'My Attendance',
            'todayRecord' => $todayRecord,
            'isClockedIn' => $isClockedIn,
            'isShiftComplete' => $isShiftComplete,
            'logHistory' => $logHistory,
            'userId' => $userId,
            'dateFrom' => $dateFrom, // Pass filters back to view for input fields
            'dateTo' => $dateTo,     // Pass filters back to view for input fields
            'totalWorkingDays' => $totalWorkingDays, // Pass the summary
        ];

        return view('profile/attendance', $data);
    }
    
    /**
     * Display the Attendance Overview for Managers/Admins, filtering staff logs.
     * This method assumes it is called by the route intended for the manager's view.
     */
    public function overview()
    {
        // 1. Get the authenticated user's ID and Role ID
        $currentUserId = session()->get('user_id');
        $currentRoleId = session()->get('role_id'); 
        
        if (!$currentUserId || !$currentRoleId) {
            return redirect()->to(base_url('login'))->with('error', 'Authentication required to view overview.');
        }

        // 2. Setup Filtering Logic
        $currentDate = date('Y-m-d');
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-d', strtotime('-30 days'));
        $dateTo = $this->request->getGet('date_to') ?? $currentDate;
        
        if (strtotime($dateTo) > strtotime($currentDate)) {
             $dateTo = $currentDate;
        }

        // 3. Determine the Role ID to filter by (New Role-to-Role Logic)
        // If Role ID 1 (Admin), set filter to null (shows all staff).
        // If any other Role ID (e.g., 7 for Pharmacy Manager), use their role ID.
        // The Model will then find all staff whose roles report to this ID.
        $isGlobalAdmin = ($currentRoleId == AttendanceModel::ADMIN_ROLE_ID); 
        
        // Pass the manager's role ID (e.g., 7) to the model, or null for Admin.
        $reportingRoleIdFilter = $isGlobalAdmin ? null : $currentRoleId;


        // 4. Fetch the aggregated log history for the overview
        // IMPORTANT: This call now passes the $reportingRoleIdFilter to the model.
        $logHistory = $this->attendanceModel->getStaffAttendanceOverview(
            $dateFrom, 
            $dateTo, 
            $reportingRoleIdFilter // Now passing the manager's ROLE ID (e.g., 7)
        );
        
        $data = [
            'title' => 'Attendance Overview',
            'logHistory' => $logHistory,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'currentRole' => $currentRoleId 
        ];

        // 5. Load the correct MySQL-backed view
        return view('staff/attendance_overview', $data);
    }

    /**
     * Handle the Clock-In action.
     * Route: POST /attendance/checkIn
     */
    public function checkIn()
    {
        // Use the corrected session key 'user_id'
        $userId = session()->get('user_id'); 
        if (!$userId) {
            return redirect()->to(base_url('login'))->with('error', 'Authentication required for clocking action.');
        }
        
        $currentDate = Time::now()->toDateString(); // Y-m-d
        $currentTime = Time::now()->toDateTimeString(); // Y-m-d H:i:s

        $existingRecord = $this->attendanceModel->getTodayCheckIn($userId, $currentDate);

        // Prevent duplicate check-in or check-in after check-out
        if ($existingRecord && $existingRecord['check_in_time']) {
            if ($existingRecord['check_out_time'] !== null) {
                return redirect()->to(base_url('attendance'))->with('info', 'Your shift for today is already complete.');
            }
            return redirect()->to(base_url('attendance'))->with('warning', 'You are already clocked in for today.');
        } 
        
        $data = [
            'user_id' => $userId,
            'date' => $currentDate,
            'check_in_time' => $currentTime,
            'status' => 'IN' 
        ];

        if ($this->attendanceModel->insert($data)) {
            return redirect()->to(base_url('attendance'))->with('success', 'Clocked in successfully!');
        } else {
            log_message('error', 'Failed to save check-in data: ' . print_r($this->attendanceModel->errors(), true));
            return redirect()->to(base_url('attendance'))->with('error', 'Failed to record check-in. Please try again.');
        }
    }

    /**
     * Handle the Clock-Out action.
     * Route: POST /attendance/checkOut
     */
    public function checkOut()
    {
        // Use the corrected session key 'user_id'
        $userId = session()->get('user_id'); 
        if (!$userId) {
            return redirect()->to(base_url('login'))->with('error', 'Authentication required for clocking action.');
        }

        $currentDate = Time::now()->toDateString();
        $currentTime = Time::now()->toDateTimeString();

        // Find today's open attendance record
        $todayRecord = $this->attendanceModel->getTodayCheckIn($userId, $currentDate);

        if (!$todayRecord || !$todayRecord['check_in_time']) {
            return redirect()->to(base_url('attendance'))->with('warning', 'You must clock in before clocking out.');
        }

        if ($todayRecord['check_out_time']) {
            return redirect()->to(base_url('attendance'))->with('info', 'You have already clocked out for today.');
        }

        // Update the existing record with the check-out time
        $updateData = [
            'check_out_time' => $currentTime,
            'status' => 'Present' // Assuming you want a specific status after a full shift
        ];
        
        // Update the record using the ID of the existing record
        if ($this->attendanceModel->update($todayRecord['id'], $updateData)) {
            return redirect()->to(base_url('attendance'))->with('success', 'Clocked out successfully!');
        } else {
            log_message('error', 'Failed to save check-out data: ' . print_r($this->attendanceModel->errors(), true));
            return redirect()->to(base_url('attendance'))->with('error', 'Failed to record check-out. Please try again.');
        }
    }
}
