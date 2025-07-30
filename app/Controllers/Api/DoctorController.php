<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel; // Assuming your Doctor information is in the UserModel or a related DoctorModel

class DoctorController extends BaseController
{
    use ResponseTrait;

    public function getDoctors()
    {
        $request = service('request');
        $term = $request->getGet('term'); // Select2 sends the search query as 'term'

        // You'll need to fetch doctors from your database.
        // Assuming doctors are users with a specific role, or you have a dedicated DoctorModel.
        // For demonstration, let's assume 'role_id' 4 is for 'Doctors'. Adjust as per your DB.

        $userModel = new UserModel(); // Adjust if you have a specific DoctorModel

        $doctors = $userModel
            ->select('users.id, users.first_name, users.last_name, doctor_profiles.specialization') // Adjust columns as needed
            ->join('roles', 'roles.id = users.role_id')
            ->join('doctor_profiles', 'doctor_profiles.user_id = users.id', 'left') // Assuming you have a doctor_profiles table
            ->where('roles.name', 'Doctor'); // Assuming 'Doctor' is the role name for doctors

        if ($term) {
            $doctors->groupStart()
                    ->like('first_name', $term, 'both')
                    ->orLike('last_name', $term, 'both')
                    ->orLike('specialization', $term, 'both')
                    ->groupEnd();
        }

        $doctors = $doctors->findAll();

        $response = [
            'doctors' => $doctors
        ];

        return $this->respond($response);
    }
}