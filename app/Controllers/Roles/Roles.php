<?php

namespace App\Controllers\Roles;

use App\Controllers\BaseController;
use App\Models\RoleModel;

class Roles extends BaseController
{
    protected $roleModel;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
    }

    public function index()
    {
        $data['roles'] = $this->roleModel->findAll();
        return view('roles/list', $data);
    }

    public function create()
    {
        return view('roles/create');
    }

    public function save()
    {
        $data = $this->request->getPost();
        if (empty($data['id'])) {
            // Create new role
            if ($this->roleModel->insert($data)) {
                return redirect()->to(base_url('roles'))->with('success', 'Role added successfully!');
            }
        } else {
            // Edit existing role
            if ($this->roleModel->update($data['id'], $data)) {
                return redirect()->to(base_url('roles'))->with('success', 'Role updated successfully!');
            }
        }
        return redirect()->back()->with('error', 'Failed to save role.');
    }

    public function edit($id)
    {
        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->to(base_url('roles'))->with('error', 'Role not found.');
        }
        return view('roles/edit', ['role' => $role]);
    }
    public function delete($id)
    {
        if ($this->roleModel->delete($id)) {
            return redirect()->to(base_url('roles'))->with('success', 'Role deleted successfully!');
        }
        return redirect()->to(base_url('roles'))->with('error', 'Failed to delete role.');
    }
}
