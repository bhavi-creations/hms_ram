<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use CodeIgniter\API\ResponseTrait;

class CasualtyController extends BaseController
{
    use ResponseTrait;

    protected $patientModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
    }

    /**
     * Displays the list of Casualty/ER patients.
     */
    public function index()
    {
        $data['title'] = 'Casualty / ER Management';
        // Use the new method from PatientModel to get only Casualty patients OR those admitted to IPD
        $data['patients'] = $this->patientModel
                                    ->groupStart()
                                    ->where('patient_type', 'Casualty')
                                    ->orWhere('patient_type', 'IPD')
                                    ->groupEnd()
                                    ->findAll();

        return view('casualty/casualty_list', $data);
    }

    // The admitToIpd method has been removed from here
    // as it is now centralized in the Patients controller.
}