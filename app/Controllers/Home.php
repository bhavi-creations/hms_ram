<?php

namespace App\Controllers;

class Home extends BaseController
{
    // This is the dashboard page after login
    public function index()
    {
        // Check if user is logged in (this check will also be done by the filter)
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $data['title'] = 'Dashboard'; // Set title for the layout
        return view('dashboard/index', $data);
    }
}
