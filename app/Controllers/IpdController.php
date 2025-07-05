<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PatientModel;

class IpdController extends BaseController
{
    protected $patientModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
    }

    /**
     * Displays the list of IPD patients.
     */
    public function index()
    {
        $data['title'] = 'IPD Management';
        // Get all patients where patient_type is 'IPD'
        $data['patients'] = $this->patientModel->where('patient_type', 'IPD')->findAll();

        return view('ipd/ipd_list', $data);
    }
}