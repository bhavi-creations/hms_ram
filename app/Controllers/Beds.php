<?php

namespace App\Controllers;

use App\Models\WardModel;
use App\Models\BedModel;
use CodeIgniter\Controller;

class Beds extends BaseController
{
    protected $wardModel;
    protected $bedModel;

    public function __construct()
    {
        // Initialize the WardModel and BedModel
        $this->wardModel = new WardModel();
        $this->bedModel  = new BedModel();
        // Helper for form validation and URL
        helper(['form', 'url']);
    }

    /**
     * Displays a list of all beds or beds filtered by a specific ward.
     * Also provides options to filter by status and edit individual bed status.
     *
     * @param int|null $wardId Optional: The ID of the ward to filter beds by.
     */
    public function index($wardId = null)
    {
        $beds = [];
        $selectedWard = null;

        // Fetch all wards to display as filter buttons
        $wards = $this->wardModel->findAll();

        // Get the current status filter from the URL query parameter
        $currentStatusFilter = $this->request->getGet('status');

        if ($wardId) {
            // If a ward ID is provided, filter beds by that ward
            $beds = $this->bedModel->where('ward_id', $wardId)->findAll();
            $selectedWard = $this->wardModel->find($wardId);
        } else {
            // If no ward ID, display all beds
            $beds = $this->bedModel->findAll();
        }

        $data = [
            'title'        => 'Beds Management',
            'wards'        => $wards, // All wards for filter buttons
            'beds'         => $beds, // Beds to display
            'selectedWard' => $selectedWard, // Currently selected ward for highlighting
            'currentStatusFilter' => $currentStatusFilter, // Pass current status filter to view
            'wardModel'    => $this->wardModel // Pass the wardModel instance to the view
        ];
        return view('hospital_resources/beds/index', $data);
    }

    /**
     * Handles AJAX request to update the status of a specific bed.
     *
     * @param int $id The ID of the bed to update.
     * @return \CodeIgniter\HTTP\ResponseInterface JSON response indicating success or failure.
     */
    public function updateStatus($id)
    {
        // --- DEBUGGING LOGS START ---
        log_message('debug', 'Beds::updateStatus called for ID: ' . $id);
        log_message('debug', 'Request Method: ' . $this->request->getMethod());
        log_message('debug', 'Raw POST data: ' . json_encode($this->request->getPost()));
        log_message('debug', 'Server REQUEST_METHOD: ' . $_SERVER['REQUEST_METHOD']);
        // --- DEBUGGING LOGS END ---

        // Ensure the request is a POST request.
        // Changed comparison to strtoupper() to handle case sensitivity.
        if (strtoupper($this->request->getMethod()) === 'POST') {
            $newStatus = $this->request->getPost('status');
            $bed = $this->bedModel->find($id);

            if (!$bed) {
                log_message('error', 'Bed not found for ID: ' . $id);
                return $this->response->setJSON(['success' => false, 'message' => 'Bed not found.']);
            }

            // Validate the new status against allowed values
            $allowedStatuses = ['Available', 'Occupied', 'Under Maintenance', 'Dirty'];
            if (!in_array($newStatus, $allowedStatuses)) {
                log_message('error', 'Invalid status provided: ' . $newStatus . ' for Bed ID: ' . $id);
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid status provided.']);
            }

            // Update the bed status
            if ($this->bedModel->update($id, ['status' => $newStatus])) {
                log_message('debug', 'Bed status updated successfully for ID: ' . $id . ' to ' . $newStatus);
                // Return the updated CSRF hash for subsequent requests
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Bed status updated successfully.',
                    'csrfHash' => csrf_hash() // Return new CSRF hash
                ]);
            } else {
                log_message('error', 'Failed to update bed status for ID: ' . $id);
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to update bed status.']);
            }
        }
        // If it's not a POST request, return an invalid request method message
        log_message('error', 'Invalid request method. Expected POST, got ' . $this->request->getMethod());
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method.']);
    }

  
    public function filter($wardId)
    {
        return $this->index($wardId);
    }
}
