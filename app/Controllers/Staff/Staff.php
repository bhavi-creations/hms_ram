<?php
namespace App\Controllers\Staff;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;

class Staff extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $staffRoleIds = [3,4,5,6,7,8]; // Staff role IDs

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        $data['staff'] = $this->userModel->whereIn('role_id', $this->staffRoleIds)->findAll();
        return view('staff/list', $data);
    }

    public function register()
    {
        $data['roles'] = $this->roleModel->find($this->staffRoleIds);
        return view('staff/register', $data);
    }

    public function save()
    {
        $postData = $this->request->getPost();
        // Add validation here
        $this->userModel->save($postData);
        return redirect()->to('/staff');
    }

    // Implement edit($id), view($id), delete($id) similarly
}
