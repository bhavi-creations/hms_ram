<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\DoctorModel; // Make sure to use your DoctorModel
use App\Models\PatientModel; // Make sure to use your PatientModel

class AppointmentController extends BaseController
{
    // Ensure models are initialized in the constructor
    protected $appointmentModel;
    protected $doctorModel;
    protected $patientModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->doctorModel = new DoctorModel();
        $this->patientModel = new PatientModel();
        helper('form'); // Ensure form helper is loaded for form_open_multipart
        helper('filesystem'); // Ensure filesystem helper is loaded for file operations
    }

    public function index()
    {
        $data['title'] = 'Current Appointments'; // Changed title to reflect filtered view

        $data['appointments'] = $this->appointmentModel
            ->select('appointments.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name, doctors.first_name as doctor_first_name, doctors.last_name as doctor_last_name')
            ->join('patients', 'appointments.patient_id = patients.id')
            ->join('doctors', 'appointments.doctor_id = doctors.id')
            ->whereIn('appointments.status', ['Pending', 'Confirmed']) // Only show Pending or Confirmed
            ->orderBy('appointment_date', 'ASC') // Order by date, then time
            ->orderBy('appointment_time', 'ASC')
            ->findAll();

        return view('appointments/index', $data);
    }


    public function create()
    {
        $data['doctors'] = $this->doctorModel->where('status', 'Active')->findAll();
        // Patients are not directly loaded here anymore, they are loaded via AJAX
        // $data['patients'] = $this->patientModel->findAll(); // This line is no longer needed
        return view('appointments/create', $data);
    }

    public function store()
    {
        $rules = [
            'patient_id'       => 'required|integer', // This is correct here, as patient_id is selected from dropdown
            'doctor_id'        => 'required|integer',
            'appointment_date' => 'required|valid_date[Y-m-d]',
            'appointment_time' => 'required',
            'reason_for_visit' => 'permit_empty|string|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'patient_id'       => $this->request->getPost('patient_id'),
            'doctor_id'        => $this->request->getPost('doctor_id'),
            'appointment_date' => $this->request->getPost('appointment_date'),
            'appointment_time' => $this->request->getPost('appointment_time'),
            'reason_for_visit' => $this->request->getPost('reason_for_visit'),
            'status'           => 'Confirmed'
        ];

        if ($this->appointmentModel->save($data)) {
            return redirect()->to('/appointments')->with('success', 'Appointment created successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create appointment.');
        }
    }

    public function edit($id)
    {
        $appointment = $this->appointmentModel->find($id);

        if (!$appointment) {
            return redirect()->to('/appointments')->with('error', 'Appointment not found.');
        }

        $data['appointment'] = $appointment;
        $data['doctors'] = $this->doctorModel->where('status', 'Active')->where('is_available', 1)->findAll();

        return view('appointments/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'doctor_id'        => 'required|integer',
            'appointment_date' => 'required|valid_date[Y-m-d]',
            'appointment_time' => 'required',
            'reason_for_visit' => 'permit_empty|string|max_length[500]',
            'status'           => 'in_list[Pending,Confirmed,Cancelled,Completed]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'doctor_id'        => $this->request->getPost('doctor_id'),
            'appointment_date' => $this->request->getPost('appointment_date'),
            'appointment_time' => $this->request->getPost('appointment_time'),
            'reason_for_visit' => $this->request->getPost('reason_for_visit'),
            'status'           => $this->request->getPost('status') ?? 'Pending'
        ];

        if ($this->appointmentModel->update($id, $data)) {
            return redirect()->to('/appointments')->with('success', 'Appointment updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update appointment.');
        }
    }

    public function delete($id)
    {
        $appointment = $this->appointmentModel->find($id);

        if ($appointment) {
            $this->appointmentModel->delete($id);
            return redirect()->to('/appointments')->with('success', 'Appointment deleted successfully.');
        } else {
            return redirect()->to('/appointments')->with('error', 'Appointment not found.');
        }
    }

    public function patientAppointments()
    {
        $patientId = session()->get('patient_id');

        if (!$patientId) {
            return redirect()->to('/login')->with('error', 'You must be logged in to view appointments.');
        }

        $data['appointments'] = $this->appointmentModel
            ->select('appointments.*, doctors.first_name as doctor_first_name, doctors.last_name as doctor_last_name')
            ->join('doctors', 'appointments.patient_id = patients.id') // Corrected join
            ->where('appointments.patient_id', $patientId)
            ->orderBy('appointment_date', 'DESC')
            ->findAll();

        $data['title'] = 'My Appointments';

        return view('appointments/patient_view', $data);
    }

    /**
     * Displays a list of completed and cancelled appointments (Appointment History).
     * This method is for admin/staff view.
     */
    public function history()
    {
        $data['appointments'] = $this->appointmentModel
            ->select('appointments.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name, doctors.first_name as doctor_first_name, doctors.last_name as doctor_last_name')
            ->join('patients', 'appointments.patient_id = patients.id')
            ->join('doctors', 'appointments.doctor_id = doctors.id')
            ->whereIn('appointments.status', ['Completed', 'Cancelled']) // Include both Completed and Cancelled
            ->orderBy('appointment_date', 'DESC')
            ->orderBy('appointment_time', 'DESC') // Order by latest completed/cancelled first
            ->findAll();

        $data['title'] = 'Appointment History'; // Updated title

        return view('appointments/history', $data);
    }

    // --- DOCTOR-SPECIFIC APPOINTMENT METHODS ---

    public function doctorAppointments()
    {
        $session = session();
        $doctor_id = $session->get('doctor_id');
        $role_id = $session->get('role_id');

        if ($role_id != 2 || !$doctor_id) {
            session()->setFlashdata('error', 'Access denied. You do not have permission to view this page.');
            return redirect()->to('/doctor/dashboard');
        }

        $data['appointments'] = $this->appointmentModel
            ->select('appointments.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name')
            ->join('patients', 'appointments.patient_id = patients.id')
            ->where('appointments.doctor_id', $doctor_id)
            ->where('appointments.status !=', 'Completed') // Exclude completed appointments
            ->orderBy('appointment_date', 'DESC')
            ->orderBy('appointment_time', 'ASC')
            ->findAll();

        $data['title'] = 'My Current Appointments';
        $data['doctor_name'] = $session->get('first_name') . ' ' . $session->get('last_name');

        return view('doctors/appointments_list', $data);
    }

    public function doctorViewAppointment($id)
    {
        $session = session();
        $doctor_id = $session->get('doctor_id');
        $role_id = $session->get('role_id');

        if ($role_id != 2 || !$doctor_id) {
            session()->setFlashdata('error', 'Access denied. You do not have permission to view this page.');
            return redirect()->to('/doctor/dashboard');
        }

        $appointment = $this->appointmentModel
            ->select('appointments.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name, doctors.first_name as doctor_first_name, doctors.last_name as doctor_last_name')
            ->join('patients', 'appointments.patient_id = patients.id')
            ->join('doctors', 'appointments.doctor_id = doctors.id')
            ->where('appointments.id', $id)
            ->where('appointments.doctor_id', $doctor_id)
            ->first();

        if (!$appointment) {
            session()->setFlashdata('error', 'Appointment not found or you do not have permission to view it.');
            return redirect()->to('/doctor/appointments');
        }

        $patient = $this->patientModel->find($appointment['patient_id']);
        $data['patient_reports'] = json_decode($patient['reports'] ?? '[]', true);

        $data['appointment'] = $appointment;
        $data['title'] = 'View Appointment Details';

        return view('doctors/appointment_view', $data);
    }

    public function doctorEditAppointment($id)
    {
        $session = session();
        $doctor_id = $session->get('doctor_id');
        $role_id = $session->get('role_id');

        if ($role_id != 2 || !$doctor_id) {
            session()->setFlashdata('error', 'Access denied. You do not have permission to view this page.');
            return redirect()->to('/doctor/dashboard');
        }

        $appointment = $this->appointmentModel
            ->select('appointments.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name')
            ->join('patients', 'appointments.patient_id = patients.id')
            ->where('appointments.id', $id)
            ->where('appointments.doctor_id', $doctor_id)
            ->first();

        if (!$appointment) {
            session()->setFlashdata('error', 'Appointment not found or you do not have permission to edit it.');
            return redirect()->to('/doctor/appointments');
        }

        $patient = $this->patientModel->find($appointment['patient_id']);
        $data['patient_reports'] = json_decode($patient['reports'] ?? '[]', true);
        $data['patient_id'] = $appointment['patient_id'];

        $data['appointment'] = $appointment;
        $data['doctors'] = $this->doctorModel->where('id', $doctor_id)->findAll();
        $data['title'] = 'Edit My Appointment';

        return view('doctors/appointment_edit', $data);
    }

    public function doctorUpdateAppointment($id)
    {
        $session = session();
        $doctor_id = $session->get('doctor_id');
        $role_id = $session->get('role_id');

        if ($role_id != 2 || !$doctor_id) {
            session()->setFlashdata('error', 'Access denied. You do not have permission to perform this action.');
            return redirect()->to('/doctor/dashboard');
        }

        $existingAppointment = $this->appointmentModel
            ->where('id', $id)
            ->where('doctor_id', $doctor_id)
            ->first();

        if (!$existingAppointment) {
            session()->setFlashdata('error', 'Appointment not found or you do not have permission to update it.');
            return redirect()->to('/doctor/appointments');
        }

        $rules = [
            'appointment_date' => 'required|valid_date[Y-m-d]',
            'appointment_time' => 'required',
            'reason_for_visit' => 'permit_empty|string|max_length[500]',
            'status'           => 'in_list[Pending,Confirmed,Cancelled,Completed]'
        ];

        $files = $this->request->getFiles();
        if (!empty($files['patient_reports'])) {
            $rules['patient_reports'] = 'uploaded[patient_reports]|max_size[patient_reports,5000]|ext_in[patient_reports,pdf,jpg,jpeg,png,gif]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $appointmentData = [
            'appointment_date' => $this->request->getPost('appointment_date'),
            'appointment_time' => $this->request->getPost('appointment_time'),
            'reason_for_visit' => $this->request->getPost('reason_for_visit'),
            'status'           => $this->request->getPost('status') ?? 'Pending',
            'doctor_id'        => $doctor_id
        ];

        $patient_id = $existingAppointment['patient_id'];
        $patient = $this->patientModel->find($patient_id);
        $existingReports = json_decode($patient['reports'] ?? '[]', true);
        $newReportNames = [];

        if (!empty($files['patient_reports'])) {
            foreach ($files['patient_reports'] as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $uploadPath = ROOTPATH . 'public/uploads/patient_reports/';
                    
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }

                    if ($file->move($uploadPath, $newName)) {
                        $newReportNames[] = $newName;
                    } else {
                        session()->setFlashdata('error', 'Failed to upload one or more files: ' . $file->getErrorString());
                        return redirect()->back()->withInput();
                    }
                }
            }
        }

        $updatedReports = array_merge($existingReports, $newReportNames);
        $patientData = ['reports' => json_encode($updatedReports)];

        $reportsToDelete = $this->request->getPost('delete_reports');
        if (!empty($reportsToDelete) && is_array($reportsToDelete)) {
            $finalReports = [];
            foreach ($updatedReports as $reportName) {
                if (!in_array($reportName, $reportsToDelete)) {
                    $finalReports[] = $reportName;
                } else {
                    $filePath = ROOTPATH . 'public/uploads/patient_reports/' . $reportName;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
            }
            $patientData['reports'] = json_encode($finalReports);
        } else {
            $patientData['reports'] = json_encode($updatedReports);
        }

        $appointmentUpdateSuccess = $this->appointmentModel->update($id, $appointmentData);
        $patientUpdateSuccess = $this->patientModel->update($patient_id, $patientData);

        if ($appointmentUpdateSuccess && $patientUpdateSuccess) {
            session()->setFlashdata('success', 'Appointment and patient reports updated successfully.');
            return redirect()->to('/doctor/appointments');
        } else {
            session()->setFlashdata('error', 'Failed to update appointment or patient reports.');
            return redirect()->back()->withInput();
        }
    }

    public function doctorAppointmentHistory()
    {
        $session = session();
        $doctor_id = $session->get('doctor_id');
        $role_id = $session->get('role_id');

        if ($role_id != 2 || !$doctor_id) {
            session()->setFlashdata('error', 'Access denied. You do not have permission to view this page.');
            return redirect()->to('/doctor/dashboard');
        }

        $data['appointments'] = $this->appointmentModel
            ->select('appointments.*, patients.first_name as patient_first_name, patients.last_name as patient_last_name')
            ->join('patients', 'appointments.patient_id = patients.id')
            ->where('appointments.doctor_id', $doctor_id)
            ->where('appointments.status', 'Completed')
            ->orderBy('appointment_date', 'DESC')
            ->orderBy('appointment_time', 'ASC')
            ->findAll();

        $data['title'] = 'My Appointment History';
        $data['doctor_name'] = $session->get('first_name') . ' ' . $session->get('last_name');

        return view('doctors/appointment_history', $data);
    }
}
