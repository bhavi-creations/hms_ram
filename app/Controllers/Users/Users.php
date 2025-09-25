<?php

namespace App\Controllers\Users;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data['users'] = $this->userModel->findAll();
        return view('users/list', $data);
    }

    public function view($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('users'))->with('error', 'User not found.');
        }
        return view('users/view', ['user' => $user]);
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(base_url('users'))->with('error', 'User not found.');
        }
        return view('users/edit', ['user' => $user]);
    }

    public function save()
    {
        $data = $this->request->getPost();
        // Assume validation here as needed
        if (empty($data['id'])) {
            // New user logic (not covered here)
        } else {
            if ($this->userModel->update($data['id'], $data)) {
                return redirect()->to(base_url('users'))->with('success', 'User updated successfully!');
            }
        }
        return redirect()->back()->with('error', 'Failed to save user.');
    }
    public function register()
    {
        $roleModel = new \App\Models\RoleModel();
        $roles = $roleModel->findAll();

        return view('users/register', ['roles' => $roles]);
    }
}
