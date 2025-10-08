<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PatientAuthFilter implements FilterInterface
{
   

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no action after request
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $isLoggedIn = $session->get('isLoggedIn');
        $roleId = $session->get('role_id');

        log_message('debug', "PatientAuthFilter: isLoggedIn={$isLoggedIn}, roleId={$roleId}");

        if (!$isLoggedIn || $roleId != 10) {
            return redirect()->to('/patient-portal/login')->with('error', 'Please log in to access the patient portal.');
        }
    }
}
