<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use CodeIgniter\API\ResponseTrait;

class GeneralController extends BaseController
{
    use ResponseTrait;

    protected $patientModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
    }

    /**
     * Displays the list of General patients, including those admitted to IPD.
     */
    public function index()
    {
        $data['title'] = 'General Patient Management';
        // Fetch patients who are currently 'General' OR 'IPD'
        // This ensures patients who transition from General to IPD are still visible on this list.
        $data['patients'] = $this->patientModel
                                    ->groupStart()
                                    ->where('patient_type', 'General')
                                    ->orWhere('patient_type', 'IPD')
                                    ->groupEnd()
                                    ->findAll();

        return view('general/general_patients_list', $data);
    }
}