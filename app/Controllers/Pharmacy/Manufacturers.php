<?php namespace App\Controllers\Pharmacy;

use App\Controllers\BaseController;
use App\Models\Pharmacy\PharmacyManufacturerModel;
use App\Models\Pharmacy\PharmacyMedicineModel;

class Manufacturers extends BaseController
{
    // Declare the model property
    protected $manufacturerModel;
    protected $medicineModel;

    /**
     * Constructor to load the necessary models.
     */
    public function __construct()
    {
        $this->manufacturerModel = new PharmacyManufacturerModel();
        $this->medicineModel = new PharmacyMedicineModel(); // For checking dependencies before deleting
    }

    /**
     * Displays a list of all manufacturers.
     */
    public function index()
    {
        $data = [
            'title' => 'Manufacturers',
            'manufacturers' => $this->manufacturerModel->findAll()
        ];
        return view('pharmacy/manufacturers/index', $data);
    }

    /**
     * Displays the form to create a new manufacturer.
     */
    public function create()
    {
        $data = [
            'title' => 'Add New Manufacturer'
        ];
        return view('pharmacy/manufacturers/create', $data);
    }

    /**
     * Handles the form submission for creating a new manufacturer.
     */
    public function store()
    {
        // Define validation rules
        $rules = [
            'name' => 'required|is_unique[pharmacy_manufacturers.name]',
            'contact_person' => 'permit_empty|max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'address' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
        ];

        if ($this->manufacturerModel->insert($data)) {
            return redirect()->to(site_url('pharmacy/manufacturers'))->with('success', 'Manufacturer added successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to add manufacturer.');
        }
    }

    /**
     * Displays the form to edit an existing manufacturer.
     *
     * @param int $id The ID of the manufacturer to edit.
     */
    public function edit($id = null)
    {
        $manufacturer = $this->manufacturerModel->find($id);

        if (!$manufacturer) {
            return redirect()->to(site_url('pharmacy/manufacturers'))->with('error', 'Manufacturer not found.');
        }

        $data = [
            'title' => 'Edit Manufacturer',
            'manufacturer' => $manufacturer
        ];
        return view('pharmacy/manufacturers/edit', $data);
    }

    /**
     * Handles the form submission for updating an existing manufacturer.
     *
     * @param int $id The ID of the manufacturer to update.
     */
    public function update($id = null)
    {
        $manufacturer = $this->manufacturerModel->find($id);

        if (!$manufacturer) {
            return redirect()->to(site_url('pharmacy/manufacturers'))->with('error', 'Manufacturer not found.');
        }

        // Define validation rules for update
        $rules = [
            'name' => 'required|is_unique[pharmacy_manufacturers.name,id,' . $id . ']',
            'contact_person' => 'permit_empty|max_length[100]',
            'phone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'address' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'contact_person' => $this->request->getPost('contact_person'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'address' => $this->request->getPost('address'),
        ];

        if ($this->manufacturerModel->update($id, $data)) {
            return redirect()->to(site_url('pharmacy/manufacturers'))->with('success', 'Manufacturer updated successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to update manufacturer.');
        }
    }

    /**
     * Deletes a manufacturer.
     *
     * @param int $id The ID of the manufacturer to delete.
     */
    public function delete($id = null)
    {
        $manufacturer = $this->manufacturerModel->find($id);

        if (!$manufacturer) {
            return redirect()->to(site_url('pharmacy/manufacturers'))->with('error', 'Manufacturer not found.');
        }

        // Check for associated medicines before deleting
        $associatedMedicines = $this->medicineModel->where('manufacturer_id', $id)->first();
        if ($associatedMedicines) {
            return redirect()->to(site_url('pharmacy/manufacturers'))->with('error', 'Cannot delete manufacturer with associated medicines. Please update or delete the medicines first.');
        }

        if ($this->manufacturerModel->delete($id)) {
            return redirect()->to(site_url('pharmacy/manufacturers'))->with('success', 'Manufacturer deleted successfully.');
        } else {
            return redirect()->to(site_url('pharmacy/manufacturers'))->with('error', 'Failed to delete manufacturer.');
        }
    }
}
