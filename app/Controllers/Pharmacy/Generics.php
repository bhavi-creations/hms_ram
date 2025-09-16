<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use App\Models\Pharmacy\PharmacyGenericModel;

class Generics extends BaseController
{
    protected $genericModel;

    public function __construct()
    {
        $this->genericModel = new PharmacyGenericModel();
    }

    public function index()
    {
        $generics = $this->genericModel->orderBy('generic_name')->findAll();

        return view('pharmacy/generics/index', [
            'title' => 'Manage Generics',
            'generics' => $generics,
        ]);
    }

    public function create()
    {
        return view('pharmacy/generics/create', [
            'title' => 'Add New Generic',
            'validation' => service('validation')
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();

        // Validation for generic_name only
        $rules = [
            'generic_name' => 'required|trim|regex_match[/^[a-zA-Z0-9 .,&-]+$/]|max_length[255]|is_unique[pharmacy_generics.generic_name]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->genericModel->insert([
            'generic_name' => $data['generic_name'],
        ]);

        return redirect()->to(site_url('pharmacy/generics'))->with('success', 'Generic added successfully.');
    }

    public function edit($id = null)
    {
        $generic = $this->genericModel->find($id);

        if (!$generic) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Generic not found');
        }

        return view('pharmacy/generics/edit', [
            'title' => 'Edit Generic',
            'generic' => $generic,
            'validation' => service('validation'),
        ]);
    }

    public function update($id = null)
    {
        $data = $this->request->getPost();

        $rules = [
            'generic_name' => "required|trim|regex_match[/^[a-zA-Z0-9 .,&-]+$/]|max_length[255]|is_unique[pharmacy_generics.generic_name,id,$id]",
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->genericModel->update($id, [
            'generic_name' => $data['generic_name'],
        ]);

        return redirect()->to(site_url('pharmacy/generics'))->with('success', 'Generic updated successfully.');
    }

    public function delete($id = null)
    {
        $generic = $this->genericModel->find($id);

        if (!$generic) {
            return redirect()->to(site_url('pharmacy/generics'))->with('error', 'Generic not found.');
        }

        $this->genericModel->delete($id);

        return redirect()->to(site_url('pharmacy/generics'))->with('success', 'Generic deleted successfully.');
    }
}
