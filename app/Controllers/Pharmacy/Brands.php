<?php

namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use App\Models\Pharmacy\PharmacyBrandModel;

class Brands extends BaseController
{
    protected $brandModel;

    public function __construct()
    {
        $this->brandModel = new PharmacyBrandModel();
    }

    public function index()
    {
        $brands = $this->brandModel->orderBy('brand_name')->findAll();

        return view('pharmacy/brands/index', [
            'title' => 'Manage Brands',
            'brands' => $brands,
        ]);
    }

    public function create()
    {
        return view('pharmacy/brands/create', [
            'title' => 'Add New Brand',
            'validation' => service('validation')
        ]);
    }

    public function store()
    {
        $data = $this->request->getPost();

        // Validation for brand_name only
        $rules = [
            'brand_name' => 'required|trim|regex_match[/^[a-zA-Z0-9 .,&-]+$/]|max_length[255]|is_unique[pharmacy_brands.brand_name]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->brandModel->insert([
            'brand_name' => $data['brand_name'],
        ]);

        return redirect()->to(site_url('pharmacy/brands'))->with('success', 'Brand added successfully.');
    }

    public function edit($id = null)
    {
        $brand = $this->brandModel->find($id);

        if (!$brand) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Brand not found');
        }

        return view('pharmacy/brands/edit', [
            'title' => 'Edit Brand',
            'brand' => $brand,
            'validation' => service('validation'),
        ]);
    }

    public function update($id = null)
    {
        $data = $this->request->getPost();

        $rules = [
            'brand_name' => "required|trim|regex_match[/^[a-zA-Z0-9 .,&-]+$/]|max_length[255]|is_unique[pharmacy_brands.brand_name,id,$id]",
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->brandModel->update($id, [
            'brand_name' => $data['brand_name'],
        ]);

        return redirect()->to(site_url('pharmacy/brands'))->with('success', 'Brand updated successfully');
    }

    public function delete($id = null)
    {
        $brand = $this->brandModel->find($id);

        if (!$brand) {
            return redirect()->to(site_url('pharmacy/brands'))->with('error', 'Brand not found.');
        }

        $this->brandModel->delete($id);

        return redirect()->to(site_url('pharmacy/brands'))->with('success', 'Brand deleted successfully.');
    }
}
