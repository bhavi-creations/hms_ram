<?php
namespace App\Controllers\Staff;

use App\Controllers\BaseController;

class Attendance extends BaseController
{
    public function index()
    {
        // Load a simple attendance list / interface view you will create.
        return view('staff/attendance');
    }

    // Add additional attendance methods if needed
}
