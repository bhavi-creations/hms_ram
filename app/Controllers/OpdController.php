<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PatientModel;
use CodeIgniter\API\ResponseTrait;

class OpdController extends BaseController
{
    use ResponseTrait;

    protected $patientModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
    }

    /**
     * Displays the list of OPD patients.
     */
    public function index()
    {
        $data['title'] = 'OPD Management';
        // Use the new method from PatientModel to get only OPD patients OR those admitted to IPD
        $data['patients'] = $this->patientModel
                                    ->groupStart()
                                    ->where('patient_type', 'OPD')
                                    ->orWhere('patient_type', 'IPD')
                                    ->groupEnd()
                                    ->findAll();

        return view('opd/opd_list', $data);
    }

    // The admitToIpd method has been removed from here
    // as it is now centralized in the Patients controller.
}