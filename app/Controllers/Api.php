<?php namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use App\Models\DoctorModel; // Make sure this path is correct for your DoctorModel

class Api extends ResourceController
{
    protected $doctorModel;

    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
    }

    /**
     * AJAX endpoint to search and return doctors for Select2 dropdown.
     */
    public function getDoctors()
    {
        // Get the search term sent by Select2 via GET parameter 'term'
        $search = $this->request->getVar('term'); 

        // Query the database for doctors matching the search term
        $doctors = $this->doctorModel
                        ->select('id, first_name, last_name, specialization')
                        ->like('first_name', $search) // Search by first name
                        ->orLike('last_name', $search)  // Or last name
                        ->orLike('specialization', $search) // Or specialization
                        ->findAll(); // Get all matching results

        // Return the results in a JSON format that Select2 expects
        return $this->response->setJSON(['doctors' => $doctors]);
    }
}